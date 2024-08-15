<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WarehouseLog extends Model
{
    use HasFactory;

    protected $fillable = ['from_warehouse_id', 'to_warehouse_id', 'product_entry_id', 'quantity', 'entry_date'];

    public function fromWarehouse() :BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id');
    }
    public function toWarehouse() :BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id');
    }
}
