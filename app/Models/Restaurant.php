<?php

namespace App\Models;

use App\Models\Category;
use App\Models\Review;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Restaurant extends Model
{
    use HasFactory, Sortable;

    public $sortable = ['created_at', 'lowest_price', 'popular'];

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTimestamps();
    }

    public function regular_holidays()
    {
        return $this->belongsToMany(RegularHoliday::class)->withTimestamps();
    }

    /*リレーションシップの設定（レビューに対して1対多）*/
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /*リレーション先であるreviewsテーブルの平均評価を算出し、その平均値をもとにデータを並べ替える*/
    public function ratingSortable($query, $direction)
    {
        return $query->withAvg('reviews', 'score')->orderBy('reviews_avg_score', $direction);
    }

    /*リレーションシップの設定（予約に対して1対多）*/
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    /*リレーション先であるreservationsテーブルの予約件数を算出し、その件数をもとにデータを並べ替える*/
    public function popularSortable($query, $direction)
    {
        return $query->withCount('reservations')->orderBy('reservations_count', $direction);
    }
}
