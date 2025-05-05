<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetSubcategory extends Model
{
    use HasFactory;

    public function  listCategory()
    {
        $subcategoryList = AssetSubCategory::select('id', 'name')
            ->where('status', 1)
            ->orderBy('name')
            ->get();
        return $subcategoryList;
    }
}
