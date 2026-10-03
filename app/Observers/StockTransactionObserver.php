<?php

namespace App\Observers;

use App\Models\StockTransaction;

class StockTransactionObserver
{
    /**
     * Handle the StockTransaction "created" event.
     */
    public function created(StockTransaction $transaction): void
    {
        $item = $transaction->item;

        if ($transaction->type === 'masuk') {
            $item->increment('current_stock', $transaction->quantity);
        } elseif ($transaction->type === 'keluar') {
            $item->decrement('current_stock', $transaction->quantity);
        }
    }
}
