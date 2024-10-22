<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductEntry extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'subcategory_id', 'company_id', 'warehouse_id', 'quantity', 'entry_date'];

    public function product() :BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function subcategory() :BelongsTo
    {
        return $this->belongsTo(Subcategory::class, 'subcategory_id', 'id');
    }
    public function company() :BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
    public function warehouse() :BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }
}
