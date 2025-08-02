<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\WorkZone;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 1;
    const ROLE_DIRECTOR = 2;
    const ROLE_USER = 3;
    const ROLE_MANAGER = 4;
    const ROLE_ACCOUNTANT = 6;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'father_name',
        'role_id',
        'salary',
        'work_zone_id',
        'lavozimi',
        'username',
        'password',
        'created_at',
        'photo'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // public function roles()
    // {
    //     return DB::table('roles')->where('id', '=', $this->role_id)->first()->name;
    // }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function work_zone()
    {
        return $this->belongsTo(WorkZone::class);
    }

    public function working_days()
    {
        return $this->hasOne(EmployeeDays::class,'user_id','id');
    }

    public function kpis(): HasMany
    {
        return $this->hasMany(UserKpi::class);
    }
    public function user_kpis(): HasMany
    {
        return $this->hasMany(UserKpi::class);
    }
    public function working_kpis(): HasMany
    {
        return $this->hasMany(Kpi::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name. ' '. $this->last_name;
    }

    protected static function booted()
    {
        static::created(function ($user) {
            \Artisan::call('kpis:assign', ['userId' => $user->id]);
        });
    }
}
