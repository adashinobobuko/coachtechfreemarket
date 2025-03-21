<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\Good;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\PurchaseRequest;
use Illuminate\Support\Facades\DB;

class PurchaseController extends Controller
{
    public function store(PurchaseRequest $request)
    {
        $user = Auth::user();
        $paymentMethod = $request->payment_method;

        DB::beginTransaction();
        try {
            Purchase::create([
                'user_id' => $user->id,
                'good_id' => $request->good_id,
                'payment_method' => $paymentMethod,
                'address' => $user->address
            ]);

            $good = Good::find($request->good_id);
            if ($good) {
                $good->update(['is_sold' => true]);
            }

            DB::commit();

            return redirect()->route('index')->with(['message' => '購入が完了しました', 'activeTab' => 'recommend']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('購入処理エラー: ' . $e->getMessage());
            return redirect()->route('index')->with('error', '購入処理中にエラーが発生しました。');
        }
}
}

