<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::active()->ordered()->get();
        return response()->json($categories);
    }

    public function show(Category $category)
    {
        return response()->json($category);
    }
}