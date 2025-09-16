<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request, Category $category)
    {
        $query = $category->items();

        // search
        if ($request->q) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }

        // price filters
        if ($request->min_price) $query->where('price', '>=', $request->min_price);
        if ($request->max_price) $query->where('price', '<=', $request->max_price);

        if ($request->min_price && $request->max_price && $request->min_price > $request->max_price) {
            return response()->json(['error' => 'min_price > max_price'], 400);
        }

        // sorting
        switch ($request->sort) {
            case 'price_asc': $query->orderBy('price'); break;
            case 'price_desc': $query->orderByDesc('price'); break;
            case 'name_asc': $query->orderBy('name'); break;
            case 'name_desc': $query->orderByDesc('name'); break;
        }

        $perPage = min($request->get('per_page', 10), 50);

        return $query->paginate($perPage);
    }
}
