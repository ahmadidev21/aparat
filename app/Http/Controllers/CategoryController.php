<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\Category\AllCategoryRequest;

class CategoryController extends Controller
{
    public function getAllCategories(AllCategoryRequest $request)
    {
        $categories = Category::all();

        return $categories;
    }

    public function getMyCategories()
    {
        $categories = Category::where('user_id', auth()->id())->get();
        return $categories;
    }
}
