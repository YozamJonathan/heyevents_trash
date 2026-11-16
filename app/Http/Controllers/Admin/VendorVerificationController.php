<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceProviderProfile;
use App\Notifications\VendorKycNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VendorVerificationController extends Controller
{
    /**
     * Display the vendor KYC approvals manager.
     */
    public function index(Request $request): Response
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $pendingVendors = ServiceProviderProfile::with(['user', 'feedbackSender'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $verifiedVendors = ServiceProviderProfile::with(['user', 'verifier', 'feedbackSender'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        $rejectedVendors = ServiceProviderProfile::with(['user', 'verifier', 'feedbackSender'])
            ->where('status', 'rejected')
            ->latest()
            ->get();

        return Inertia::render('admin/VendorApprovals', [
            'pendingVendors' => $pendingVendors,
            'verifiedVendors' => $verifiedVendors,
            'rejectedVendors' => $rejectedVendors,
            'stats' => [
                'pending_count' => $pendingVendors->count(),
                'verified_count' => $verifiedVendors->count(),
                'rejected_count' => $rejectedVendors->count(),
            ],
        ]);
    }
    /**
     * Approve a vendor KYC profile.
     */
    public function approve(Request $request, ServiceProviderProfile $profile): RedirectResponse
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $profile->update([
            'status' => 'approved',
            'verified_at' => now(),
            'verified_by' => $request->user()->id,
            'rejection_reason' => null,
        ]);

        if ($profile->user && ! $profile->user->hasRole('Service Provider')) {
            $profile->user->assignRole('Service Provider');
        }

        if ($profile->user) {
            $profile->user->notify(new VendorKycNotification(
                title: 'KYC Verification Approved!',
                message: "Congratulations! Your business \"{$profile->brand_name}\" has been officially verified by HEY Events Administration. Your vendor storefront and equipment packages are now active.",
                type: 'approved',
                actionUrl: '/dashboard',
                senderName: $request->user()->name
            ));
        }

        return back()->with('success', "Vendor \"{$profile->brand_name}\" has been successfully verified.");
    }

    /**
     * Reject a vendor KYC profile.
     */
    public function reject(Request $request, ServiceProviderProfile $profile): RedirectResponse
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $reason = $validated['reason'] ?? 'Provided documentation or NIDA credentials could not be verified.';

        $profile->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        if ($profile->user) {
            $profile->user->notify(new VendorKycNotification(
                title: 'Action Required: KYC Verification Attention',
                message: "Your application for \"{$profile->brand_name}\" could not be verified. Reason: {$reason}. Please review your details and re-submit.",
                type: 'rejected',
                actionUrl: '/onboarding',
                senderName: $request->user()->name
            ));
        }

        return back()->with('success', "Vendor \"{$profile->brand_name}\" KYC application rejected.");
    }

    /**
     * Revoke a vendor's verification and reset status to pending (for testing or re-evaluation).
     */
    public function revoke(Request $request, ServiceProviderProfile $profile): RedirectResponse
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $profile->update([
            'status' => 'pending',
            'verified_at' => null,
            'verified_by' => null,
            'rejection_reason' => null,
        ]);

        if ($profile->user) {
            $profile->user->notify(new VendorKycNotification(
                title: 'Verification Status Update',
                message: "Verification for \"{$profile->brand_name}\" has been returned to pending review by administration.",
                type: 'feedback',
                actionUrl: '/dashboard',
                senderName: $request->user()->name
            ));
        }

        return back()->with('success', "Verification for \"{$profile->brand_name}\" has been revoked and returned to pending review.");
    }

    /**
     * Send a feedback message / flag mistakes for a vendor.
     */
    public function sendMessage(Request $request, ServiceProviderProfile $profile): RedirectResponse
    {
        if (! $request->user()->can('verify service providers') && ! $request->user()->hasRole('Super Admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'subject' => ['nullable', 'string', 'max:150'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $subject = $validated['subject'] ?: 'Feedback on Your KYC Application';
        $message = $validated['message'];

        $profile->update([
            'admin_feedback' => $message,
            'feedback_sent_at' => now(),
            'feedback_sent_by' => $request->user()->id,
        ]);

        if ($profile->user) {
            $profile->user->notify(new VendorKycNotification(
                title: $subject,
                message: $message,
                type: 'feedback',
                actionUrl: '/dashboard',
                senderName: $request->user()->name
            ));
        }

        return back()->with('success', "Message sent to \"{$profile->brand_name}\". The vendor can view it on their dashboard and notification center.");
    }
}
