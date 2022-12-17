<?php

namespace App\Models;

use App\Models\Cgy;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'pic', 'price', 'enabled', 'desc', 'enabled_at', 'cgy_id'];

    public function cgy()
    {
        return $this->belongsTo(Cgy::class);
    }

    public function tags()
    {
        //多對多關係,有中介表
        return $this->belongsToMany(Tag::class)->withTimestamps()->withPivot(['color']);
    }
}