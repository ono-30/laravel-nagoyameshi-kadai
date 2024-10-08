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
            '評価が高い順' => 'rating desc',
            '予約数が多い順' => 'popular desc'
        ];

        $sort_query = [];
        $sorted = 'created_at desc';

        if ($request->has('select_sort')) {
            $slices = explode(' ', $request->input('select_sort'));
            $sort_query[$slices[0]] = $slices[1];
            $sorted = $request->input('select_sort');
        }


        /*キーワード検索*/
        if ($keyword) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->orWhere('address', 'like', "%{$keyword}%")->orWhereHas('categories', function ($query) use ($keyword) {
                $query->where('categories.name', 'like', "%{$keyword}%");
            })
                ->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        } elseif ($category_id) {
            /*カテゴリによる絞り込み*/
            $restaurants = Restaurant::whereHas('categories', function ($query) use ($category_id) {
                $query->where('categories.id', $category_id);
            })->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        } elseif ($price) {
            /*価格帯による絞り込み*/
            $restaurants = Restaurant::where('lowest_price', '<=', $price)->sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        } else {
            /*並び替えとページネーションの適用*/
            $restaurants = Restaurant::sortable($sort_query)->orderBy('created_at', 'desc')->paginate(15);
        }

        /*カテゴリの取得*/
        $categories = Category::all();

        /*総数の取得*/
        $total = $restaurants->total();

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
