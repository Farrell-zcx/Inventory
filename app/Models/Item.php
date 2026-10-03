<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'category_id',
        'condition',
        'location_id',
        'unit',
        'current_stock'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $item->code = static::generateCode();
        });
    }

    protected static function generateCode(): string
    {
        $today = now()->format('Ymd');
        $prefix = "ITM-{$today}-";

        return DB::transaction(function () use ($prefix) {
            $last = static::where('code', 'like', $prefix . '%')
                ->lockForUpdate()
                ->orderByDesc('code')
                ->first();

            $nextNumber = $last
                ? ((int) substr($last->code, -4)) + 1
                : 1;

            return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        });
    }
}
