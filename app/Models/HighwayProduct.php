<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HighwayProduct extends Model
{
    use HasFactory;

    protected $fillable = ['highway_id', 'measure', 'moto_saat', 'product_entry_id', 'quantity', 'pdf_file', 'entry_date', 'from_warehouse_id'];

    public function product_entry(): BelongsTo
    {
        return $this->belongsTo(ProductEntry::class, 'product_entry_id');
    }

    public function highway(): BelongsTo
    {
        return $this->belongsTo(Highway::class, 'highway_id');
    }

    protected static function boot(){
        parent::boot();

        static::created(function ($model) {
            Log::create([
                'user_id' => auth()->id(),
                'action' => 'Yaratdı',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode(["action" => $model->highway?->code . " adlı şassi yaratdı", "data" => ["say" => $model->quantity, "mehsul" => $model->getProductInfo()]], JSON_UNESCAPED_UNICODE)
            ]);
        });
    }

    public function getProductInfo()
    {
        if ($this->product_entry?->product) {
            return $this->product_entry?->product->name . ' (' . $this->product_entry?->product->code . ')';
        }
        return 'Unknown Product (Unknown Code)';
    }
}
