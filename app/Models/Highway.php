<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Highway extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'belong_to_warehouse_id'];

    public function products(): HasMany
    {
        return $this->hasMany(HighwayProduct::class, 'highway_id');
    }

    public function belongWarehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class, 'belong_to_warehouse_id');
    }
}
