<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Category;
use Illuminate\Http\Request;

class RestaurantController extends Controller
{
    public function index(Request $request)
    {
        /*検索パラメータの取得*/
        $keyword = $request->input('keyword');
        $category_id = $request->input('category_id');
        $price = $request->input('price');

        /*並び替えオプションの定義*/
        $sorts = [
            '掲載日が新しい順' => 'created_at desc',
            '価格が安い順' => 'lowest_price asc',
            '評価が高い順' => 'rating desc'
        ];

        $sort_query = [];
        $sorted = "created_at desc";

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }

        /*クエリビルダの初期化*/
        $query = Restaurant::query();

        /*キーワード検索*/
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")->orWhere('address', 'like', "%{$keyword}%")->orWhereHas('categories', function ($query) use ($keyword) {
                    $query->where('categories.name', 'like', "%{$keyword}%");
                });
            });
        }
        /*カテゴリによる絞り込み*/
        if ($category_id) {
            $query->whereHas('categories', function ($q) use ($category_id) {
                $q->where('categories.id', $category_id);
            });
        }

        /*価格による絞り込み*/
        if ($price) {
            $query->where('lowest_price', '<=', $price);
        }

        /*並び替えとページネーションの適用*/
        $restaurants = $query->sortable()->orderByRaw($sorted)->paginate(15);

        /*総数の取得*/
        $total = $restaurants->total();

        /*カテゴリの取得*/
        $categories = Category::all();

        return view('restaurants.index', compact(
            'keyword',
            'category_id',
            'price',
            'sorts',
            'sorted',
            'restaurants',
            'categories',
            'total'
        ));
    }



    public function show(Restaurant $restaurant)
    {
        return view('restaurants.show', compact('restaurant'));
    }
}
