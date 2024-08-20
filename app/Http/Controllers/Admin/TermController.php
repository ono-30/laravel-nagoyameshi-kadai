<?php

namespace App\Http\Controllers\Admin;

use App\Models\Term;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TermController extends Controller
{
    public function index()
    {
        $term = Term::first();
        return view('admin.terms.index', compact('term'));
    }

    public function edit(Term $term)
    {
        return view('admin.terms.edit', compact('term'));
    }

    public function update(Request $request, Term $term)
    {
        /*バリデーションの設定*/
        $request->validate([
            'content' => 'required',
        ]);

        /*HTTPリクエストから値を取得し、既存の利用規約を更新*/
        $term->content = $request->input('content');

        /*データベースに保存*/
        $term->save();

        /*保存成功後のリダイレクト・レスポンス*/
        return redirect()->route('admin.terms.index')->with('flash_message', '利用規約を編集しました。');
    }
}
