import { ref, computed } from 'vue';
import {
    Heart,
    Briefcase,
    Music,
    Users2,
    CheckCircle2,
    DollarSign,
    Smartphone,
    ShieldCheck,
    Radio,
    WifiOff,
    CreditCard,
    QrCode,
    BarChart3,
    Compass,
    Ticket,
    Send,
    UserCheck,
    Layers,
} from '@lucide/vue';

export interface EventHub {
    id: string;
    title: string;
    badge: string;
    icon: any;
    eventTypes: string[];
    highlights: string[];
    previewTitle: string;
    previewMetrics: { label: string; value: string }[];
    sampleAction: string;
}

export interface PipelinePhase {
    id: string;
    step: string;
    title: string;
    subtitle: string;
    icon: any;
    highlights: string[];
    moduleType: 'budget' | 'ticket' | 'scanner' | 'analytics';
}

export function useLandingData() {
    // 1. Event Hubs (The 4 Functional Hubs covering all 17 event types)
    const activeHubId = ref('social');

    const eventHubs: EventHub[] = [
        {
            id: 'social',
            title: 'Social & Celebrations',
            badge: 'Weddings & Milestones',
            icon: Heart,
            eventTypes: ['Weddings', 'Birthdays', 'Graduations', 'Private Parties', 'Funerals & Memorials'],
            highlights: [
                'Itemized budget planner & contribution tracking',
                'Custom digital RSVP links with dietary preferences',
                'Interactive table & seating arrangement builder',
                'Vendor assignment & milestone payments',
            ],
            previewTitle: 'Amani & Zawadi Wedding Gala',
            previewMetrics: [
                { label: 'RSVPs Confirmed', value: '380 / 400' },
                { label: 'Budget Funded', value: '94%' },
            ],
            sampleAction: 'Generate Guest Seating Chart',
        },
        {
            id: 'corporate',
            title: 'Corporate & Business',
            badge: 'Conferences & Summits',
            icon: Briefcase,
            eventTypes: ['Conferences', 'Seminars', 'Workshops', 'Product Launches', 'Networking Mixers'],
            highlights: [
                'Multi-track session agendas & speaker directory',
                'Automated badge generation & QR pass distribution',
                'Sponsor lead retrieval & booth assignment',
                'Executive attendance & ROI analytics',
            ],
            previewTitle: 'East Africa Tech Summit 2026',
            previewMetrics: [
                { label: 'Delegates Registered', value: '2,450' },
                { label: 'Tracks / Keynotes', value: '8 Tracks' },
            ],
            sampleAction: 'Publish Keynote Agenda',
        },
        {
            id: 'entertainment',
            title: 'Entertainment & Large Scale',
            badge: 'Festivals & Stadiums',
            icon: Music,
            eventTypes: ['Concerts', 'Festivals & Carnivals', 'Sports Tournaments', 'Exhibitions & Expos'],
            highlights: [
                'Tiered early-bird, VIP & backstage passes',
                'High-throughput mobile gate scanner app (<0.5s entry)',
                'Real-time gate capacity & entrance heatmaps',
                'Cashless wristband & concession support',
            ],
            previewTitle: 'Safari Beats Summer Festival',
            previewMetrics: [
                { label: 'Tickets Sold', value: '8,920' },
                { label: 'Peak Gate Flow', value: '120/min' },
            ],
            sampleAction: 'Launch Tier 2 Early Bird',
        },
        {
            id: 'community',
            title: 'Community & Non-Profit',
            badge: 'Galas & Ministries',
            icon: Users2,
            eventTypes: ['Fundraising Galas', 'Religious Events & Retreats'],
            highlights: [
                'Live donation & donor tier progress tracking',
                'Broadcast SMS & WhatsApp announcements',
                'Volunteer team shift & station coordination',
                'Pledge reconciliation & tax receipting',
            ],
            previewTitle: 'Education Foundation Annual Gala',
            previewMetrics: [
                { label: 'Pledges Raised', value: '$84,500' },
                { label: 'Volunteers Active', value: '45 Crew' },
            ],
            sampleAction: 'Broadcast Event Updates',
        },
    ];

    const currentHub = computed(() => eventHubs.find((h) => h.id === activeHubId.value) || eventHubs[0]);

    // 2. Actionable Workflow Pipeline (4 Modules)
    const activePipelinePhase = ref<string>('plan');

    const pipelinePhases: PipelinePhase[] = [
        {
            id: 'plan',
            step: '01 Plan',
            title: 'Vendor Directory & Budget Estimator',
            subtitle: 'Real-time itemized budget allocation vs. actual spend with vendor contract milestones.',
            icon: Compass,
            highlights: ['Floorplan & Seating Builder', 'Automated vendor payment schedules', 'Variance alerts on overspending'],
            moduleType: 'budget',
        },
        {
            id: 'organize',
            step: '02 Organize',
            title: 'Ticket Tiering & Custom Invites',
            subtitle: 'Configure VIP, Early Bird, Table packages and custom RSVP waivers in seconds.',
            icon: Ticket,
            highlights: ['Dynamic pricing windows', 'Custom form questions & waivers', 'Instant Apple/Google Wallet sync'],
            moduleType: 'ticket',
        },
        {
            id: 'execute',
            step: '03 Execute',
            title: 'Multi-Usher QR Scanner & Gate App',
            subtitle: 'Sub-second mobile entry verification with live multi-door sync and offline resilience.',
            icon: QrCode,
            highlights: ['<0.5s barcode scanning', 'VIP arrival push alerts', 'Zero-stall offline fallback'],
            moduleType: 'scanner',
        },
        {
            id: 'evaluate',
            step: '04 Evaluate',
            title: 'Live Analytics & Revenue Reports',
            subtitle: 'Comprehensive financial reconciliation, attendance heatmaps, and instant CSV/PDF audits.',
            icon: BarChart3,
            highlights: ['Real-time revenue audits', 'Drop-off & check-in heatmaps', 'Automated post-event surveys'],
            moduleType: 'analytics',
        },
    ];

    const currentPipeline = computed(() => pipelinePhases.find((p) => p.id === activePipelinePhase.value) || pipelinePhases[0]);

    // 3. Enterprise & Team Readiness Features
    const enterpriseFeatures = [
        {
            title: 'Granular Team Roles',
            description: 'Assign tailored permissions for Lead Planners, Venue Coordinators, Vendors, and Gate Ushers with isolated audit trails.',
            icon: UserCheck,
            badge: 'Access Control',
        },
        {
            title: 'Mobile Money & Card Payments',
            description: 'Native instant checkout with M-Pesa, Tigo Pesa, Airtel Money, Visa, and Mastercard with real-time payout reconciliation.',
            icon: CreditCard,
            badge: 'Local Payments',
        },
        {
            title: 'Automated SMS & WhatsApp',
            description: 'Direct dispatch of QR tickets, venue directions, and real-time schedule updates directly to attendees’ WhatsApp and SMS.',
            icon: Send,
            badge: 'Omnichannel Alerts',
        },
        {
            title: 'Offline Gate Resilience',
            description: 'Gates never freeze when internet drops. Scan tickets continuously offline with instant background cloud sync when reconnected.',
            icon: WifiOff,
            badge: '100% Gate Uptime',
        },
    ];

    return {
        activeHubId,
        eventHubs,
        currentHub,
        activePipelinePhase,
        pipelinePhases,
        currentPipeline,
        enterpriseFeatures,
    };
}
