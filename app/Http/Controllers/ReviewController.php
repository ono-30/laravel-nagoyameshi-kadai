<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Restaurant;
use App\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Restaurant $restaurant, Request $request)
    {
        $user = Auth::user();


        if (Auth::user()->subscribed('premium_plan')) {
            /*有料プランユーザーの場合*/
            $reviews = Review::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->paginate(5);
        } else {
            /*無料ユーザーの場合*/
            $reviews = Review::where('restaurant_id', $restaurant->id)->orderBy('created_at', 'desc')->take(3)->get();
        }
        return view('reviews.index', compact('restaurant', 'reviews'));
    }



    public function create(Restaurant $restaurant)
    {
        return view('reviews.create', compact('restaurant'));
    }



    public function store(Request $request, Restaurant $restaurant)
    {
        /*バリデーションの設定*/
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        /*restaurant_idを取得*/
        $restaurantId = $restaurant->id;

        /*HTTPリクエストから上記のパラメータを取得し、IDの配列に基づいてreviewsテーブルにデータを追加する */
        $review = new Review();
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->restaurant_id = $restaurant->id;
        $review->user_id = $request->user()->id;

        /*レビューを保存*/
        $review->save();

        /*フラッシュメッセージをセッションに保存*/
        session()->flash('flash_message', 'レビューを投稿しました。');

        /*レビュー投稿後のリダイレクト・レスポンス*/
        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを投稿しました。');
    }



    public function edit(Restaurant $restaurant, Review $review)
    {
        /*現在のユーザーがレビューの作成者であるか確認*/
        if ($review->user_id !== Auth::id()) {
            /*ユーザーIDが一致しない場合、エラーメッセージを表示してリダイレクト*/
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }
        /*ビューにデータを渡す*/
        return view('reviews.edit', compact('restaurant', 'review'));
    }



    public function update(Request $request, Restaurant $restaurant, Review $review)
    {
        /*バリデーションの設定*/
        $request->validate([
            'score' => 'required|numeric|between:1,5',
            'content' => 'required',
        ]);

        /*現在のユーザーがレビューの作成者であるか確認*/
        if ($review->user_id !== Auth::id()) {
            /*ユーザーIDが一致しない場合、エラーメッセージを表示してリダイレクト*/
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        /*HTTPリクエストから上記のパラメータを取得し、IDの配列に基づいてreviewsテーブルにデータを更新する */
        $review->score = $request->input('score');
        $review->content = $request->input('content');
        $review->save();

        /*レビュー編集後のリダイレクト・レスポンス*/
        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを編集しました。');
    }



    public function destroy(Restaurant $restaurant, Review $review)
    {
        /*現在のユーザーがレビューの作成者であるか確認*/
        if ($review->user_id !== Auth::id()) {
            /*ユーザーIDが一致しない場合、エラーメッセージを表示してリダイレクト*/
            return redirect()->route('restaurants.reviews.index', $restaurant)->with('error_message', '不正なアクセスです。');
        }

        /*レビューの削除*/
        $review->delete();

        /*レビュー削除後のリダイレクト・レスポンス*/
        return redirect()->route('restaurants.reviews.index', $restaurant)->with('flash_message', 'レビューを削除しました。');
    }
}
