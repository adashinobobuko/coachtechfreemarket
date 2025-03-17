<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Good;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;

class PurchaseController extends Controller
{
    public function store(PurchaseRequest $request)
    {
        $user = Auth::user();

        Purchase::create([
            'user_id' => $user->id,
            'good_id' => $request->good_id,
            'payment_method' => $request->payment_method,
            'address' => $user->address
        ]);

        //売り切れの処理フラグを更新
        $good = Good::find($request->good_id);
        if($good){
            $good->update(['is_sold' => true]);
        }

        //フラッシュメッセージとともにindexへ遷移
        return redirect()->route('index')->with(['message' => '購入が完了しました',
                                    'activeTab' => 'recommend'
                                ]);
    }
}

