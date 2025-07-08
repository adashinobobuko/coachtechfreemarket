<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Good;
use App\Models\User;
use App\Models\Category;
use App\Http\Requests\ExhibitionRequest;
use Illuminate\Support\Facades\Auth;

class ListingController extends Controller
{
    public function showSellForm()
    {
        $categories = Category::all();
        return view('listing', compact('categories'));
    }
    
    public function store(ExhibitionRequest $request)
    {
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('goods', 'public');
        } else {
            return back()->with('error', '画像がアップロードされていません');
        }
    
        $goods = Good::create([
            'user_id' => Auth::id(),
            'image' => $imagePath,
            'condition' => $request->condition,
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
            'price' => $request->price,
        ]);
    
        // 中間テーブルへカテゴリを保存
        $goods->categories()->sync($request->input('category_ids', []));
    
        return redirect()->route('index')->with('success', '商品が出品されました');
    }
    
}
