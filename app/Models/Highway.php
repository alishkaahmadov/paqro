<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Highway extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'product_entry_id', 'quantity', 'pdf_file', 'entry_date'];

    protected static function boot(){
        parent::boot();

        static::created(function ($model) {
            Log::create([
                'user_id' => auth()->id(),
                'action' => 'Yaratdı',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode(["action" => $model->code . " adlı şassi yaratdı", "data" => ["say" => $model->quantity, "mehsul" => $model->getProductInfo()]], JSON_UNESCAPED_UNICODE)
            ]);
        });
    }

    public function product_entry(): BelongsTo
    {
        return $this->belongsTo(ProductEntry::class, 'product_entry_id');
    }

    public function getProductInfo()
    {

        if ($this->product_entry?->product) {
            return $this->product_entry?->product->name . ' (' . $this->product_entry?->product->code . ')';
        }
        return 'Unknown Product (Unknown Code)';
    }

}
