<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'status',
        'requested_by',
        'approved_by',
        'rejection_reason',
        'notes',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($po) {
            DB::transaction(function () use ($po) {
                $datePrefix = 'PO-' . now()->format('Ymd') . '-';
                $lastPo = PurchaseOrder::where('po_number', 'like', $datePrefix . '%')
                    ->lockForUpdate()
                    ->orderBy('po_number', 'desc')
                    ->first();

                $sequence = 1;
                if ($lastPo) {
                    $lastSequence = (int) substr($lastPo->po_number, -4);
                    $sequence = $lastSequence + 1;
                }

                $po->po_number = $datePrefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            });
        });
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
