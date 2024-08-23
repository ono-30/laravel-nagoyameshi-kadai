<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Restaurant;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;


class HomeController extends Controller
{
    public function index()
    {
        $total_users = User::count();
        /*有料会員数*/
        $total_premium_users = DB::table('subscriptions')->where('stripe_status', 'active')->distinct('user_id')->count('user_id');
        /*無料会員数 変数$total_usersの値から変数$total_premium_usersの値を減算*/
        $total_free_users = $total_users - $total_premium_users;
        $total_restaurants = Restaurant::count();
        $total_reservations = Reservation::count();
        /*月間売上 ※月額の300と有料会員数を乗算*/
        $sales_for_this_month = $total_premium_users * 300;

        return view('admin.home', compact(
            'total_users',
            'total_premium_users',
            'total_free_users',
            'total_restaurants',
            'total_reservations',
            'sales_for_this_month'
        ));
    }
}
