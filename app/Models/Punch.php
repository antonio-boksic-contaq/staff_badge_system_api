<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;


class Punch extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    protected $fillable = [
        'user_id',
        'check_in',
        'check_out',
        'notes',
        'ci_accepted',
        'co_accepted',
    ];



    // Relazione con User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    
}