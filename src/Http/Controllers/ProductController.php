<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\Product;
use Isotope\ShopBoss\Models\Category;
use Isotope\ShopBoss\Models\Branch;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
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
        $query = Product::query()
                ->join('categories','categories.id','products.category_id')
                ->selectRaw('products.*,categories.category_name');
                
        // Filter by branch if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('products.branch_id', Auth::user()->branch->id);
            }
        }
        
        $products = $query->search()
                ->orderBydesc('products.id')
                ->paginate(15);
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
                        
        return view('shopboss::products.index', compact('products', 'branches'));
    }

    public function create()
    {
        $categories = Category::all();
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            // Apply branch filter to categories if user has a branch
            if (Auth::user()->branch) {
                $categories = $categories->where('branch_id', Auth::user()->branch->id);
            }
            $branches = Branch::all();
        }
        
        return view('shopboss::products.create', compact('categories', 'branches'));
    }

    public function store(Request $request)
    {
        try 
        {
            $rules = [
                "category_id"         => 'required',
                "product_name"        => 'required|max:32',
                "product_code"        => 'required|max:28',
                "product_cost"        => 'required',
                "product_price"       => 'required',
                "uom"                 => 'required|max:16',
                "product_stock_alert" => 'required',
                "product_note"        => 'nullable|max:100',
            ];
            
            // Add branch validation if enabled
            if (settings()->enable_branch == 1) {
                $rules['branch_id'] = 'required|exists:branches,id';
            }
            
            $request->validate($rules);
            
            $req = $request->all();
            $category = Category::findOrFail($req['category_id']);
            
            // Check if user can access this category's branch
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $category->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('products.create')
                        ->withErrors('You do not have access to create products in this category.');
                }
            }

            $data = [
                "category_id"         => $category->id,
                "category_name"       => $category->category_name,
                "product_name"        => $req['product_name'],
                "product_code"        => $req['product_code'],
                "product_cost"        => $req['product_cost'],
                "product_price"       => $req['product_price'],
                "uom"                 => $req['uom'],
                "product_stock_alert" => $req['product_stock_alert'],
                "product_note"        => $req['product_note'],
            ];
            
            // Add branch ID if branch system is enabled
            if (settings()->enable_branch == 1) {
                $data['branch_id'] = $req['branch_id'];
            }
            
            $product = Product::create($data);
    
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
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $product->branch_id != Auth::user()->branch->id) {
                return redirect()->route('products.index')
                    ->withErrors('You do not have access to view this product.');
            }
        }
        
        return view('shopboss::products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $product->branch_id != Auth::user()->branch->id) {
                return redirect()->route('products.index')
                    ->withErrors('You do not have access to edit this product.');
            }
        }
        
        $categories = Category::all();
        
        // Filter categories by branch if needed
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $categories = $categories->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::products.edit', compact('product', 'categories', 'branches'));
    }

    public function update(Request $request, Product $product)
    {
        try 
        {
            // Check branch access if enabled
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $product->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('products.index')
                        ->withErrors('You do not have access to edit this product.');
                }
            }
            
            $rules = [
                "category_id"         => 'required',
                "product_name"        => 'required|max:32',
                "product_code"        => 'required|max:28',
                "product_cost"        => 'required',
                "product_price"       => 'required',
                "uom"                 => 'required|max:16',
                "product_stock_alert" => 'required',
                "product_note"        => 'nullable|max:100',
            ];
            
            // Add branch validation if enabled
            if (settings()->enable_branch == 1) {
                $rules['branch_id'] = 'required|exists:branches,id';
            }
            
            $request->validate($rules);
            
            $req = $request->all();
            $category = Category::findOrFail($req['category_id']);
            
            // Check if user can access this category's branch
            if (settings()->enable_branch == 1) {
                if (Auth::user()->branch && $category->branch_id != Auth::user()->branch->id) {
                    return redirect()->route('products.edit', $product->uuid)
                        ->withErrors('You do not have access to use this category.');
                }
            }
            
            $data = [
                "category_id"         => $category->id,
                "category_name"       => $category->category_name,
                "product_name"        => $req['product_name'],
                "product_code"        => $req['product_code'],
                "product_cost"        => $req['product_cost'],
                "product_price"       => $req['product_price'],
                "uom"                 => $req['uom'],
                "product_stock_alert" => $req['product_stock_alert'],
                "product_note"        => $req['product_note'],
            ];
            
            // Add branch ID if branch system is enabled
            if (settings()->enable_branch == 1) {
                $data['branch_id'] = $req['branch_id'];
            }
            
            $product->update($data);
            
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
            return redirect()->back()->withErrors($e->getMessage());
        }
    }

    public function destroy(Product $product)
    {
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $product->branch_id != Auth::user()->branch->id) {
                return redirect()->route('products.index')
                    ->withErrors('You do not have access to delete this product.');
            }
        }
        
        $product->delete();

        return redirect()->route('products.index')->withSuccess('Product Deleted!');
    }
}
