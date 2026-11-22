<?php

namespace Database\Seeders;

use App\Models\Courier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        $couriers = [
            [
                'code' => 'JNE',
                'name' => 'JNE',
                'grouping' => 'EXPEDITION',
                'children' => [
                    [
                        'code' => 'JNE-REG',
                        'name' => 'JNE Regular',
                        'children' => [
                            [
                                'code' => 'JNE-REG-STD',
                                'name' => 'JNE Regular Standard',
                            ],
                        ],
                    ],
                    [
                        'code' => 'JNE-YES',
                        'name' => 'JNE YES',
                    ],
                    [
                        'code' => 'JNE-OKE',
                        'name' => 'JNE OKE',
                    ],
                    [
                        'code' => 'JNE-TRK',
                        'name' => 'JNE Trucking',
                    ],
                ],
            ],
            [
                'code' => 'JNT',
                'name' => 'J&T Express',
                'grouping' => 'EXPEDITION',
                'children' => [
                    [
                        'code' => 'JNT-EZ',
                        'name' => 'J&T EZ',
                        'children' => [
                            [
                                'code' => 'JNT-EZ-STD',
                                'name' => 'J&T EZ Standard',
                            ],
                        ],
                    ],
                    [
                        'code' => 'JNT-CARGO',
                        'name' => 'J&T Cargo',
                    ],
                ],
            ],
            [
                'code' => 'SICEPAT',
                'name' => 'SiCepat',
                'grouping' => 'EXPEDITION',
                'children' => [
                    [
                        'code' => 'SICEPAT-REG',
                        'name' => 'SiCepat REG',
                        'children' => [
                            [
                                'code' => 'SICEPAT-REG-STD',
                                'name' => 'SiCepat Regular Standard',
                            ],
                        ],
                    ],
                    [
                        'code' => 'SICEPAT-BEST',
                        'name' => 'SiCepat BEST',
                    ],
                    [
                        'code' => 'SICEPAT-HALU',
                        'name' => 'SiCepat HALU',
                    ],
                ],
            ],
            [
                'code' => 'ANTERAJA',
                'name' => 'AnterAja',
                'grouping' => 'EXPEDITION',
                'children' => [
                    [
                        'code' => 'ANTERAJA-REG',
                        'name' => 'AnterAja Regular',
                        'children' => [
                            [
                                'code' => 'ANTERAJA-REG-STD',
                                'name' => 'AnterAja Regular Standard',
                            ],
                        ],
                    ],
                    [
                        'code' => 'ANTERAJA-SAME',
                        'name' => 'AnterAja Same Day',
                    ],
                    [
                        'code' => 'ANTERAJA-NEXT',
                        'name' => 'AnterAja Next Day',
                    ],
                ],
            ],
            [
                'code' => 'POS',
                'name' => 'POS Indonesia',
                'grouping' => 'EXPEDITION',
                'children' => [
                    [
                        'code' => 'POS-REG',
                        'name' => 'Pos Reguler',
                    ],
                    [
                        'code' => 'POS-EXP',
                        'name' => 'Pos Express',
                    ],
                    [
                        'code' => 'POS-EMS',
                        'name' => 'Pos EMS',
                    ],
                ],
            ],
            [
                'code' => 'INTERNAL',
                'name' => 'Internal Courier',
                'grouping' => 'INTERNAL',
                'children' => [
                    [
                        'code' => 'INTERNAL-DELIVERY',
                        'name' => 'Internal Delivery',
                        'children' => [
                            [
                                'code' => 'INTERNAL-DELIVERY-REG',
                                'name' => 'Regular Delivery',
                            ],
                        ],
                    ],
                    [
                        'code' => 'INTERNAL-PICKUP',
                        'name' => 'Customer Pickup',
                    ],
                ],
            ],
        ];

        foreach ($couriers as $courier) {
            $this->createCourier($courier);
        }
    }

    private function createCourier(
        array $data,
        ?string $parentUuid = null,
        int $level = 1
    ): void {
        $children = $data['children'] ?? [];

        $uuid = Courier::query()
            ->where('code', $data['code'])
            ->value('uuid') ?? (string) Str::uuid();

        $courier = Courier::updateOrCreate(
            ['code' => $data['code']],
            [
                'uuid' => $uuid,
                'parent_uuid' => $parentUuid,
                'name' => $data['name'],
                'grouping' => $data['grouping'] ?? 'EXPEDITION',
                'level' => $level,
            ]
        );

        foreach ($children as $child) {
            $this->createCourier(
                $child,
                $courier->uuid,
                $level + 1
            );
        }
    }
}
