<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function product_entries(): HasMany
    {
        return $this->hasMany(ProductEntry::class, 'subcategory_id');
    }
}
