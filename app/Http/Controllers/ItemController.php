<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request, Category $category)
    {
        $query = $category->items();

        // Search
        if ($request->q) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // Price filter
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Invalid price combination
        if ($request->min_price && $request->max_price && $request->min_price > $request->max_price) {
            return response()->json(['error' => 'min_price cannot be greater than max_price'], 400);
        }

        // Sorting
        switch ($request->sort) {
            case 'price_asc':  $query->orderBy('price', 'asc'); break;
            case 'price_desc': $query->orderBy('price', 'desc'); break;
            case 'name_asc':   $query->orderBy('name', 'asc'); break;
            case 'name_desc':  $query->orderBy('name', 'desc'); break;
        }

        // Pagination (default 10, max 50)
        $perPage = min($request->get('per_page', 10), 50);

        return $query->paginate($perPage);
    }
}
