<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function create()
    {
        $intent = Auth::user()->createSetupIntent();
        return view('subscription.create', compact('intent'));
    }



    public function store(Request $request)
    {
        /*Userインスタンスを取得し、newSubscriptionメソッドを使用してモデルのサブスクリプションを作成*/
        $request->user()->newSubscription('premium_plan', 'price_1Pq3dvBEJgRPhNMQSy7gFBt1')->create($request->paymentMethodId);

        /*有料プラン登録後のリダイレクト・レスポンス*/
        return redirect()->route('home')->with('flash_message', '有料プランへの登録が完了しました。');
    }



    public function edit()
    {
        $user = Auth::user();
        $intent = $user->createSetupIntent();

        return view('subscription.edit', compact('user', 'intent'));
    }



    public function update(Request $request)
    {
        /*Userインスタンスを取得し、updateDefaultPaymentMethodメソッドを使用してそのユーザーのデフォルトの支払い方法を更新*/
        $request->user()->updateDefaultPaymentMethod($request->paymentMethodId);/*''を外した*/

        /*お支払い方法更新後のリダイレクト・レスポンス*/
        return redirect()->route('home')->with('flash_message', 'お支払い方法を変更しました。');
    }



    public function cancel()
    {

        return view('subscription.cancel');
    }



    public function destroy(Request $request, User $user)
    {
        /*Userインスタンスを取得し、そのユーザーのサブスクリプションをすぐにキャンセル（解約）*/
        $request->user()->subscription('premium_plan')->cancelNow();

        /*有料プラン解約後のリダイレクト・レスポンス*/
        return redirect()->route('home')->with('flash_message', '有料プランを解約しました。');
    }
}
