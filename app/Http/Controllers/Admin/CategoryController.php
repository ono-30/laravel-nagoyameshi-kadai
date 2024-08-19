<?php

namespace App\Http\Controllers\Admin;

use App\Models\Restaurant;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        if ($keyword !== null) {
            $categories = Category::where('name', 'like', "%{$keyword}%")->paginate(15);
            $total = $categories->total();
        } else {
            $categories = Category::paginate(15);
            $total = $categories->total();
        }

        return view('admin.categories.index', compact('categories', 'keyword', 'total'));
    }



    public function store(Request $request)
    {
        /*バリデーションの設定*/
        $request->validate([
            'name' => 'required',
        ]);

        /*HTTPリクエストから値を取得し、新しいカテゴリを作成*/
        $category = new Category();
        $category->name = $request->input('name');

        /*データベースに保存*/
        $category->save();

        /*保存成功後のリダイレクト・レスポンス*/
        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを登録しました。');
    }



    public function update(Request $request, Category $category)
    {
        /*バリデーションの設定*/
        $request->validate([
            'name' => 'required',
        ]);

        /*HTTPリクエストから値を取得し、既存のカテゴリを更新*/
        $category->name = $request->input('name');

        /*データベースに保存*/
        $category->save();

        /*保存成功後のリダイレクト・レスポンス*/
        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを編集しました。');
    }



    public function destroy(Category $category)
    {
        /*レストランデータを削除*/
        $category->delete();

        /*削除成功後のリダイレクトとフラッシュメッセージの設定*/
        return redirect()->route('admin.categories.index')->with('flash_message', 'カテゴリを削除しました。');
    }
}
