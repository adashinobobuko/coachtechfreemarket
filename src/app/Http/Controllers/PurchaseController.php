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

        return redirect()->route('purchase.complete')->with('success', '購入が完了しました');
    }

    public function complete()
    {
        return view('index');
    }
}

