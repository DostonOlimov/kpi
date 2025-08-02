<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class InsertBandsTwo extends Migration
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
                'name'=>'To‘liq bajardi',
                'fine_ball'=>0,
                'type'=>3,
            ],
            [
                'name'=>'Qisman bajardi',
                'fine_ball'=>1,
                'type'=>3,
            ],
            [
                'name'=>'Bajarilmadi',
                'fine_ball'=>2,
                'type'=>3,
            ],
            [
                'name'=>'To‘liq javob berilgan',
                'fine_ball'=>0,
                'type'=>4,
            ],
            [
                'name'=>'Qisman javob berilgan',
                'fine_ball'=>1,
                'type'=>4,
            ],
            [
                'name'=>'Javob berilmagan yoki sust',
                'fine_ball'=>2,
                'type'=>4,
            ],
        ];
        DB::table('kpi_criteria_bands')->insert($bands_data);

        $data = [[
            'kpi_id' => 5,
            'name' => '	Topshiriqlarni bajarish soni',
            'description' => 'Belgilangan vazifalar sonining bajarilganiga nisbati.',
            'type' =>3
        ],
            [
                'kpi_id' => 5,
                'name' => '	Topshiriqlarning sifatli bajarilishi',
                'description' => 'Natijaning sifat talablariga mosligi.',
                'type' =>3
            ],
            [
                'kpi_id' => 5,
                'name' => 'Muddatga rioya qilish',
                'description' => 'Belgilangan vaqt ichida tugatganlik holati.',
                'type' =>3
            ],
            [
                'kpi_id' => 5,
                'name' => 'Yuklatilgan vazifalarga mas’uliyat bilan yondashish',
                'description' => 'Berilgan vazifalarni e’tibor bilan, to‘liq va javobgarlik bilan bajarish.',
                'type' =>3
            ],
            [
                'kpi_id' => 5,
                'name' => 'Takroriy topshiriqlar ehtiyoji',
                'description' => 'Ishni qayta topshirishga ehtiyoj tug‘ilgan holatlar soni.',
                'type' =>3
            ],
        ];

        DB::table('kpi_criterias')->insert($data);

        $data4 = [[
            'kpi_id' => 6,
            'name' => 'Murojaatlarga o‘z vaqtida javob berish',
            'description' => 'Fuqarolarning murojaatlariga belgilangan muddatlarda javob berilganligi.',
            'type' =>4
        ],
            [
                'kpi_id' => 6,
                'name' => 'Javoblarning sifati',
                'description' => 'Berilgan javoblarning aniqligi, tushunarli va qonuniy asoslanganligi.',
                'type' =>4
            ],
            [
                'kpi_id' => 6,
                'name' => 'Murojaatlarni to‘liq o‘rganish.',
                'description' => 'Har bir murojaatning mazmuni bilan to‘liq tanishish va tahlil qilish.',
                'type' =>4
            ],
            [
                'kpi_id' => 6,
                'name' => 'Murojaatchiga hurmatli munosabat.',
                'description' => 'Muloqotda odob saqlash, murojaatchining ehtiyojlariga e’tibor qaratish.',
                'type' =>4
            ],
            [
                'kpi_id' => 6,
                'name' => 'Murojaatlarning statistik nazorati va hisobotini yuritish.',
                'description' => 'Murojaatlarning ro‘yxati, javob sanalari va holati bo‘yicha monitoring.',
                'type' =>4
            ],
        ];

        DB::table('kpi_criterias')->insert($data4);
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
