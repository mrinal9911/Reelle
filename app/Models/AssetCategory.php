<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    public function  listCategory()
    {
        $categoryList =  AssetCategory::select('id', 'name')
            ->where('status',1)
            ->orderBy('id')
            ->get();
        return $categoryList;
    }
}
