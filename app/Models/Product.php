<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'description', 'subcategory_id'];

    public function subcategory() :BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
    public function product_entries(): HasMany
    {
        return $this->hasMany(ProductEntry::class, 'product_id');
    }
}
