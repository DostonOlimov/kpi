<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertDefaultCriterias extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $bands_data = [
            [
                'name'=>'uchramadi',
                'fine_ball'=>0,
                'type'=>1,
            ],
            [
                'name'=>'ba\'zida kuzatildi',
                'fine_ball'=>1,
                'type'=>1,
            ],
            [
                'name'=>'surunkali kuzatildi',
                'fine_ball'=>2,
                'type'=>1,
            ],
            [
                'name'=>'to\'liq rioya qildi',
                'fine_ball'=>0,
                'type'=>2,
            ],
            [
                'name'=>'qisman rioya qildi',
                'fine_ball'=>1,
                'type'=>2,
            ],
            [
                'name'=>'rioya qilmadi',
                'fine_ball'=>2,
                'type'=>2,
            ],
        ];
        DB::table('kpi_criteria_bands')->insert($bands_data);

        $data = [[
            'kpi_id' => 8,
            'name' => 'Ishga kelmaslik',
            'description' => 'Xodim ruxsatsiz ishga kelmagan holatlar.',
            'type' =>1
        ],
            [
                'kpi_id' => 8,
                'name' => '	Kechikishlar',
                'description' => 'Ishga kech kelishlar soni.',
                'type' =>1
            ],
            [
                'kpi_id' => 8,
                'name' => 'Ishdan erta ketish',
                'description' => 'Ruxsatsiz erta ish joyini tark etish.',
                'type' =>1
            ],
            [
                'kpi_id' => 8,
                'name' => 'Tanaffuslardan noto‘g‘ri foydalanish',
                'description' => 'Tushlik yoki namoz tanaffusini belgilangan vaqtdan ortiqcha ishlatish.',
                'type' =>1
            ],
            [
                'kpi_id' => 8,
                'name' => '	Ish vaqtida ish joyida bo‘lmaslik',
                'description' => 'Ish vaqtida ish joyida bo‘lmaslik holatlari.',
                'type' =>1
            ],
            [
                'kpi_id' => 8,
                'name' => 'Mehnat ta’tili qoidalariga rioya qilmaslik',
                'description' => 'Kasallik varaqasi yoki ta’tilga chiqish tartibiga rioya qilmaslik.',
                'type' =>1
            ],
            [
                'kpi_id' => 8,
                'name' => 'Mehnat faolyati bilan bog\'liq boshqa kamchiliklar',
                'description' => 'Mehnat faolyati bilan bog\'liq boshqa kamchiliklar.',
                'type' =>1
            ]
        ];

        DB::table('kpi_criterias')->insert($data);

        $data2 = [[
            'kpi_id' => 9,
            'name' => '	Kasb madaniyati',
            'description' => 'Ish joyida hurmatli, odobli muomala qilishi.',
            'type' =>2
        ],
            [
                'kpi_id' => 9,
                'name' => 'Jamoaga moslashuv',
                'description' => 'Hamkasblar bilan o‘zaro hurmatda bo‘lish.',
                'type' =>2
            ],
            [
                'kpi_id' => 9,
                'name' => 'Fuqarolar bilan muomala',
                'description' => 'Fuqarolar, tashrif buyuruvchilar bilan axloqli muomala.',
                'type' =>2
            ],
            [
                'kpi_id' => 9,
                'name' => 'Ichki tartibga amal qilish',
                'description' => 'Ofis ichki tartib-qoidalariga bo‘ysunish.',
                'type' =>2
            ],
            [
                'kpi_id' => 9,
                'name' => 'Kommunikatsiya madaniyati',
                'description' => 'Rasmiy yozishmalar, gapirish uslubi, telefon etiketi.',
                'type' =>2
            ],
            [
                'kpi_id' => 9,
                'name' => 'Boshqa odob axloq normalarida faolligi',
                'description' => 'Boshqa odob axloq normalarida faolligi.',
                'type' =>2
            ]
        ];

        DB::table('kpi_criterias')->insert($data2);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
