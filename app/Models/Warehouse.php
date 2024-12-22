<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_main', 'warehouseman'];

    protected static function boot(){
        parent::boot();

        static::created(function ($model) {
            Log::create([
                'user_id' => auth()->id() ?? 1,
                'action' => 'Yaratdı',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode(["action" => $model->name . " adlı anbar yaratdı", "data" => $model->getAttributes()], JSON_UNESCAPED_UNICODE)
            ]);
        });

        static::updated(function ($model) {
            Log::create([
                'user_id' => auth()->id(),
                'action' => 'Düzəliş etdi',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode(["action" => $model->getOriginal('name') . " adlı anbara düzəliş etdi", "uptaded" => $model->getChanges()], JSON_UNESCAPED_UNICODE)
            ]);
        });

        static::deleted(function ($model) {
            Log::create([
                'user_id' => auth()->id(),
                'action' => 'Sildi',
                'model_type' => get_class($model),
                'model_id' => $model->id,
                'changes' => json_encode(["action" => $model->name . " adlı anbarı sildi"], JSON_UNESCAPED_UNICODE)
            ]);
        });
    }


    public static function getMainWarehouse()
    {
        return self::where('is_main', 1)->first();
    }

    public function product_entries(): HasMany
    {
        return $this->hasMany(ProductEntry::class, 'warehouse_id');
    }
    public function warehouse_from_logs(): HasMany
    {
        return $this->hasMany(WarehouseLog::class, 'from_warehouse_id');
    }
    public function warehouse_to_logs(): HasMany
    {
        return $this->hasMany(WarehouseLog::class, 'to_warehouse_id');
    }
}
