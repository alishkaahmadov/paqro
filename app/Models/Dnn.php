<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dnn extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'product_entry_id', 'quantity', 'pdf_file', 'entry_date'];
}
