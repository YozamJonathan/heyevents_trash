<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ItemApprovalController extends Controller
{
    /**
     * Display the items moderation portal for tools, equipment, and services.
     */
    public function index(): Response
    {
        $pendingItems = CatalogItem::with(['provider.user'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        $approvedItems = CatalogItem::with(['provider.user', 'approver'])
            ->where('status', 'approved')
            ->latest()
            ->get();

        $rejectedItems = CatalogItem::with(['provider.user', 'approver'])
            ->where('status', 'rejected')
            ->latest()
            ->get();

        return Inertia::render('admin/ItemApprovals', [
            'pendingItems' => $pendingItems,
            'approvedItems' => $approvedItems,
            'rejectedItems' => $rejectedItems,
        ]);
    }

    /**
     * Approve an item and publish it to the Explore catalog.
     */
    public function approve(Request $request, CatalogItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'client_price' => ['nullable', 'numeric', 'min:0'],
            'is_featured' => ['nullable', 'boolean'],
        ]);

        $item->update([
            'status' => 'approved',
            'is_available' => true,
            'approved_at' => now(),
            'approved_by' => $request->user()->id,
            'client_price' => $validated['client_price'] ?? $item->client_price,
            'is_featured' => $validated['is_featured'] ?? $item->is_featured,
            'admin_notes' => null,
        ]);

        session()->flash('toast', [
            'type' => 'success',
            'message' => 'Item "'.$item->title.'" has been verified and published to Explore catalog.',
        ]);

        return back();
    }

    /**
     * Reject an item or request modifications from the provider.
     */
    public function reject(Request $request, CatalogItem $item): RedirectResponse
    {
        $validated = $request->validate([
            'admin_notes' => ['required', 'string', 'max:1000'],
        ]);

        $item->update([
            'status' => 'rejected',
            'is_available' => false,
            'admin_notes' => $validated['admin_notes'],
            'approved_by' => $request->user()->id,
        ]);

        session()->flash('toast', [
            'type' => 'error',
            'message' => 'Item rejected and feedback sent to provider.',
        ]);

        return back();
    }
}
