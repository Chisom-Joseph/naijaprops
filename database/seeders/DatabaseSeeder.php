<?php

namespace Database\Seeders;

use App\Models\Enquiry;
use App\Models\Property;
use App\Models\PropertyImage;
use App\Models\Report;
use App\Models\SavedProperty;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with realistic Nigerian property data.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        // 1. Seed Users across all roles (admin, owner, agent, buyer)
        $admin = User::updateOrCreate(
            ['email' => 'admin@naijaprops.ng'],
            [
                'full_name' => 'Chukwuma Nwosu (Admin)',
                'password' => $defaultPassword,
                'phone' => '+234 803 111 2233',
                'role' => 'admin',
                'status' => 'active',
            ]
        );

        $owner1 = User::updateOrCreate(
            ['email' => 'adebayo.ogunlesi@naijaprops.ng'],
            [
                'full_name' => 'Chief Adebayo Ogunlesi',
                'password' => $defaultPassword,
                'phone' => '+234 802 345 6789',
                'role' => 'owner',
                'status' => 'active',
            ]
        );

        $owner2 = User::updateOrCreate(
            ['email' => 'tamuno.briggs@naijaprops.ng'],
            [
                'full_name' => 'Engr. Tamuno Briggs',
                'password' => $defaultPassword,
                'phone' => '+234 806 789 0123',
                'role' => 'owner',
                'status' => 'active',
            ]
        );

        $agent1 = User::updateOrCreate(
            ['email' => 'zainab.bello@naijaprops.ng'],
            [
                'full_name' => 'Zainab Bello (Prime Crest Realty)',
                'password' => $defaultPassword,
                'phone' => '+234 809 444 5566',
                'role' => 'agent',
                'status' => 'active',
            ]
        );

        $agent2 = User::updateOrCreate(
            ['email' => 'chinedu.okafor@naijaprops.ng'],
            [
                'full_name' => 'Chinedu Okafor (Eko Luxury Homes)',
                'password' => $defaultPassword,
                'phone' => '+234 813 222 8899',
                'role' => 'agent',
                'status' => 'active',
            ]
        );

        $buyer1 = User::updateOrCreate(
            ['email' => 'amaka.eze@naijaprops.ng'],
            [
                'full_name' => 'Amaka Eze',
                'password' => $defaultPassword,
                'phone' => '+234 703 555 1212',
                'role' => 'buyer',
                'status' => 'active',
            ]
        );

        $buyer2 = User::updateOrCreate(
            ['email' => 'ibrahim.musa@naijaprops.ng'],
            [
                'full_name' => 'Ibrahim Musa',
                'password' => $defaultPassword,
                'phone' => '+234 805 666 7788',
                'role' => 'buyer',
                'status' => 'active',
            ]
        );

        $buyer3 = User::updateOrCreate(
            ['email' => 'opirite.dappa@naijaprops.ng'],
            [
                'full_name' => 'Opirite Dappa',
                'password' => $defaultPassword,
                'phone' => '+234 818 900 3344',
                'role' => 'buyer',
                'status' => 'inactive',
            ]
        );

        // 2. Seed Realistic Nigerian Properties (Lagos, Abuja, Port Harcourt)
        $propertiesData = [
            // LAGOS PROPERTIES
            [
                'owner_id' => $agent2->id,
                'title' => '5 Bedroom Fully Detached Duplex with Swimming Pool & BQ',
                'description' => 'Exquisitely finished 5-bedroom fully detached duplex in a gated estate in Lekki Phase 1. Features a private swimming pool, fitted smart kitchen, cinema room, boys quarters (BQ), stamped concrete floor, 24/7 security, and treated water supply. Governor\'s Consent available.',
                'property_type' => 'duplex',
                'transaction_type' => 'sale',
                'price' => 320000000.00,
                'location' => 'Lekki Phase 1, Lagos',
                'bedrooms' => 5,
                'bathrooms' => 6,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $owner1->id,
                'title' => 'Luxury 3 Bedroom Serviced Apartment with Ocean View',
                'description' => 'Modern 3-bedroom luxury serviced flat in Victoria Island. Comes with 24-hour uninterrupted power supply, elevator, gym, swimming pool, fitted air conditioners, and ample parking space. Ideal for expatriates and corporate executives.',
                'property_type' => 'apartment',
                'transaction_type' => 'rent',
                'price' => 18000000.00,
                'location' => 'Victoria Island, Lagos',
                'bedrooms' => 3,
                'bathrooms' => 4,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $agent2->id,
                'title' => '4 Bedroom Semi-Detached Terrace Duplex in Ikoyi',
                'description' => 'Contemporary 4-bedroom terrace duplex in Old Ikoyi with lush green surroundings, clubhouse access, automated gate, all rooms en-suite, and high-grade security. Perfect blend of serenity and prime Lagos living.',
                'property_type' => 'duplex',
                'transaction_type' => 'sale',
                'price' => 450000000.00,
                'location' => 'Ikoyi, Lagos',
                'bedrooms' => 4,
                'bathrooms' => 5,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $owner1->id,
                'title' => 'Clean 2 Bedroom Flat inside Secured Estate in Ikeja GRA',
                'description' => 'Well-maintained 2-bedroom apartment located in the quiet heart of Ikeja GRA. Easy proximity to Murtala Muhammed Airport, shopping malls, and business districts. Comes with prepaid meter and dedicated parking.',
                'property_type' => 'apartment',
                'transaction_type' => 'rent',
                'price' => 5500000.00,
                'location' => 'Ikeja GRA, Lagos',
                'bedrooms' => 2,
                'bathrooms' => 3,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?auto=format&fit=crop&w=1000&q=80',
                ],
            ],

            // ABUJA PROPERTIES
            [
                'owner_id' => $agent1->id,
                'title' => '6 Bedroom Ambassadorial Mansion with 2 Rooms BQ in Maitama',
                'description' => 'Ultra-luxury 6-bedroom detached mansion on 1,200 sqm in Maitama, Abuja. Features bulletproof doors, private elevator, two living rooms, lush landscaped garden, Gazebo, and C of O title documentation.',
                'property_type' => 'house',
                'transaction_type' => 'sale',
                'price' => 650000000.00,
                'location' => 'Maitama, Abuja',
                'bedrooms' => 6,
                'bathrooms' => 7,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $agent1->id,
                'title' => '4 Bedroom Terrace Duplex in Guzape Hills',
                'description' => 'Brand new 4-bedroom terrace duplex overlooking scenic Guzape hills. Tarred road access, central generator, solar inverter readiness, and spacious en-suite bedrooms with modern wardrobes.',
                'property_type' => 'duplex',
                'transaction_type' => 'rent',
                'price' => 12000000.00,
                'location' => 'Guzape, Abuja',
                'bedrooms' => 4,
                'bathrooms' => 5,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $agent1->id,
                'title' => '3 Bedroom Block of Flats in Wuse 2',
                'description' => 'Tastefully finished 3-bedroom apartment in central Wuse 2, Abuja. Close to banks, supermarkets, and restaurants. Features POP ceiling, water heaters, borehole water, and uniformed security guards.',
                'property_type' => 'apartment',
                'transaction_type' => 'rent',
                'price' => 8500000.00,
                'location' => 'Wuse 2, Abuja',
                'bedrooms' => 3,
                'bathrooms' => 3,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1493809842364-78817add7ffb?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $owner1->id,
                'title' => '1000 SQM FCDA Approved Residential Land in Katampe Extension',
                'description' => 'Prime dry and level 1,000 sqm residential plot in Diplomatic Zone, Katampe Extension, Abuja. Comes with FCDA Right of Occupancy (R of O) and full infrastructure in place.',
                'property_type' => 'land',
                'transaction_type' => 'sale',
                'price' => 95000000.00,
                'location' => 'Katampe Extension, Abuja',
                'bedrooms' => null,
                'bathrooms' => null,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1000&q=80',
                ],
            ],

            // PORT HARCOURT PROPERTIES
            [
                'owner_id' => $owner2->id,
                'title' => '4 Bedroom Executive Detached Duplex in Old GRA Port Harcourt',
                'description' => 'Newly built 4-bedroom detached duplex with security house and boys quarters in Old GRA, Port Harcourt. Federal light zone (20+ hours daily power), serene neighborhood, spacious compound for 5 cars.',
                'property_type' => 'duplex',
                'transaction_type' => 'sale',
                'price' => 145000000.00,
                'location' => 'Old GRA, Port Harcourt',
                'bedrooms' => 4,
                'bathrooms' => 5,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1000&q=80',
                    'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $owner2->id,
                'title' => 'Modern 3 Bedroom Flat in Peter Odili Road',
                'description' => 'Spacious 3-bedroom flat in a gated close off Peter Odili Road, Trans-Amadi, Port Harcourt. All rooms en-suite, visitor\'s toilet, fitted kitchen cabinets, interlock compound, and constant water supply.',
                'property_type' => 'apartment',
                'transaction_type' => 'rent',
                'price' => 3500000.00,
                'location' => 'Peter Odili Road, Port Harcourt',
                'bedrooms' => 3,
                'bathrooms' => 4,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $owner2->id,
                'title' => '2Plots of Dry Land with Deed of Conveyance in Eliozu / G.U. Ake Road',
                'description' => 'Two contiguous plots of 100% dry table land in a built-up estate off G.U. Ake / Eliozu link road, Port Harcourt. Free from community encumbrances with registered Deed of Conveyance.',
                'property_type' => 'land',
                'transaction_type' => 'sale',
                'price' => 42000000.00,
                'location' => 'Eliozu, Port Harcourt',
                'bedrooms' => null,
                'bathrooms' => null,
                'status' => 'available',
                'images' => [
                    'https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
            [
                'owner_id' => $owner2->id,
                'title' => 'Affordable 2 Bedroom Apartment Near UniPort Choba Axis',
                'description' => 'Neat 2-bedroom apartment in a secure compound along Ada-George / Choba axis, Port Harcourt. POP ceiling, tiled floors, steady borehole water, and good road network.',
                'property_type' => 'apartment',
                'transaction_type' => 'rent',
                'price' => 1500000.00,
                'location' => 'Ada-George, Port Harcourt',
                'bedrooms' => 2,
                'bathrooms' => 2,
                'status' => 'rented',
                'images' => [
                    'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1000&q=80',
                ],
            ],
        ];

        $seededProperties = [];

        foreach ($propertiesData as $data) {
            $images = $data['images'];
            unset($data['images']);

            $property = Property::updateOrCreate(
                ['title' => $data['title'], 'location' => $data['location']],
                $data
            );

            // Seed Property Images
            foreach ($images as $imagePath) {
                PropertyImage::firstOrCreate([
                    'property_id' => $property->id,
                    'image_path' => $imagePath,
                ]);
            }

            $seededProperties[] = $property;
        }

        // 3. Seed Saved Properties for Buyers
        if (count($seededProperties) >= 9) {
            SavedProperty::firstOrCreate([
                'user_id' => $buyer1->id,
                'property_id' => $seededProperties[0]->id, // Lekki Duplex (Lagos)
            ]);
            SavedProperty::firstOrCreate([
                'user_id' => $buyer1->id,
                'property_id' => $seededProperties[1]->id, // Victoria Island Apartment (Lagos)
            ]);
            SavedProperty::firstOrCreate([
                'user_id' => $buyer1->id,
                'property_id' => $seededProperties[8]->id, // Old GRA PH Duplex (Port Harcourt)
            ]);
            SavedProperty::firstOrCreate([
                'user_id' => $buyer2->id,
                'property_id' => $seededProperties[4]->id, // Maitama Mansion (Abuja)
            ]);
            SavedProperty::firstOrCreate([
                'user_id' => $buyer2->id,
                'property_id' => $seededProperties[5]->id, // Guzape Terrace (Abuja)
            ]);
        }

        // 4. Seed Enquiries
        if (count($seededProperties) >= 12) {
            Enquiry::firstOrCreate(
                [
                    'property_id' => $seededProperties[0]->id,
                    'user_id' => $buyer1->id,
                ],
                [
                    'message' => 'Good day, is the ₦320M price slightly negotiable for an outright cash buyer? I would like to book an inspection this Saturday in Lekki Phase 1.',
                    'status' => 'pending',
                ]
            );

            Enquiry::firstOrCreate(
                [
                    'property_id' => $seededProperties[9]->id,
                    'user_id' => $buyer1->id,
                ],
                [
                    'message' => 'Hello Engr. Tamuno, please what is the annual service charge for the 3 bedroom flat on Peter Odili Road?',
                    'status' => 'replied',
                ]
            );

            Enquiry::firstOrCreate(
                [
                    'property_id' => $seededProperties[5]->id,
                    'user_id' => $buyer2->id,
                ],
                [
                    'message' => 'Salam Zainab, is the 4 bedroom terrace in Guzape still available for immediate move-in?',
                    'status' => 'pending',
                ]
            );

            Enquiry::firstOrCreate(
                [
                    'property_id' => $seededProperties[11]->id,
                    'user_id' => $buyer2->id,
                ],
                [
                    'message' => 'Good afternoon, I saw that the 2 bedroom apartment on Ada-George / Choba axis has been rented. Please let me know if another flat in the compound becomes available.',
                    'status' => 'closed',
                ]
            );
        }

        // 5. Seed Reports across Lagos, Abuja, and Port Harcourt listings
        if (count($seededProperties) >= 11) {
            Report::firstOrCreate(
                [
                    'property_id' => $seededProperties[3]->id, // Ikeja GRA Flat (Lagos)
                    'reported_by' => $buyer2->id,
                ],
                [
                    'reason' => 'Agent requested an upfront inspection fee before showing the property in Ikeja GRA. Please verify ownership details.',
                    'status' => 'pending',
                ]
            );

            Report::firstOrCreate(
                [
                    'property_id' => $seededProperties[6]->id, // Wuse 2 Block of Flats (Abuja)
                    'reported_by' => $buyer1->id,
                ],
                [
                    'reason' => 'The listed annual rent in Wuse 2, Abuja differs from what the agent quoted on phone (quoted ₦11,000,000 instead of ₦8,500,000).',
                    'status' => 'reviewed',
                ]
            );

            Report::firstOrCreate(
                [
                    'property_id' => $seededProperties[10]->id, // Eliozu Land (Port Harcourt)
                    'reported_by' => $buyer1->id,
                ],
                [
                    'reason' => 'Please verify the registered Deed of Conveyance survey plan number for the 2 plots in Eliozu, Port Harcourt.',
                    'status' => 'resolved',
                ]
            );
        }
    }
}
