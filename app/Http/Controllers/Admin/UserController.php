<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /*会員一覧ページのアクションを定義*/
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword !== null) {
            $users = User::where('name', 'like', "%{$keyword}%")->paginate(15);
            $total = $users->total();
        } else {
            $users = User::paginate(15);
            $total = $users->total();
        }

        return view('admin.users.index', compact('users', 'keyword', 'total'));
    }

    /*会員詳細ページのアクションを定義 */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }
}
