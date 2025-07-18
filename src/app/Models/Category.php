<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    protected $table = 'categories'; 

    public $timestamps = true; 

    public function goods()
    {
        return $this->belongsToMany(Good::class, 'category_good');
    }
}
