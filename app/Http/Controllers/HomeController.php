<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {

        /*restaurantsテーブルから6つのデータを取得し、変数$new_restaurantsに代入する*/
        $highly_rated_restaurants = Restaurant::take(6)->get();
        /*categoriesテーブルからすべてのデータを取得し、変数$categoriesに代入する*/
        $categories = Category::all();
        /*restaurantsテーブルから作成日時が新しい順に6つのデータを取得し、変数$new_restaurantsに代入する*/
        $new_restaurants = Restaurant::orderBy('created_at', 'desc')->take(6)->get();
        /*変数$highly_rated_restaurants、$categories、$new_restaurantsをhome.blade.phpファイルに渡す*/
        return view('home', compact('highly_rated_restaurants', 'categories', 'new_restaurants'));
    }
}
