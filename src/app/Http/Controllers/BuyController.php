<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Good;

class BuyController extends Controller
{
    public function showBuyform($id)
    {
        $good = Good::find($id); // 商品IDから取得

        if (!$good)
        {
        abort(404, '商品が見つかりません');
        }

        return view('goods.goods-buy',compact('good'));
    }
}
