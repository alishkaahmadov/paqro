<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'is_main'];


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
