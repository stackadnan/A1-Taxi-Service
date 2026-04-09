<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        \App\Models\CardFleet::insert([
            [
                'section_key' => 'card-fleet',
                'title' => 'Saloon',
                'subtitle' => 'Affordable',
                'description' => 'Toyota Prius, Ford Mondeo, VW Passat or similar.',
                'image' => 'assets/img/car/saloon.png',
                'passengers' => 4,
                'suitcases' => 2,
                'cabin_bags' => 2,
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'card-fleet',
                'title' => 'First Class',
                'subtitle' => 'Luxury',
                'description' => 'Mercedes E Class, BMW 5 Series or similar.',
                'image' => 'assets/img/car/executive.png',
                'passengers' => 4,
                'suitcases' => 2,
                'cabin_bags' => 2,
                'order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'card-fleet',
                'title' => 'People Carrier', 
                'subtitle' => 'Family',
                'description' => 'VW Sharan, Seat Alhambra, Ford Galaxy or similar.',
                'image' => 'assets/img/car/mpv6.png',
                'passengers' => 6,
                'suitcases' => 3,
                'cabin_bags' => 2,
                'order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'section_key' => 'card-fleet',
                'title' => 'Mini Van 8',
                'subtitle' => 'Comfort',
                'description' => 'Mercedes V Class, VW Transporter or similar.',
                'image' => 'assets/img/car/mpv8.png',
                'passengers' => 8,
                'suitcases' => 8,
                'cabin_bags' => 8,
                'order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \App\Models\CardBlog::insert([
            [
                'title' => 'The best fastest and most powerful road car',
                'author' => 'Mike Hardson',
                'body' => 'Sed ut perspiciatis unde omnis iste natus error sit voluptatemâ€¦',
                'image' => 'assets/img/news/01.jpg',
                'post_date' => now()->subDays(10),
                'comments' => 2,
                'link' => 'news-details',
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Why a city transfer can change your travel day',
                'author' => 'Mike Hardson',
                'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'image' => 'assets/img/news/02.jpg',
                'post_date' => now()->subDays(5),
                'comments' => 2,
                'link' => 'news-details',
                'order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Airport pickup tips for stress-free rides',
                'author' => 'Mike Hardson',
                'body' => 'Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit.',
                'image' => 'assets/img/news/03.jpg',
                'post_date' => now()->subDays(1),
                'comments' => 2,
                'link' => 'news-details',
                'order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $airportPages = [
            ['name' => 'Heathrow Airport', 'slug' => 'heathrow-airport-transfers'],
            ['name' => 'Gatwick Airport', 'slug' => 'gatwick-airport-transfers'],
            ['name' => 'Stansted Airport', 'slug' => 'stansted-airport-transfers'],
            ['name' => 'Luton Airport', 'slug' => 'luton-airport-transfers'],
            ['name' => 'Manchester Airport', 'slug' => 'manchester-airport-transfers'],
            ['name' => 'Birmingham Airport', 'slug' => 'birmingham-airport-transfers'],
            ['name' => 'London City Airport', 'slug' => 'london-city-airport-transfers'],
        ];

        $cityPages = [
            ['name' => 'Aylesbury', 'slug' => 'aylesbury-city-transfers'],
            ['name' => 'Buckingham', 'slug' => 'buckingham-city-transfers'],
            ['name' => 'Coventry', 'slug' => 'coventry-city-transfers'],
            ['name' => 'Baldock', 'slug' => 'baldock-city-transfers'],
            ['name' => 'Bedford', 'slug' => 'bedford-city-transfers'],
            ['name' => 'Cambridge', 'slug' => 'cambridge-city-transfers'],
            ['name' => 'Corby', 'slug' => 'corby-city-transfers'],
            ['name' => 'Dartford', 'slug' => 'dartford-city-transfers'],
            ['name' => 'Daventry', 'slug' => 'daventry-city-transfers'],
            ['name' => 'Dunstable', 'slug' => 'dunstable-city-transfers'],
            ['name' => 'Harpenden', 'slug' => 'harpenden-city-transfers'],
            ['name' => 'East Grinstead', 'slug' => 'east-grinstead-city-transfers'],
            ['name' => 'East Midlands', 'slug' => 'east-midlands-city-transfers'],
        ];

        $createdPageIds = [];
        $urlRows = [];
        foreach ($airportPages as $airportPage) {
            $name = $airportPage['name'];
            $quoteBaseName = trim((string) preg_replace('/\s+Airport$/i', '', $name));
            if ($quoteBaseName === '') {
                $quoteBaseName = $name;
            }

            $page = \App\Models\Page::create([
                'name' => $name,
                'head_title' => 'A1 Airport Cars ',
                'quote_title' => "Reliable {$quoteBaseName} Airport Taxi Service",
                'quote_subtitle' => "{$name} Pickups and Drop-offs",
                'quote_description' => "Book professional {$name} airport taxi transfers to and from all major UK airports. We provide punctual drivers, fixed fares, and comfortable vehicles for every journey.",
                'why_us_title' => 'Why Choose Us',
                'why_us_heading' => "Why Book {$name} Taxi with Us?",
                'why_use_heading' => 'Why You Should Use A1 Airport Cars',
                'main_title' => "Reliable {$name} Transfers",
                'main_description' => "<strong>A1 Airport Cars</strong> provides professional and reliable {$name} transfer services. Whether you are arriving in the city or heading to catch a flight, our service ensures a smooth and comfortable journey.",
                'left_title' => "Professional {$name} Drivers",
                'left_description' => "Our experienced drivers are fully licensed and highly familiar with {$name} terminals, pickup points, and surrounding routes. They monitor traffic and journeys to ensure timely pickups.",
                'right_title' => "Comfortable Vehicles for {$name} Transfers",
                'right_description' => 'From affordable saloon cars for individuals to executive vehicles and spacious MPVs for families and groups, our fleet is maintained to high standards with ample luggage space.',
                'bottom_title' => "Simple Booking for {$name} Transfers",
                'bottom_description' => "Booking your {$name} transfer with A1 Airport Cars is quick and easy. Reserve your transfer online in minutes or contact our support team for assistance.",
            ]);

            $createdPageIds[] = $page->id;

            $urlRows[] = [
                'page_id' => $page->id,
                'group_slug' => 'airport-transfers',
                'slug' => $airportPage['slug'],
                'date' => now()->toDateString(),
                'meta' => json_encode(['source' => 'seed', 'category' => 'airport']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        foreach ($cityPages as $cityPage) {
            $name = $cityPage['name'];

            $page = \App\Models\Page::create([
                'name' => $name,
                'head_title' => 'A1 Airport Cars ',
                'quote_title' => "Reliable {$name} Airport Taxi Service",
                'quote_subtitle' => "{$name} Airport Transfer Service",
                'quote_description' => "Book professional {$name} airport taxi transfers to and from all major UK airports. We provide punctual drivers, fixed fares, and comfortable vehicles for every journey.",
                'why_us_title' => 'Why Choose Us',
                'why_us_heading' => "Why Book {$name} Taxi with Us?",
                'why_use_heading' => 'Why You Should Use A1 Airport Cars',
                'main_title' => "Reliable {$name} City Transfers",
                'main_description' => "<strong>A1 Airport Cars</strong> provides reliable city transfer services in {$name}. Whether you are travelling for business or leisure, our service ensures a smooth and comfortable journey.",
                'left_title' => "Professional {$name} Transfer Drivers",
                'left_description' => "Our experienced drivers are fully licensed and familiar with {$name} routes and surrounding areas, helping you travel safely and on time.",
                'right_title' => "Comfortable Vehicles for {$name} City Transfers",
                'right_description' => 'From affordable saloon cars for individuals to executive vehicles and spacious MPVs for families and groups, our fleet is maintained to high standards with ample luggage space.',
                'bottom_title' => "Simple Booking for {$name} City Transfers",
                'bottom_description' => "Booking your {$name} city transfer with A1 Airport Cars is quick and easy. Reserve online in minutes or contact our support team for assistance.",
            ]);

            $createdPageIds[] = $page->id;

            $urlRows[] = [
                'page_id' => $page->id,
                'group_slug' => 'city-transfers',
                'slug' => $cityPage['slug'],
                'date' => now()->toDateString(),
                'meta' => json_encode(['source' => 'seed', 'category' => 'city']),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        \App\Models\Url::insert($urlRows);

        $partialRows = [];

        foreach ($createdPageIds as $createdPageId) {
            $partialRows[] = [
                'page_id' => $createdPageId,
                'head' => true,
                'preloader' => true,
                'scroll_up' => true,
                'offcanvas' => true,
                'header' => true,
                'breadcrumb' => true,
                'quotes' => true,
                'testimonials' => true,
                'why_us' => true,
                'card_fleet' => true,
                'steps' => true,
                'card_blog' => true,
                'faq' => true,
                'footer' => true,
                'script' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($partialRows)) {
            \App\Models\PagePartial::insert($partialRows);
        }

        \App\Models\Header::insert([
            [
                'section_key' => 'header',
                'top_email' => 'info@example.com',
                'top_address' => '88 Broklyn Golden Street. New York',
                'top_links' => json_encode([
                    ['label' => 'Manage Bookings', 'url' => 'contact'],
                    ['label' => 'Support', 'url' => 'contact'],
                    ['label' => 'Contact', 'url' => 'contact'],
                ]),
                'social_links' => json_encode([
                    ['icon' => 'fab fa-facebook-f', 'url' => '#'],
                    ['icon' => 'fab fa-twitter', 'url' => '#'],
                    ['icon' => 'fa-brands fa-linkedin-in', 'url' => '#'],
                    ['icon' => 'fa-brands fa-youtube', 'url' => '#'],
                ]),
                'logo_light' => 'assets/img/logo/white-logo-2.png',
                'logo_dark' => 'assets/img/logo/black-logo.png',
                'phone_label' => 'Call Anytime',
                'phone_number' => '+92 (8800) - 9850',
                'button_text' => 'Manage Bookings',
                'button_link' => 'car-details',
                'airport_links' => json_encode([
                    ['label' => 'Heathrow Airport Transfers', 'url' => '/airport-transfers/heathrow-airport-transfers'],
                    ['label' => 'Gatwick Airport Transfers', 'url' => '/airport-transfers/gatwick-airport-transfers'],
                    ['label' => 'Stansted Airport Transfers', 'url' => '/airport-transfers/stansted-airport-transfers'],
                    ['label' => 'Luton Airport Transfers', 'url' => '/airport-transfers/luton-airport-transfers'],
                    ['label' => 'Manchester Airport Transfers', 'url' => '/airport-transfers/manchester-airport-transfers'],
                    ['label' => 'Birmingham Airport Transfers', 'url' => '/airport-transfers/birmingham-airport-transfers'],
                    ['label' => 'London City Airport Transfers', 'url' => '/airport-transfers/london-city-airport-transfers'],
                ]),
                'city_links' => json_encode([
                    ['label' => 'Aylesbury', 'url' => '/city-transfers/aylesbury-city-transfers'],
                    ['label' => 'Buckingham', 'url' => '/city-transfers/buckingham-city-transfers'],
                    ['label' => 'Coventry', 'url' => '/city-transfers/coventry-city-transfers'],
                    ['label' => 'Baldock', 'url' => '/city-transfers/baldock-city-transfers'],
                    ['label' => 'Bedford', 'url' => '/city-transfers/bedford-city-transfers'],
                    ['label' => 'Cambridge', 'url' => '/city-transfers/cambridge-city-transfers'],
                    ['label' => 'Corby', 'url' => '/city-transfers/corby-city-transfers'],
                    ['label' => 'Dartford', 'url' => '/city-transfers/dartford-city-transfers'],
                    ['label' => 'Daventry', 'url' => '/city-transfers/daventry-city-transfers'],
                    ['label' => 'Dunstable', 'url' => '/city-transfers/dunstable-city-transfers'],
                    ['label' => 'Harpenden', 'url' => '/city-transfers/harpenden-city-transfers'],
                    ['label' => 'East Grinstead', 'url' => '/city-transfers/east-grinstead-city-transfers'],
                    ['label' => 'East Midlands', 'url' => '/city-transfers/east-midlands-city-transfers'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \App\Models\Offcanvas::insert([
            [
                'section_key' => 'offcanvas',
                'logo' => 'assets/img/logo/black-logo.png',
                'address' => '960 Capability Green, LU1 3PE Luton',
                'email' => 'info@a1airportcars.co.uk',
                'phone' => '(+44) - 1582 - 801 - 611',
                'button_text' => 'Manage My Booking',
                'button_link' => 'contact',
                'social_links' => json_encode([
                    ['icon' => 'fab fa-facebook-f', 'link' => '#'],
                    ['icon' => 'fab fa-twitter', 'link' => '#'],
                    ['icon' => 'fab fa-youtube', 'link' => '#'],
                    ['icon' => 'fab fa-linkedin-in', 'link' => '#'],
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \App\Models\Footer::insert([
            [
                'section_key' => 'footer',
                'logo' => 'assets/img/logo/white-logo-2.png',
                'tagline' => 'Your go to option for reliable Airport Transfers',
                'contact_address' => '960 Capability Green, LU1 3PE Luton, United Kingdom',
                'contact_email' => 'info@a1airportcars.co.uk',
                'contact_phone' => '(+44) - 1582 - 801 - 611',
                'links' => json_encode([
                    ['label' => 'FAQ', 'url' => 'about'],
                    ['label' => 'Terms & Conditions', 'url' => 'car-details'],
                    ['label' => 'Refund Policy', 'url' => 'news-details'],
                    ['label' => 'Privacy Policy', 'url' => 'gallery'],
                    ['label' => 'Contact', 'url' => 'contact'],
                ]),
                'airports' => json_encode([
                    ['label' => 'Heathrow Airport Transfers', 'url' => '/airport-transfers/heathrow-airport-transfers'],
                    ['label' => 'Gatwick Airport Transfers', 'url' => '/airport-transfers/gatwick-airport-transfers'],
                    ['label' => 'Stansted Airport Transfers', 'url' => '/airport-transfers/stansted-airport-transfers'],
                    ['label' => 'Luton Airport Transfers', 'url' => '/airport-transfers/luton-airport-transfers'],
                    ['label' => 'Manchester Airport Transfers', 'url' => '/airport-transfers/manchester-airport-transfers'],
                    ['label' => 'Birmingham Airport Transfers', 'url' => '/airport-transfers/birmingham-airport-transfers'],
                    ['label' => 'London City Airport Transfers', 'url' => '/airport-transfers/london-city-airport-transfers'],
                ]),
                'cities' => json_encode(['Aylesbury','Buckingham','Coventry','Baldock','Bedford','Cambridge','Corby','Dartford','Daventry','Dunstable','Harpenden','East Grinstead','East Midlands']),
                'copyright' => '&copy; Copyright '.date('Y').' A1 Airport Cars | Powered by <a href="./">BXS</a>',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $faqRows = [];
        $pages = \App\Models\Page::select('id', 'name')->get();

        foreach ($pages as $page) {
            $pageName = trim((string) $page->name);
            if ($pageName === '') {
                $pageName = 'your transfer';
            }

            $faqRows[] = [
                'page_id' => $page->id,
                'question' => "How does {$pageName} pickup work?",
                'answer' => "Once your {$pageName} booking is confirmed, our driver monitors your journey and arrives on time with a meet-and-greet service.",
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $faqRows[] = [
                'page_id' => $page->id,
                'question' => 'Can I cancel my booking?',
                'answer' => 'Yes! Free cancellation is available up to 12 hours before pickup, with terms and conditions.',
                'order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $faqRows[] = [
                'page_id' => $page->id,
                'question' => 'Do you offer flight tracking?',
                'answer' => 'Yes. We track flight status continuously and adjust pickup times for delays.',
                'order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        if (!empty($faqRows)) {
            \App\Models\FaqItem::insert($faqRows);
        }

        \App\Models\Breadcrumb::insert([
            [
                'page_key' => 'home',
                'img' => 'assets/img/breadcrumb-banner.png',
                'title' => 'Home',
                'title2' => 'Welcome',
                'subtitle' => 'Find the best airport transfer services',
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_key' => 'about',
                'img' => 'assets/img/breadcrumb-banner.png',
                'title' => 'Home',
                'title2' => 'About Us',
                'subtitle' => 'Learn more about our mission and team',
                'order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'page_key' => 'contact',
                'img' => 'assets/img/breadcrumb-banner.png',
                'title' => 'Home',
                'title2' => 'Contact',
                'subtitle' => 'Get in touch anytime',
                'order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \App\Models\StepItem::insert([
            [
                'title' => 'Get Quotes',
                'link' => 'car-details',
                'icon1' => 'assets/img/how-work/icon-1.png',
                'icon2' => 'assets/img/how-work/icon-11.png',
                'description' => 'Enter your pickup and drop-off locations, travel date, and time to get instant quotes for your airport transfer.',
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Select Vehicle',
                'link' => 'car-details',
                'icon1' => 'assets/img/how-work/icon-2.png',
                'icon2' => 'assets/img/how-work/icon-22.png',
                'description' => 'Choose the perfect car for your journey, whether it\'s a saloon, executive, MPV, or mini-van for extra comfort.',
                'order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Few Details',
                'link' => 'car-details',
                'icon1' => 'assets/img/how-work/icon-3.png',
                'icon2' => 'assets/img/how-work/icon-33.png',
                'description' => 'Fill in your contact info and flight details so our driver knows exactly when and where to pick you up.',
                'order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Book & Relax',
                'link' => 'car-details',
                'icon1' => 'assets/img/how-work/icon-4.png',
                'icon2' => 'assets/img/how-work/icon-44.png',
                'description' => 'Confirm your booking in seconds and relax knowing a professional driver will be ready to take you to your destination safely.',
                'order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \App\Models\FeatureBenefit::insert([
            [
                'title' => '24/7\nCustomer Support',
                'description' => 'Our support team is always available to assist with bookings, flight updates, or any special requests.',
                'icon' => 'assets/img/feature-benefit/icon-1.png',
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Free\nFlight Tracking',
                'description' => 'Provide your flight details and we\'ll track your arrival to ensure punctual pickup and drop-off.',
                'icon' => 'assets/img/feature-benefit/icon-2.png',
                'order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Drivers\nLicensed & CRB Checked',
                'description' => 'All our drivers are fully licensed and CRB-checked for your safety and peace of mind during transfers.',
                'icon' => 'assets/img/feature-benefit/icon-3.png',
                'order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Free\nCancellation',
                'description' => 'Change or cancel your booking free of charge up to 12 hours before your scheduled pickup.',
                'icon' => 'assets/img/feature-benefit/icon-6.png',
                'order' => 40,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \App\Models\WhyUs::insert([
            [
                'section_key' => 'why-us',
                'section_title' => 'Why Choose Us',
                'section_subtitle' => 'Why Book an Airport Taxi with Us?',
                'left_items' => json_encode([
                    'Save up to 40% compared to other taxi fares',
                    'The price you see is the price you pay',
                    'No hidden fees or surprise charges',
                    'Free 45 minutes airport waiting time',
                    'Real-time flight monitoring for timely pickups',
                    'Guaranteed airport pickup service',
                    'More affordable than traditional black cabs',
                    'No extra charge if your flight is delayed',
                    'Free cancellation (terms & conditions apply)',
                    '24/7 customer support for assistance anytime',
                ]),
                'right_items' => json_encode([
                    '<strong>Prices:</strong> Up to 40% cheaper than many airport taxis',
                    '<strong>Vehicles:</strong> Saloon, Executive, MPV, and 8-Seater Minivans',
                    '<strong>Drivers:</strong> Fully licensed and background checked',
                    '<strong>Cancellation:</strong> No cancellation charge*',
                    '<strong>Baby Seats:</strong> Available free of charge (subject to availability)',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        \App\Models\Testimonial::insert([
            [
                'author' => 'Emily Carter',
                'company' => 'Google',
                'message' => 'The driver was punctual and very professional. My airport transfer was smooth and stress-free. Highly recommended!',
                'rating' => 5,
                'order' => 10,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author' => 'David Thompson',
                'company' => 'Google',
                'message' => 'A1 Airport Cars made my business trip hassle-free. The car was clean, and the driver was courteous. I’ll definitely book again.',
                'rating' => 5,
                'order' => 20,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'author' => 'Sophia Williams',
                'company' => 'Google',
                'message' => 'Fantastic service! The driver tracked my delayed flight and adjusted pickup accordingly. Very reliable and friendly.',
                'rating' => 5,
                'order' => 30,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
