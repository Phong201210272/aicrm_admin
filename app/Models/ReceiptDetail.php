<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptDetail extends Model
{
    use HasFactory;

    protected $table = 'receipts_detail';

    protected $fillable = [
        'receipt_id',
        'content',
        'amount',
        'date',
    ];


}
