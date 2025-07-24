<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\TransactionMessageRequest;
use App\Models\TransactionMessage;
use App\Models\Transaction;
use App\Models\Purchase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\TransactionCompletedMail;

class ChatController extends Controller
{
    public function showBuyerChat($purchaseId)
    {
        $user = Auth::user();

        $purchase = Purchase::with('transaction.good.user')->findOrFail($purchaseId);

        if (!$purchase->transaction || !$purchase->transaction->good) {
            abort(404, '取引情報が不完全です');
        }

        $transaction = $purchase->transaction;
        $good = $transaction->good;
        $otherUser = $good->user;

        TransactionMessage::where('purchase_id', $purchaseId)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $otherTransactions = Purchase::with('good', 'transaction')
            ->where('user_id', $user->id)
            ->where('id', '!=', $purchaseId)
            ->orderByDesc('updated_at')
            ->get();

        $messages = TransactionMessage::where('purchase_id', $purchaseId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $alreadyEvaluated = \App\Models\Evaluation::where('purchase_id', $purchase->id)
            ->where('from_user_id', $user->id)
            ->exists();

        return view('chat.chat-buyer', compact(
            'purchase',
            'transaction',
            'good',
            'otherUser',
            'messages',
            'otherTransactions',
            'alreadyEvaluated'
        ));
    }
    
    public function showSellerChat($purchaseId)
    {
        $user = Auth::user();
    
        $purchase = Purchase::findOrFail($purchaseId);
        $transaction = $purchase->transaction;
    
        $good = $purchase->good;
        $otherUser = $transaction->buyer_id === $user->id ? $transaction->seller : $transaction->buyer;
        $alreadyEvaluated = $transaction->evaluation()
            ->where('from_user_id', $user->id)
            ->exists();
    
        // 未読メッセージを既読に更新
        TransactionMessage::where('transaction_id', $transaction->id)
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);
    
        // サイドバー表示用：他の取引（出品者としての進行中取引）
        $otherTransactions = Purchase::with('good')
            ->whereHas('transaction', function ($q) use ($user) {
                $q->where('seller_id', $user->id)
                  ->where('status', 'in_progress');
            })
            ->where('id', '!=', $purchaseId)
            ->orderByDesc('updated_at')
            ->get();
    
        $messages = TransactionMessage::where('transaction_id', $transaction->id)
            ->orderBy('created_at', 'asc')
            ->get();
    
        return view('chat.chat-seller', [
            'transaction' => $transaction,
            'purchase' => $purchase,
            'good' => $good,
            'alreadyEvaluated' => $alreadyEvaluated,
            'otherUser' => $otherUser,
            'messages' => $messages,
            'otherTransactions' => $otherTransactions,
        ]);
    }
    
    
    public function store(TransactionMessageRequest $request, $purchaseId)
    {
        $data = $request->validated();
    
        // 画像があれば保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('transaction_images', 'public');
            $data['image_path'] = $path;
        }
    
        // 購入情報取得（リレーションがあれば調整）
        $purchase = Purchase::findOrFail($purchaseId);
        $data['transaction_id'] = $purchase->transaction_id;
    
        // メッセージの保存
        $data['purchase_id'] = $purchaseId;
        $data['user_id'] = auth()->id();
    
        // 送信先のユーザーIDを設定
        if ($data['user_id'] === $purchase->user_id) {
            $data['recipient_id'] = $purchase->good->user_id;
        } else {
            $data['recipient_id'] = $purchase->user_id;
        }
    
        TransactionMessage::create($data);
    
        if (auth()->id() === $purchase->user_id) {
            return redirect()->route('chat.buyer', ['purchaseId' => $purchaseId])
                ->with('success', 'メッセージを送信しました。');
        } else {
            return redirect()->route('chat.seller', ['purchaseId' => $purchaseId])
                ->with('success', 'メッセージを送信しました。');
        }
    }

    public function edit($id)
    {
        // メッセージの内容を編集する
        $message = TransactionMessage::findOrFail($id);
        $this->authorize('update', $message);
        return view('chat.edit-message', compact('message'));
    }

    public function update(TransactionMessageRequest $request, $id)
    {
        // メッセージの更新
        $message = TransactionMessage::findOrFail($id);
        $this->authorize('update', $message);
        
        $data = $request->validated();
        $message->update($data);
        
        return redirect()->back()->with('success', 'メッセージを更新しました。');
    }

    public function destroy($id)
    {
        // メッセージを削除する
        $message = TransactionMessage::findOrFail($id);
        $this->authorize('delete', $message);
        
        $message->delete();
        
        return redirect()->back()->with('success', 'メッセージを削除しました。');
    }

    public function complete(Purchase $purchase)
    {   
        if ($purchase->user_id !== auth()->id()) {
            abort(403, 'あなたにはこの取引を完了する権限がありません。');
        }
    
        $transaction = $purchase->transaction;
        $transaction->status = 'completed'; // ← is_completedをやめてstatusに統一
        $transaction->save();

        if (!$transaction) {
            return redirect()->back()->with('error', '取引データが見つかりません。');
        }
    
        $transaction->load('purchase.good', 'purchase.user');

        $seller = $purchase->good->user;
        Mail::to($seller->email)->send(new TransactionCompletedMail($seller, $transaction));
    
        return redirect()->route('chat.buyer', ['purchaseId' => $purchase->id, 'completed' => 1])
        ->with('success', '取引を完了しました。');

    }
}
