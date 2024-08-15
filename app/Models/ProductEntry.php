<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductEntry extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'company_id', 'warehouse_id', 'quantity', 'entry_date'];

    public function product() :BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function warehouse() :BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function warehouse_logs(): HasMany
    {
        return $this->hasMany(WarehouseLog::class, 'product_entry_id');
    }
}
