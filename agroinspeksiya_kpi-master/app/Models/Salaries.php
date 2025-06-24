<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Salaries extends Model
{
    protected $table = 'salaries';
    use HasFactory;
    protected $fillable = [
        'user_id',
        'salary',
        'from_date',
        'to_date',
    ];
    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
