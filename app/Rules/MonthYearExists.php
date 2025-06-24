<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MonthYearExists implements Rule
{
    protected $table;
    protected $column;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($table,$column)
    {
        $this->table = $table;
        $this->column = $column;
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
        list($month, $year) = explode('-', $this->column);

        return DB::table($this->table)
            ->where('month_id', '=', $month)
            ->where('year', '=', $year)
            ->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Ushbu oyda ma\'lumot kiritish tasdiqlanmagan';
    }
}
