<?php

namespace Database\Seeders;

use App\Models\CatalogItem;
use App\Models\ServiceProviderProfile;
use App\Models\User;
use Illuminate\Database\Seeder;

class CatalogItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Link to an approved provider or admin user
        $defaultProvider = ServiceProviderProfile::where('status', 'approved')->first()
            ?? ServiceProviderProfile::first();

        $adminUser = User::role(['Admin', 'Super Admin'])->first();

        $providerId = $defaultProvider?->id;
        $adminId = $adminUser?->id;

        $items = [
            // ==========================================
            // 1. EQUIPMENT & RENTALS (Vifaa & Ukodishaji)
            // ==========================================
            [
                'title' => 'Chiavari & Banquet Chairs (White, Gold & Mahogany)',
                'category' => 'rentals',
                'description' => 'Luxury ergonomic event chairs with thick leatherette cushion seats. Ideal for weddings, gala dinners, and VIP banquets.',
                'client_price' => 2500,
                'provider_payout' => 2000,
                'pricing_unit' => 'per chair',
                'location' => 'Kinondoni, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 600,
                'is_featured' => true,
            ],
            [
                'title' => 'High-Peak Hexagonal Marquee Tent (200 - 500 Guests)',
                'category' => 'rentals',
                'description' => 'Heavy-duty waterproof PVC canopy with galvanized steel frame, interior silk draping, perimeter poles, and setup crew included.',
                'client_price' => 650000,
                'provider_payout' => 520000,
                'pricing_unit' => 'per day',
                'location' => 'Sinza, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 12,
                'is_featured' => true,
            ],
            [
                'title' => 'Silent Heavy-Duty Backup Generator (65 kVA Soundproof)',
                'category' => 'rentals',
                'description' => 'Ultra-quiet mobile diesel generator with automatic changeover switch, full fuel tank, distribution panels, and standby electrical technician.',
                'client_price' => 380000,
                'provider_payout' => 300000,
                'pricing_unit' => 'per event',
                'location' => 'Dar es Salaam & Bagamoyo',
                'image_url' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 6,
                'is_featured' => false,
            ],
            [
                'title' => 'Modular Stage Platform with Velvet Skirting (8m x 6m)',
                'category' => 'rentals',
                'description' => 'Heavy-duty interlocking aluminum stage modules with non-slip flooring, access stairs, side railings, and royal red carpet runway.',
                'client_price' => 500000,
                'provider_payout' => 400000,
                'pricing_unit' => 'per event',
                'location' => 'Mikocheni, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1501386761578-eac5c94b800a?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 8,
                'is_featured' => true,
            ],

            // ==========================================
            // 2. VENUES & HALLS (Kumbi & Viwanja)
            // ==========================================
            [
                'title' => 'Serena Grand Ballroom & Tropical Garden Lawn',
                'category' => 'venues',
                'description' => 'Air-conditioned luxury ballroom accommodating up to 650 banquet guests, attached ocean-breeze gardens, VIP suites, and standby generator.',
                'client_price' => 3500000,
                'provider_payout' => 3000000,
                'pricing_unit' => 'per day',
                'location' => 'City Centre, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 1,
                'is_featured' => true,
            ],
            [
                'title' => 'Oysterbay Oceanfront Beachside Terrace (500 Guests)',
                'category' => 'venues',
                'description' => 'Stunning oceanview manicured lawn facing the Indian Ocean with sunset views, paved parking for 150 vehicles, and private beach frontage.',
                'client_price' => 2800000,
                'provider_payout' => 2400000,
                'pricing_unit' => 'per day',
                'location' => 'Oysterbay, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 1,
                'is_featured' => true,
            ],
            [
                'title' => 'Arusha Mount Meru View Pavilion & Event Lawns',
                'category' => 'venues',
                'description' => 'Cool highland lush gardens with a breathtaking backdrop of Mount Meru, ideal for destination weddings, summits, and executive dinners.',
                'client_price' => 2200000,
                'provider_payout' => 1850000,
                'pricing_unit' => 'per day',
                'location' => 'Arusha Town',
                'image_url' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 1,
                'is_featured' => true,
            ],
            [
                'title' => 'Zanzibar Sunset Coral Stone Banquet Villa',
                'category' => 'venues',
                'description' => 'Historic Swahili-Arabesque open-air coastal palace with coconut palm groves, direct white sand beach access, and royal dinner setup.',
                'client_price' => 3200000,
                'provider_payout' => 2750000,
                'pricing_unit' => 'per day',
                'location' => 'Kendwa / Nungwi, Zanzibar',
                'image_url' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 1,
                'is_featured' => true,
            ],

            // ==========================================
            // 3. CATERING & CAKES (Upishi & Keki)
            // ==========================================
            [
                'title' => 'Royal Swahili & International 3-Course Buffet',
                'category' => 'catering',
                'description' => 'Authentic Zanzibar pilau, biryani ya mbuzi, charcoal-grilled kingfish, butter chicken, fresh salads, dessert stations, and fine china dinnerware.',
                'client_price' => 32000,
                'provider_payout' => 26000,
                'pricing_unit' => 'per plate',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1555244162-803834f70033?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 1200,
                'is_featured' => true,
            ],
            [
                'title' => '5-Tier Fondant Royal Wedding Cake & Dessert Bar',
                'category' => 'catering',
                'description' => 'Bespoke edible art wedding cake with edible gold leaf detailing, white chocolate red velvet tiers, macaron towers, and gourmet dessert station.',
                'client_price' => 850000,
                'provider_payout' => 700000,
                'pricing_unit' => 'per event',
                'location' => 'Masaki, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1535141192574-5d4897c13136?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 5,
                'is_featured' => true,
            ],
            [
                'title' => 'Live Nyama Choma BBQ Experience & Spit-Roast Station',
                'category' => 'catering',
                'description' => 'On-site charcoal grilled mbuzi choma, kuku kienyeji, beef mishkaki, accompanied by freshly tossed kachumbari, pili-pili roast, and ugali/chips.',
                'client_price' => 28000,
                'provider_payout' => 23000,
                'pricing_unit' => 'per plate',
                'location' => 'Dar es Salaam & Coast',
                'image_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 800,
                'is_featured' => false,
            ],
            [
                'title' => 'Artisan Mocktail & Tropical Fresh Juice Bar',
                'category' => 'catering',
                'description' => 'Mobile mixology cocktail bar featuring freshly pressed passion-mint mojitos, tamarind spritz, chilled young coconuts (madafu), and professional baristas.',
                'client_price' => 12000,
                'provider_payout' => 9500,
                'pricing_unit' => 'per guest',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 600,
                'is_featured' => false,
            ],

            // ==========================================
            // 4. SOUND & MEDIA (Muziki & Picha/Sauti)
            // ==========================================
            [
                'title' => 'Line-Array Concert Audio Rig & Professional DJ Setup',
                'category' => 'sound',
                'description' => 'JBL Line Array towers, dual 18-inch subwoofers, 32-channel digital mixer, wireless Shure vocal mics, live DJ equipment, and certified audio engineer.',
                'client_price' => 850000,
                'provider_payout' => 700000,
                'pricing_unit' => 'per event',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 5,
                'is_featured' => true,
            ],
            [
                'title' => 'Cinematic 4K Wedding & Event Film + 4K Drone Aerials',
                'category' => 'sound',
                'description' => 'Multi-camera 4K cinema crew, motorized gimbal stabilizers, FAA-licensed drone pilot, same-day highlight reel, and hard-cover crystal photo albums.',
                'client_price' => 1200000,
                'provider_payout' => 980000,
                'pricing_unit' => 'per event',
                'location' => 'Dar es Salaam, Arusha & Zanzibar',
                'image_url' => 'https://images.unsplash.com/photo-1537633552985-df8429e8048b?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 4,
                'is_featured' => true,
            ],
            [
                'title' => 'P3 Ultra-HD Indoor/Outdoor LED Video Wall (4m x 3m)',
                'category' => 'sound',
                'description' => 'High-brightness seamless LED video screen with NovaStar processor, live multi-camera vision mixer, and on-site graphics playback operator.',
                'client_price' => 1600000,
                'provider_payout' => 1350000,
                'pricing_unit' => 'per day',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 4,
                'is_featured' => true,
            ],
            [
                'title' => 'Intelligent Moving-Head Beams & Stage Ambiance Lights',
                'category' => 'sound',
                'description' => 'DMX computerized moving beam lights, warm wireless ambient uplights, low-lying dry ice fog machine for bridal entrance, and lighting director.',
                'client_price' => 600000,
                'provider_payout' => 490000,
                'pricing_unit' => 'per event',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 6,
                'is_featured' => false,
            ],

            // ==========================================
            // 5. DECOR & STYLING (Mapambo & Maua)
            // ==========================================
            [
                'title' => 'Bespoke Fairytale Floral Arch & Bridal Main Stage Backdrop',
                'category' => 'decor',
                'description' => 'Fresh imported roses, hydrangea, eucalyptus foliage, crystal chandeliers, illuminated glass walkway, and luxury curved bridal sofa.',
                'client_price' => 1500000,
                'provider_payout' => 1250000,
                'pricing_unit' => 'per event',
                'location' => 'Kinondoni, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 4,
                'is_featured' => true,
            ],
            [
                'title' => 'Bohemian Rustic Table Styling & Centerpiece Ensemble',
                'category' => 'decor',
                'description' => 'Gold laser-cut charger plates, linen napkins, dried pampas grass vases, fairy light cloches, gold cutlery, and custom printed menus.',
                'client_price' => 35000,
                'provider_payout' => 28000,
                'pricing_unit' => 'per table',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1478146896981-b80fe463b330?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 50,
                'is_featured' => false,
            ],
            [
                'title' => 'Ceiling Fabric Draping & 2,000 Micro-LED Starlight Canopy',
                'category' => 'decor',
                'description' => 'Cathedral-style sheer ivory fabric draping covering entire ceiling with 2,000 warm white starlight LEDs creating an enchanted night sky.',
                'client_price' => 900000,
                'provider_payout' => 750000,
                'pricing_unit' => 'per event',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1527529482837-4698179dc6ce?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 4,
                'is_featured' => true,
            ],
            [
                'title' => 'Illuminated Floral Tunnel & Mirror Welcome Chart',
                'category' => 'decor',
                'description' => 'Grand 10-meter flower and light archway welcoming guests with a personalized calligraphy mirrored seating chart on a golden easel.',
                'client_price' => 450000,
                'provider_payout' => 370000,
                'pricing_unit' => 'per event',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1520854221256-17451cc331bf?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 3,
                'is_featured' => false,
            ],

            // ==========================================
            // 6. TRANSPORT & CARS (Usafiri & Magari)
            // ==========================================
            [
                'title' => 'Mercedes-Benz S-Class (W222) Luxury Bridal Chauffeur',
                'category' => 'transport',
                'description' => 'Immaculate black Mercedes-Benz S-Class decorated with white silk ribbons, red carpet arrival, fuel included, and suited professional chauffeur.',
                'client_price' => 400000,
                'provider_payout' => 320000,
                'pricing_unit' => 'per day',
                'location' => 'Oysterbay, Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 5,
                'is_featured' => true,
            ],
            [
                'title' => 'Toyota Land Cruiser V8 (ZX) Executive Convoy (2 Units)',
                'category' => 'transport',
                'description' => 'Pair of matching pearl white Land Cruiser V8 luxury SUVs with dark privacy tint, leather interior, and professional convoy drivers for bridal party.',
                'client_price' => 750000,
                'provider_payout' => 620000,
                'pricing_unit' => 'per day',
                'location' => 'Dar es Salaam & Bagamoyo',
                'image_url' => 'https://images.unsplash.com/photo-1533473359331-0135ef1b58bf?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 4,
                'is_featured' => true,
            ],
            [
                'title' => 'Vintage Classic 1968 Jaguar Convertible (Collector Edition)',
                'category' => 'transport',
                'description' => 'Authentic vintage British classic convertible in cream and chrome for unforgettable photoshoots and dramatic grand entrances.',
                'client_price' => 950000,
                'provider_payout' => 780000,
                'pricing_unit' => 'per day',
                'location' => 'Dar es Salaam',
                'image_url' => 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 2,
                'is_featured' => true,
            ],
            [
                'title' => 'VIP Executive Mercedes-Benz Sprinter (22 Leather Seats)',
                'category' => 'transport',
                'description' => 'High-roof luxury passenger shuttle with climate control, reclining captain chairs, USB charging ports, and onboard Wi-Fi for VIP guests.',
                'client_price' => 350000,
                'provider_payout' => 280000,
                'pricing_unit' => 'per day',
                'location' => 'Dar es Salaam & Arusha',
                'image_url' => 'https://images.unsplash.com/photo-1570125909232-eb263c188f7e?auto=format&fit=crop&w=800&q=80',
                'stock_quantity' => 4,
                'is_featured' => false,
            ],
        ];

        foreach ($items as $data) {
            CatalogItem::updateOrCreate(
                ['title' => $data['title']],
                array_merge($data, [
                    'service_provider_profile_id' => $providerId,
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => $adminId,
                    'is_available' => true,
                ])
            );
        }
    }
}
