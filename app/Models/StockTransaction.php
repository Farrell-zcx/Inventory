<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'type',
        'quantity',
        'supplier_id',
        'borrower_name',
        'status',
        'reference_id',
        'document_path',
        'notes',
        'created_by',
        'transaction_date',
        'unit_price',
        'total_price'
    ];

    protected $casts = [
        'transaction_date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        return $this->belongsTo(StockTransaction::class, 'reference_id');
    }
}
