<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ExistMonthforEmployee implements Rule
{
    protected $table;
    protected $year;
    protected $month;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table,$year,$month)
    {
        $this->table = $table;
        $this->year = $year;
        $this->month = $month;
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
            ->where('month_id', '=', $this->month)
            ->where('year', '=', $this->year)
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
