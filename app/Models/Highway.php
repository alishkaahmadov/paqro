<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Highway extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'product_entry_id', 'quantity', 'pdf_file', 'exit_date'];
}
