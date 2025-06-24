<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DirectorKpiExist implements Rule
{
    protected $table;
    protected $year;
    protected $month;
    protected $work_zone_id;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table,$work_zone_id,$month,$year)
    {
        $this->table = $table;
        $this->year = $year;
        $this->month = $month;
        $this->work_zone_id = $work_zone_id;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {

        return DB::table($this->table)
            ->where('month', '=', $this->month)
            ->where('year', '=', $this->year)
            ->where('work_zone_id', '=', $this->work_zone_id)
            ->where('razdel', '=', 1)
            ->where('status', '=', 'active')
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ushbu oy uchun ma\'lumotlar kiritish tasdiqlanmagan.';
    }
}
