<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Method;

class MethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Method::create([
        'name'=>'pH Test',
        'base_method_id'=>1,
        'version'=>1,
        'status'=>'PUBLISHED',
        'schema_json'=>[
            'fields'=>[
                ['key'=>'ph','label'=>'pH','type'=>'number','unit'=>'pH','required'=>true]
            ]
        ],
        'limits_json'=>[
            'ph'=>['min'=>6.5,'max'=>7.5]
        ],
        'created_by'=>1
        ]);

        Method::create([
            'name'=>'Colony Count',
            'base_method_id'=>2,
            'version'=>1,
            'status'=>'PUBLISHED',
            'schema_json'=>[
                'fields'=>[
                    ['key'=>'count','label'=>'Colony count','type'=>'number','unit'=>'CFU/ml','required'=>true]
                ]
            ],
            'limits_json'=>[
                'count'=>['min'=>0,'max'=>10000]
            ],
            'created_by'=>1
        ]);

        Method::create([
            'name'=>'Visual Check',
            'base_method_id'=>3,
            'version'=>1,
            'status'=>'PUBLISHED',
            'schema_json'=>[
                'fields'=>[
                    ['key'=>'result','label'=>'Visual check','type'=>'select','options'=>['OK','SUSPECT','FAIL'],'required'=>true]
                ]
            ],
            'created_by'=>1
        ]);
    }
    
}
