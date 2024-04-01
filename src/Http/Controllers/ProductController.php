<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Category;
use Illuminate\Support\Facades\Storage;
use Isotope\ShopBoss\Http\Requests\StoreProductRequest;
use Isotope\ShopBoss\Http\Requests\UpdateProductRequest;
use Isotope\ShopBoss\Http\Services\DataTables\ProductDataTable;

class ProductController extends Controller
{
    public static $permissions = [
        'index'   => ['access_products', 'Product List'],
        'create'  => ['create_products', 'Product Create'],
        'store'   => ['store_products', 'Product Store'],
        'show'    => ['show_products', 'Product Show'],
        'edit'    => ['edit_products', 'Product Edit'],
        'update'  => ['update_products', 'Product Update'],
        'destroy' => ['delete_products', 'Product Delete'],
    ];

    public function index()
    {
        $products = Product::query()
                        ->join('categories','categories.id','products.category_id')
                        ->selectRaw('products.*,categories.category_name')
                        ->search()
                        ->orderBydesc('products.id')
                        ->paginate(15);
                        
        return view('shopboss::products.index',compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('shopboss::products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        try 
        {
            $request->validate([
                "category_id"         => 'required',
                "product_name"        => 'required|max:32',
                "product_code"        => 'required|max:28',
                "product_cost"        => 'required',
                "product_price"       => 'required',
                "uom"                 => 'required|max:16',
                "product_stock_alert" => 'required',
                "product_note"        => 'nullable|max:100',
            ]);
            $req      = $request->all();
            $category = Category::findOrFail($req['category_id']);

            $product  = Product::create([
                "category_id"         => $category->id,
                "category_name"       => $category->category_name,
                "product_name"        => $req['product_name'],
                "product_code"        => $req['product_code'],
                "product_cost"        => $req['product_cost'],
                "product_price"       => $req['product_price'],
                "uom"                 => $req['uom'],
                "product_stock_alert" => $req['product_stock_alert'],
                "product_note"        => $req['product_note'],
            ]);
    
            if ($request->has('document')) {
                foreach ($request->input('document', []) as $file) {
                    $filePath = Storage::path('public/dropzone/' . $file);
                    if (file_exists($filePath)) {
                        $product->uploads()->create([
                            "folder" => '/storage/dropzone/',
                            "filename" => $file
                        ]);
                    }
                }
            }
            return redirect()->route('products.index')->withSuccess('Product Created!');
        } 
        catch (Exception $e) {
            return redirect()->route('products.create')->withErrors($e->getMessage());
        }
    }

    public function show(Product $product)
    {
        return view('shopboss::products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $categories = Category::all();
        return view('shopboss::products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        try 
        {
            $request->validate([
                "category_id"         => 'required',
                "product_name"        => 'required|max:32',
                "product_code"        => 'required|max:28',
                "product_cost"        => 'required',
                "product_price"       => 'required',
                "uom"                 => 'required|max:16',
                "product_stock_alert" => 'required',
                "product_note"        => 'nullable|max:100',
            ]);
            $req      = $request->all();
            $category = Category::findOrFail($req['category_id']);
            $product->update([
                "category_id"         => $category->id,
                "category_name"       => $category->category_name,
                "product_name"        => $req['product_name'],
                "product_code"        => $req['product_code'],
                "product_cost"        => $req['product_cost'],
                "product_price"       => $req['product_price'],
                "uom"                 => $req['uom'],
                "product_stock_alert" => $req['product_stock_alert'],
                "product_note"        => $req['product_note'],
            ]);
            if ($request->has('document')) {
                $documents = $request->input('document', []);
                if(count($documents) > 0) {
                    $product->uploads->map(fn($u)=> $u->delete());
                }
                foreach ($documents as $file) {
                    $filePath = Storage::path('public/dropzone/' . $file);
                    if (file_exists($filePath)) {
                        $product->uploads()->create([
                            "folder" => '/storage/dropzone/',
                            "filename" => $file
                        ]);
                    }
                }
            }
            return redirect()->route('products.index')->withSuccess('Product Updated!');
        } 
        catch (Exception $e) {
            dd($e);
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('products.index')->withSuccess('Product Deleted!');
    }
}
