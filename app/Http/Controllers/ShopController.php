<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function shop(Request $request)
    {
        $size = $request->query('size', 12);
        $order = (int) $request->query('order', -1);
        $f_brands = $request->query('brands');
        $f_categories = $request->query('categories');
        $min_price = $request->query('min', 100);
        $max_price = $request->query('max', 5000);

        $sortOptions = [
            -1 => ['id', 'DESC'],
            1 => ['created_at', 'DESC'],
            2 => ['created_at', 'ASC'],
            3 => ['sale_price', 'ASC'],
            4 => ['sale_price', 'DESC'],
        ];

        [$o_column, $o_order] = $sortOptions[$order] ?? $sortOptions[-1];

        $brands = Brand::orderBy('name','ASC')->get();
        $categories = Category::orderBy('name','ASC')->get();
        $products = Product::when($f_brands, function ($query, $f_brands) {
                $query->whereIn('brand_id', explode(',', $f_brands));
            })
            ->when($f_categories, function ($query, $f_categories) {
                $query->whereIn('category_id', explode(',', $f_categories));
            })
            ->when($min_price !== null && $max_price !== null, function ($query) use ($min_price, $max_price) {
                $query->where(function ($query) use ($min_price, $max_price) {
                    $query->whereBetween('regular_price', [$min_price, $max_price])
                        ->orWhereBetween('sale_price', [$min_price, $max_price]);
                });
            })
            ->orderBy($o_column, $o_order)
            ->paginate($size);

        return view('shop', compact('products', 'size', 'order', 'brands', 'f_brands', 'categories', 'f_categories', 'min_price', 'max_price'));
    }

    public function product_details($product_slug)
    {
        $product = Product::where('slug', $product_slug)->first();

        if (! $product) {
            return redirect()->route('shop.shop')->with('error', 'Product not found.');
        }

        $rproducts = Product::where('slug', '<>', $product_slug)->take(8)->get();

        return view('details', compact('product', 'rproducts'));
    }


}
