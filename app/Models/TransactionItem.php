<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionItem extends Model
{
    use HasFactory;
    protected $primaryKey = 'item_id';
    protected $table      = 'transaction_item';
    protected $guarded    = [];
    public $timestamps    = false;
}
