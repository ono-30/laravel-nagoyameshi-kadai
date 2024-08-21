<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /*リレーションシップの設定（レストランに対して多対1）*/
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class)->withTimestamps();
    }

    /*リレーションシップの設定（会員に対して多対1）*/
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
