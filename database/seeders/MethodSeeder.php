<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Method;

class MethodSeeder extends Seeder
{
    public function run(): void
    {
        $methods = [
            [
                'name' => 'pH Analysis',
                'version' => '1.0',
                'status' => 'PUBLISHED',
                'schema_json' => [
                    'fields' => [
                        [
                            'key' => 'ph_value',
                            'label' => 'pH Value',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'pH',
                            'min' => 0,
                            'max' => 14,
                            'description' => 'pH measurement of the sample'
                        ],
                        [
                            'key' => 'temperature',
                            'label' => 'Temperature',
                            'type' => 'number',
                            'required' => true,
                            'unit' => '°C',
                            'min' => 0,
                            'max' => 100,
                            'description' => 'Temperature during measurement'
                        ],
                        [
                            'key' => 'conductivity',
                            'label' => 'Conductivity',
                            'type' => 'number',
                            'required' => false,
                            'unit' => 'µS/cm',
                            'description' => 'Electrical conductivity measurement'
                        ]
                    ]
                ],
                'limits_json' => [
                    'ph_value' => ['min' => 6.5, 'max' => 8.5],
                    'temperature' => ['min' => 15, 'max' => 30],
                ],
                'created_by' => 1,
            ],
            [
                'name' => 'Microbial Analysis',
                'version' => '1.0',
                'status' => 'PUBLISHED',
                'schema_json' => [
                    'fields' => [
                        [
                            'key' => 'total_count',
                            'label' => 'Total Count',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'CFU/mL',
                            'min' => 0,
                            'description' => 'Total bacterial count'
                        ],
                        [
                            'key' => 'coliform',
                            'label' => 'Coliform',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'CFU/mL',
                            'min' => 0,
                            'description' => 'Coliform bacteria count'
                        ],
                        [
                            'key' => 'e_coli',
                            'label' => 'E. coli',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'CFU/mL',
                            'min' => 0,
                            'description' => 'E. coli bacteria count'
                        ],
                        [
                            'key' => 'yeast_mold',
                            'label' => 'Yeast & Mold',
                            'type' => 'number',
                            'required' => false,
                            'unit' => 'CFU/mL',
                            'min' => 0,
                            'description' => 'Yeast and mold count'
                        ]
                    ]
                ],
                'limits_json' => [
                    'total_count' => ['max' => 10000],
                    'coliform' => ['max' => 100],
                    'e_coli' => ['max' => 0],
                ],
                'created_by' => 1,
            ],
            [
                'name' => 'Chemical Composition',
                'version' => '1.0',
                'status' => 'PUBLISHED',
                'schema_json' => [
                    'fields' => [
                        [
                            'key' => 'protein',
                            'label' => 'Protein',
                            'type' => 'number',
                            'required' => true,
                            'unit' => '%',
                            'min' => 0,
                            'max' => 100,
                            'description' => 'Protein content percentage'
                        ],
                        [
                            'key' => 'fat',
                            'label' => 'Fat',
                            'type' => 'number',
                            'required' => true,
                            'unit' => '%',
                            'min' => 0,
                            'max' => 100,
                            'description' => 'Fat content percentage'
                        ],
                        [
                            'key' => 'moisture',
                            'label' => 'Moisture',
                            'type' => 'number',
                            'required' => true,
                            'unit' => '%',
                            'min' => 0,
                            'max' => 100,
                            'description' => 'Moisture content percentage'
                        ],
                        [
                            'key' => 'ash',
                            'label' => 'Ash',
                            'type' => 'number',
                            'required' => false,
                            'unit' => '%',
                            'min' => 0,
                            'max' => 100,
                            'description' => 'Ash content percentage'
                        ]
                    ]
                ],
                'limits_json' => [
                    'protein' => ['min' => 10, 'max' => 90],
                    'fat' => ['min' => 0.1, 'max' => 50],
                    'moisture' => ['min' => 5, 'max' => 80],
                ],
                'created_by' => 1,
            ],
            [
                'name' => 'Heavy Metals',
                'version' => '1.0',
                'status' => 'PUBLISHED',
                'schema_json' => [
                    'fields' => [
                        [
                            'key' => 'lead',
                            'label' => 'Lead (Pb)',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'mg/kg',
                            'min' => 0,
                            'description' => 'Lead concentration'
                        ],
                        [
                            'key' => 'cadmium',
                            'label' => 'Cadmium (Cd)',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'mg/kg',
                            'min' => 0,
                            'description' => 'Cadmium concentration'
                        ],
                        [
                            'key' => 'mercury',
                            'label' => 'Mercury (Hg)',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'mg/kg',
                            'min' => 0,
                            'description' => 'Mercury concentration'
                        ],
                        [
                            'key' => 'arsenic',
                            'label' => 'Arsenic (As)',
                            'type' => 'number',
                            'required' => true,
                            'unit' => 'mg/kg',
                            'min' => 0,
                            'description' => 'Arsenic concentration'
                        ]
                    ]
                ],
                'limits_json' => [
                    'lead' => ['max' => 0.1],
                    'cadmium' => ['max' => 0.05],
                    'mercury' => ['max' => 0.02],
                    'arsenic' => ['max' => 0.1],
                ],
                'created_by' => 1,
            ]
        ];

        foreach ($methods as $method) {
            Method::create($method);
        }
    }
}
