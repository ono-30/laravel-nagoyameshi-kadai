<?php

namespace App\Models;

use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    /*リレーションシップの設定（レストランに対して多対1）*/
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /*リレーションシップの設定（会員に対して多対1）*/
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
