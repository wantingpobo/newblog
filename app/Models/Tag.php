<?php

namespace App\Models;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'type'];

    public function items()
    {
        //多對多關係,有中介表
        return $this->belongsToMany(Item::class);
    }
}