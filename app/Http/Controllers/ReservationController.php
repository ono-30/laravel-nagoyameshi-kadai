<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $reservations = Reservation::where('user_id', $user->id)->orderBy('reserved_datetime', 'desc')->paginate(15);
        /*$reservations = Auth::user()->reservations()->orderBy※('reserved_datetime※カラム名を入れる', 'desc')->paginate(15);*/

        return view('reservations.index', compact('reservations'));
    }



    public function create(Restaurant $restaurant)
    {
        return view('reservations.create', compact('restaurant'));
    }



    public function store(Request $request, Restaurant $restaurant)
    {
        /*バリデーションの設定*/
        $request->validate([
            'reservation_date' => 'required|date_format:Y-m-d',
            'reservation_time' => 'required|date_format:H:i',
            'number_of_people' => 'required|numeric|between:1,50'
        ]);

        /*HTTPリクエストから上記のパラメータを取得し、IDの配列に基づいてreservationsテーブルにデータを追加する */
        $reservation = new Reservation();
        /*$reservation->reservation_date = $request->input('reservation_date');
        $reservation->reservation_time = $request->input('reservation_time');*/
        $reservation->number_of_people = $request->input('number_of_people');
        $reservation->restaurant_id = $restaurant->id;
        $reservation->user_id = $request->user()->id;

        /*reserved_datetimeプロパティを追加*/
        $reservationDate = $request->input('reservation_date');
        $reservationTime = $request->input('reservation_time');
        $reservation->reserved_datetime = $reservationDate . '-' . $reservationTime;

        /*予約を保存*/
        $reservation->save();

        /*予約完了後のリダイレクト・レスポンス*/
        return redirect()->route('reservations.index')->with('flash_message', '予約が完了しました。');
    }



    public function destroy(Request $request, Reservation $reservation)
    {
        /*現在のユーザーIDと予約のユーザーIDを比較*/
        if ($reservation->user_id !== Auth::id()) {
            /*一致しない場合、エラーメッセージをセッションに保存してリダイレクト*/
            return redirect()->route('reservations.index')->with('error_message', '不正なアクセスです。');
        }

        /*予約データを削除*/
        $reservation->delete();

        /*削除完了後のリダイレクト・レスポンス*/
        return redirect()->route('reservations.index')->with('flash_message', '予約がキャンセルされました。');
    }
}
