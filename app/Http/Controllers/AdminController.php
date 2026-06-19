<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.admin');
    }

    // Brand in admin page
    public function brands()
    {
        $brands = Brand::orderBy('id','DESC')->paginate(10);//capital Brand is model name
        return view('admin.brands',compact('brands'));
    }

    // Add Product in Brand 
    public function add_brand()
    {
        return view('admin.brand-add');
    }

    public function brand_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = new Brand();

        $brand->name = $request->name;
        $brand->slug = str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = carbon::now()->timestamp.'.'.$file_extention;
        $this->GenerateBrandThumbailsImage($image,$file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status','Brand has been added succesfully!!');
    }

    public function brand_edit($id)
    {
        $brand = Brand::find($id);//capital Brand is model name
        return view('admin.brand-edit',compact('brand'));
    }

    public function brand_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $brand = Brand::find($request->id);//capital Brand is model name
        $brand->name = $request->name;
        $brand->slug = str::slug($request->name);
        if($request->hasFile('image')){ 
             if (File::exists(public_path('uploads/brands').'/'.$brand->image))
             {
                  File::delete(public_path('uploads/brands').'/'.$brand->image);
             }

             $image = $request->file('image');
             $file_extention = $request->file('image')->extension();
             $file_name = carbon::now()->timestamp.'.'.$file_extention;
             $this->GenerateBrandThumbailsImage($image,$file_name);
             $brand->image = $file_name;
    }
        $brand->save();
        return redirect()->route('admin.brands')->with('status','Brand has been updated succesfully!!');
    }

    public function GenerateBrandThumbailsImage($image,$imagename)
    {
        $destinationPath = public_path('uploads/brands');
        $this->saveUploadedImage($image, $imagename, $destinationPath, null, 124, 124);

    }

    public function brand_delete($id){
        $brand = Brand::find($id);//capital Brand is model name
        if(File::exists(public_path('uploads/brands').'/'.$brand->image))
        {
             File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Brand has been deleted succesfully!!');
    }

// Category Start Here In admin page

    public function categories(){
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.categories',compact('categories'));
    }

    public function category_add(){
        $categories = Category::orderBy('id','DESC')->paginate(10);
        return view('admin.category-add');
    }

    public function category_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = str::slug($request->name);
        $image = $request->file('image');
        $file_extention = $request->file('image')->extension();
        $file_name = carbon::now()->timestamp.'.'.$file_extention;
        $this->GenerateCategoryThumbailsImage($image,$file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status','category has been added succesfully!!');
    }

    public function GenerateCategoryThumbailsImage($image,$imagename)
    {
        $destinationPath = public_path('uploads/categories');
        $this->saveUploadedImage($image, $imagename, $destinationPath, null, 124, 124);

    }

    public function category_edit($id)
    {
        $category = Category::find($id);//capital Category is model name
        return view('admin.category-edit',compact('category'));
    }

    public function category_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);

        $category = Category::find($request->id);//capital Category is model name
        $category->name = $request->name;
        $category->slug = str::slug($request->name);
        if($request->hasFile('image')){ 
             if (File::exists(public_path('uploads/categories').'/'.$category->image))
             {
                  File::delete(public_path('uploads/categories').'/'.$category->image);
             }

             $image = $request->file('image');
             $file_extention = $request->file('image')->extension();
             $file_name = carbon::now()->timestamp.'.'.$file_extention;
             $this->GenerateCategoryThumbailsImage($image,$file_name);
             $category->image = $file_name;
        }
            $category->save();
            return redirect()->route('admin.categories')->with('status','Category has been updated succesfully!!');
    }

    public function category_delete($id){
        $category = Category::find($id);//capital Category is model name 
        if(File::exists(public_path('uploads/categories').'/'.$category->image))
        {
             File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status','Category has been deleted succesfully!!');
    }

// Product Start Here In admin page

    public function  products()
    {
      $products = Product::orderBy('created_at','DESC')->paginate(10);
       return view('admin.products',compact('products'));
    }

    public function  product_add()
    {
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-add',compact('categories','brands'));
    }

    public function product_store(Request $request)
        {
             $request->validate([
                     'name' => 'required',
                     'slug' => 'required|unique:products,slug',
                     'short_description' => 'required',
                     'description' => 'required',
                     'regular_price' => 'required',
                     'sale_price' => 'required',
                     'SKU' => 'required',
                     'stock_status' => 'required',
                     'featured' => 'required',
                     'quantity' => 'required',
                     'image' => 'required|mimes:png,jpg,jpeg|max:2048',
                     'category_id' => 'required|integer|exists:categories,id',
                     'brand_id' => 'required|integer|exists:brands,id'
                 ]);

         $product = new Product();
         $product->name = $request->name;
         $product->slug = Str::slug($request->name);
         $product->short_description = $request->short_description;
         $product->description = $request->description;
         $product->regular_price = $request->regular_price;
         $product->sale_price = $request->sale_price;
         $product->SKU = $request->SKU;
         $product->stock_status = $request->stock_status;
         $product->featured = $request->featured;
         $product->quantity = $request->quantity;
         $product->category_id = $request->category_id;
         $product->brand_id = $request->brand_id;

         $current_timestamp = Carbon::now()->timestamp;

            if($request->hasFile('image'))
            {
                $image = $request->file('image');
                $imagename = $current_timestamp . '.' . $image->extension();
                $this->GenerateproductThumbailImage($image,$imagename);
                $product->image = $imagename;
            }

            $gallery_arr = array();
            $gallery_images = "";
            $counter = 1;

            if($request->hasFile('images'))
            {
                $allowedfileExtion = ['jpg','png','jpeg'];
                $files = $request->file('images');
                foreach($files as $file)
                {
                    $gextension = $file->getClientOriginalExtension();
                    $gcheck = in_array($gextension,$allowedfileExtion);
                    if($gcheck)
                    {
                        $gfilename = $current_timestamp . "-" . $counter . "." . $gextension;
                        $this->GenerateproductThumbailImage($file,$gfilename);
                        array_push($gallery_arr,$gfilename);
                        $counter = $counter + 1;
                    }
                }
                $gallery_images = implode(',',$gallery_arr);
            }
            $product->images = $gallery_images;
            $product->save();
            return  redirect()->route('admin.products')->with('status','Product has been added successfully!!');
        }

    public function GenerateproductThumbailImage($image,$imageName)
    {
        $destinationPath = public_path('uploads/products');
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $this->saveUploadedImage($image, $imageName, $destinationPath, $destinationPathThumbnail, 540, 689, 104, 104);

    }

    private function saveUploadedImage($image, $imageName, $destinationPath, $thumbPath = null, $width = null, $height = null, $thumbWidth = null, $thumbHeight = null)
    {
        if (! File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        if ($thumbPath && ! File::exists($thumbPath)) {
            File::makeDirectory($thumbPath, 0755, true);
        }

        $destinationFile = $destinationPath . '/' . $imageName;

        try {
            if (extension_loaded('gd') || extension_loaded('imagick')) {
                $img = Image::make($image->path());

                if ($width && $height) {
                    $img->fit($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                $img->save($destinationFile);

                if ($thumbPath) {
                    $thumb = Image::make($image->path());
                    if ($thumbWidth && $thumbHeight) {
                        $thumb->fit($thumbWidth, $thumbHeight, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        });
                    }
                    $thumb->save($thumbPath . '/' . $imageName);
                }
                return;
            }
        } catch (\Throwable $e) {
            // Fall back to a direct file move when image driver isn't available.
        }

        $image->move($destinationPath, $imageName);
        if ($thumbPath) {
            copy($destinationFile, $thumbPath . '/' . $imageName);
        }
    }

    public function product_edit($id)
    {
        $product = Product::find($id);
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-edit',compact('product','categories','brands'));

    }

    public function product_update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required',
            'featured' => 'required',
            'quantity' => 'required',
            'image' => 'mimes:png,jpg,jpeg|max:2048',
            'category_id' => 'required|integer|exists:categories,id',
            'brand_id' => 'required|integer|exists:brands,id'
        ]);

        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = Str::slug($request->name);
        $product->short_description = $request->short_description;
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->featured = $request->featured;
        $product->quantity = $request->quantity;
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image'))
        {
            if(File::exists(public_path('uploads/products').'/'.$product->image))
            {
                File::delete(public_path('uploads/products').'/'.$product->image);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image))
            {
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }

            $image = $request->file('image');
            $imagename = $current_timestamp . '.' . $image->extension();
            $this->GenerateproductThumbailImage($image,$imagename);
            $product->image = $imagename;
        }

        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;

        if($request->hasFile('images'))
        {
            foreach(explode(',',$product->images) as $ofile)
            {
                if(File::exists(public_path('uploads/products').'/'.$ofile))
                {
                    File::delete(public_path('uploads/products').'/'.$ofile);
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile))
                {
                    File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
                } 
            }

            $allowedfileExtion = ['jpg','png','jpeg'];
            $files = $request->file('images');
            foreach($files as $file)
            {
                $gextension = $file->getClientOriginalExtension();
                $gcheck = in_array($gextension,$allowedfileExtion);
                if($gcheck)
                {
                    $gfilename = $current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateproductThumbailImage($file,$gfilename);
                    array_push($gallery_arr,$gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(',',$gallery_arr);
            $product->images = $gallery_images;

        }
        $product->save();
        return  redirect()->route('admin.products')->with('status','Product has been added successfully!!');
    }

    public function product_delete($id)
    {
        $product = Product::find($id);
        if(File::exists(public_path('uploads/products').'/'.$product->image))
            {
                File::delete(public_path('uploads/products').'/'.$product->image);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image))
            {
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }

            foreach(explode(',',$product->images) as $ofile)
            {
                if(File::exists(public_path('uploads/products').'/'.$ofile))
                {
                    File::delete(public_path('uploads/products').'/'.$ofile);
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile))
                {
                    File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
                } 
            }
        $product->delete();
        return  redirect()->route('admin.products')->with('status','Product has been deleted successfully!!');
    
    }

    public function coupons()
    {
      $coupons = Coupon::orderBy('expiry_date','DESC')->paginate(10);
      return view('admin.coupons',compact('coupons'));
    }

    public function coupon_add()
    {
        return view('admin.coupon-add');
    }

    public function coupon_store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'type' => 'required',
            'value' => 'required|numeric',
            'cart_value' => 'required|numeric',
            'expiry_date' => 'required|date',
        ]);
        $coupon = new Coupon();
        $coupon->code = $request->code;
        $coupon->type = $request->type;
        $coupon->value = $request->value;
        $coupon->cart_value = $request->cart_value;
        $coupon->expiry_date = $request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupons')->with('status','Coupon Added Successfully!!');
    }

    public function coupon_delete($id)
    {
        $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupons')->with('status','Coupon Has Been Deleted Successfully!!');

    }



    

}
