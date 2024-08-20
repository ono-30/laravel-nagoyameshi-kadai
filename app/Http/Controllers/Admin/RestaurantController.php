<?php

namespace App\Http\Controllers\Admin;

use App\Models\RegularHoliday;
use App\Models\Category;
use App\Models\Restaurant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{

    public function index(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword !== null) {
            $restaurants = Restaurant::where('name', 'like', "%{$keyword}%")->paginate(15);
            $total = $restaurants->total();
        } else {
            $restaurants = Restaurant::paginate(15);
            $total = $restaurants->total();
        }

        return view('admin.restaurants.index', compact('restaurants', 'keyword', 'total'));
    }



    public function show(Restaurant $restaurant)
    {
        return view('admin.restaurants.show', compact('restaurant'));
    }



    public function create()
    {
        $categories = Category::all();
        $regular_holidays = RegularHoliday::all();
        return view('admin.restaurants.create', compact('categories', 'regular_holidays'));
    }



    public function store(Request $request)
    {
        /*バリデーションの設定*/
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        /*HTTPリクエストから値を取得し、新しいレストランを作成*/
        $restaurant = new Restaurant();
        $restaurant->name = $request->input('name');

        /*画像のアップロード処理*/
        /*アップロードされたファイル（name="image"）が存在すれば処理を実行する*/
        if ($request->hasFile('image')) {
            /*アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$imageに代入する*/
            $image = $request->file('image')->store('public/restaurants');
            /*ファイルパスからファイル名のみを取得し、Restaurantインスタンスのimageプロパティに代入する*/
            $restaurant->image = basename($image);
        } else {
            /*画像がアップロードされていない場合、空文字を代入*/
            $restaurant->image = '';
        }

        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');

        /*データベースに保存*/
        $restaurant->save();

        /*HTTPリクエストからcategory_idsパラメータを取得し、カテゴリのIDの配列に基づいてcategory_restaurantテーブルにデータを追加する */
        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);

        /*HTTPリクエストからregular_holiday_idsパラメータを取得し、定休日のIDの配列に基づいてregular_holiday_restaurantテーブルのデータを同期する */
        /*null合体演算子を使用して、nullの場合は空の配列を使用*/
        $regular_holiday_ids = $request->input('regular_holiday_ids') ?? [];
        /*array_filterを適用（空の要素を除去）*/
        $filtered_holiday_ids = array_filter($regular_holiday_ids);
        /*同期処理を実行*/
        $restaurant->regular_holidays()->sync($filtered_holiday_ids);

        /*保存成功後のリダイレクト・レスポンス*/
        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を登録しました。');
    }



    public function edit(Restaurant $restaurant)
    {
        $categories = Category::all();
        $regular_holidays = RegularHoliday::all();
        /*設定されたカテゴリのIDを配列化する*/
        $category_ids = $restaurant->categories->pluck('id')->toArray();

        return view('admin.restaurants.edit', compact('restaurant', 'categories', 'category_ids', 'regular_holidays'));
    }



    public function update(Request $request, Restaurant $restaurant)
    {
        /*バリデーションの設定*/
        $request->validate([
            'name' => 'required',
            'image' => 'image|max:2048',
            'description' => 'required',
            'lowest_price' => 'required|numeric|min:0|lte:highest_price',
            'highest_price' => 'required|numeric|min:0|gte:lowest_price',
            'postal_code' => 'required|digits:7',
            'address' => 'required',
            'opening_time' => 'required|before:closing_time',
            'closing_time' => 'required|after:opening_time',
            'seating_capacity' => 'required|numeric|min:0',
        ]);

        /*HTTPリクエストから値を取得し、既存のレストランを更新*/
        $restaurant->name = $request->input('name');

        /*画像のアップロード処理*/
        /*アップロードされたファイル（name="image"）が存在すれば処理を実行する*/
        if ($request->hasFile('image')) {
            /*アップロードされたファイル（name="image"）をstorage/app/public/restaurantsフォルダに保存し、戻り値（ファイルパス）を変数$imageに代入する*/
            $image = $request->file('image')->store('public/restaurants');
            /*ファイルパスからファイル名のみを取得し、Restaurantインスタンスのimageプロパティに代入する*/
            $restaurant->image = basename($image);
        }

        $restaurant->description = $request->input('description');
        $restaurant->lowest_price = $request->input('lowest_price');
        $restaurant->highest_price = $request->input('highest_price');
        $restaurant->postal_code = $request->input('postal_code');
        $restaurant->address = $request->input('address');
        $restaurant->opening_time = $request->input('opening_time');
        $restaurant->closing_time = $request->input('closing_time');
        $restaurant->seating_capacity = $request->input('seating_capacity');

        /*データベースに保存*/
        $restaurant->save();

        /*HTTPリクエストからcategory_idsパラメータを取得し、IDの配列に基づいてcategory_restaurantテーブルにデータを追加する */
        $category_ids = array_filter($request->input('category_ids'));
        $restaurant->categories()->sync($category_ids);

        /*HTTPリクエストからregular_holiday_idsパラメータを取得し、定休日のIDの配列に基づいてregular_holiday_restaurantテーブルのデータを同期する */
        /*null合体演算子を使用して、nullの場合は空の配列を使用*/
        $regular_holiday_ids = $request->input('regular_holiday_ids') ?? [];
        /*array_filterを適用（空の要素を除去）*/
        $filtered_holiday_ids = array_filter($regular_holiday_ids);
        /*同期処理を実行*/
        $restaurant->regular_holidays()->sync($filtered_holiday_ids);

        /*保存成功後のリダイレクト・レスポンス*/
        return redirect()->route('admin.restaurants.show', ['restaurant' => $restaurant->id])->with('flash_message', '店舗を編集しました。');
    }



    public function destroy(Restaurant $restaurant)
    {
        /*レストランデータを削除*/
        $restaurant->delete();

        /*削除成功後のリダイレクトとフラッシュメッセージの設定*/
        return redirect()->route('admin.restaurants.index')->with('flash_message', '店舗を削除しました。');
    }
}
