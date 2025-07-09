<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Isotope\ShopBoss\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Isotope\ShopBoss\Models\Category;
use Isotope\ShopBoss\Http\Services\DataTables\ProductCategoriesDataTable;

class CategoriesController extends Controller
{
    public static $permissions = [
        'index'   => ['access_product_categories', 'Category List'],
        'create'  => ['create_product_categories', 'Category Create'],
        'store'   => ['store_product_categories', 'Category Store'],
        'edit'    => ['edit_product_categories', 'Category Edit'],
        'update'  => ['update_product_categories', 'Category Update'],
        'destroy' => ['delete_product_categories', 'Category Delete'],
    ];

    public function index() {
        $query = Category::search();
        
        // Filter by branch if needed
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch) {
                $query->where('branch_id', Auth::user()->branch->id);
            }
        }
        
        $categories = $query->orderByDesc('id')->paginate(15);
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }
        
        return view('shopboss::categories.index', compact('categories', 'branches'));
    }

    public function store(Request $request) {
        $rules = [
            'category_code' => 'required|unique:categories,category_code|max:32',
            'category_name' => 'required|max:32'
        ];
        
        // Add branch validation if enabled
        if (settings()->enable_branch == 1) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $request->validate($rules);

        $data = [
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ];
        
        // Add branch ID if branch system is enabled
        if (settings()->enable_branch == 1) {
            $data['branch_id'] = $request->branch_id;
        }

        Category::create($data);

        return redirect()->route('product-categories.index')->withSuccess('Product Category Created!');
    }

    public function edit($id) {
        $category = Category::findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $category->branch_id != Auth::user()->branch->id) {
                return redirect()->route('product-categories.index')->withErrors('You do not have access to this category.');
            }
        }
        
        // Get branches for dropdown
        $branches = [];
        if (settings()->enable_branch == 1) {
            $branches = Branch::all();
        }

        return view('shopboss::categories.edit', compact('category', 'branches'));
    }

    public function update(Request $request, $id) {
        $rules = [
            'category_code' => 'required|unique:categories,category_code,' . $id . ',id|max:32',
            'category_name' => 'required|max:32'
        ];
        
        // Add branch validation if enabled
        if (settings()->enable_branch == 1) {
            $rules['branch_id'] = 'required|exists:branches,id';
        }
        
        $request->validate($rules);
        
        $category = Category::findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $category->branch_id != Auth::user()->branch->id) {
                return redirect()->route('product-categories.index')->withErrors('You do not have access to this category.');
            }
        }

        $data = [
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ];
        
        // Add branch ID if branch system is enabled
        if (settings()->enable_branch == 1) {
            $data['branch_id'] = $request->branch_id;
        }

        $category->update($data);

        return redirect()->route('product-categories.index')->withSuccess('Product Category Updated!');
    }

    public function destroy($id) {
        $category = Category::findOrFail($id);
        
        // Check branch access if enabled
        if (settings()->enable_branch == 1) {
            if (Auth::user()->branch && $category->branch_id != Auth::user()->branch->id) {
                return redirect()->route('product-categories.index')->withErrors('You do not have access to this category.');
            }
        }

        if ($category->products->isNotEmpty()) {
            return redirect()->route('product-categories.index')->withErrors('Can\'t delete beacuse there are products associated with this category.');
        }

        $category->delete();

        return redirect()->route('product-categories.index')->withSuccess('Product Category Deleted!');
    }
}
