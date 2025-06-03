<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Punch;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    public function punches()
    {
        return $this->hasMany(Punch::class);
    }

    public function lastCheckIn()
    {
        $last = $this->punches()
        ->whereNotNull('check_in')
        ->orderByDesc('check_in')
        ->first();

        return $last ? \Carbon\Carbon::parse($last->check_in)->toDateString() : null;
        // return "2025-05-27";
    }

        public function lastCheckOut()
    {
        $last = $this->punches()
        ->whereNotNull('check_out')
        ->orderByDesc('check_out')
        ->first();

         return $last ? \Carbon\Carbon::parse($last->check_out)->toDateString() : null;
        // return "2025-05-26";
    }

    
}