<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelevantUser extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'boss_id'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boss()
    {
        return $this->belongsTo(User::class, 'boss_id');
    }
}