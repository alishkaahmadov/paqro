<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseLog extends Model
{
    use HasFactory;

    protected $fillable = ['from_warehouse_id', 'to_warehouse_id', 'product_id', 'subcategory_id', 'quantity', 'measure', 'entry_date', 'company_id', 'highway_id', 'highway_product_id'];

    public function fromWarehouse() :BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    public function toWarehouse() :BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
    public function product() :BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function subcategory() :BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function highway() :BelongsTo
    {
        return $this->belongsTo(Highway::class);
    }
}
