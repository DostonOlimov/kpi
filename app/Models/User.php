<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Role;
use App\Models\WorkZone;
use App\Models\Attendance;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    const ROLE_ADMIN = 1;
    const ROLE_DIRECTOR = 2;
    const ROLE_USER = 3;
    const ROLE_MANAGER = 4;
    const ROLE_ACCOUNTANT = 6;
    const ROLE_RAHBAR = 7;
    const ROLE_KADRLAR = 8;
    const ROLE_IJRO = 9;

    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

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
        'photo',
        'pinfl',
        'ch_id'
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

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
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

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(Task::class, UserKpi::class);
    }
    public function user_kpis(): HasMany
    {
        return $this->hasMany(UserKpi::class);
    }
    public function working_kpis(): HasMany
    {
        return $this->hasMany(Kpi::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'external_id', 'ch_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name. ' '. $this->last_name;
    }

    /**
     * Check if the user has a given role (by role_id constant).
     * Checks both the default role_id and the user_roles pivot table.
     */
    public function hasRole(int $roleId): bool
    {
        if ($this->role_id === $roleId) {
            return true;
        }
        return $this->roles()->where('roles.id', $roleId)->exists();
    }

    /**
     * Check if the user has any of the given roles.
     */
    public function hasAnyRole(array $roleIds): bool
    {
        foreach ($roleIds as $roleId) {
            if ($this->hasRole($roleId)) {
                return true;
            }
        }
        return false;
    }

    protected static function booted()
    {
        static::created(function ($user) {
            \Artisan::call('kpis:assign', ['userId' => $user->id]);
        });
        static::addGlobalScope('active', function ($query) {
            $query->where('users.status', self::STATUS_ACTIVE);
        });
    }
}
