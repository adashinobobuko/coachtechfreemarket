<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Evaluation;
use App\Models\User;

class EvaluationController extends Controller
{
    public function store(Request $request, $purchaseId)
    {
        $purchase = Purchase::findOrFail($purchaseId);
    
        $fromUserId = auth()->id();
        $toUserId = $fromUserId === $purchase->buyer_id
            ? $purchase->good->user_id
            : $purchase->buyer_id;
    
        // すでに評価済みか確認
        $alreadyEvaluated = Evaluation::where('purchase_id', $purchase->id)
            ->where('from_user_id', $fromUserId)
            ->exists();
    
        if ($alreadyEvaluated) {
            return redirect()->back()->with('error', 'この取引はすでに評価済みです。');
        }
    
        Evaluation::create([
            'purchase_id' => $purchase->id,
            'from_user_id' => $fromUserId,
            'to_user_id' => $toUserId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);
    
        return redirect()->route('index', $purchaseId)->with('success', '評価を投稿しました。');
    }
}
