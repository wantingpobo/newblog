<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cgy extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'pic', 'sort'];

    public function items()
    {
        //一個分類有多個品項
        return $this->hasMany(Item::class);
    }
}