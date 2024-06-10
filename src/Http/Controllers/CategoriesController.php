<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Category;
use Illuminate\Routing\Controller;
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
        return view('shopboss::categories.index', ['categories' => Category::search()->orderBydesc('id')->paginate(15)]);
    }

    public function store(Request $request) {
        $request->validate([
            'category_code' => 'required|unique:categories,category_code|max:32',
            'category_name' => 'required|max:32'
        ]);

        Category::create([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ]);

        return redirect()->route('product-categories.index')->withSuccess('Product Category Created!');

    }

    public function edit($id) {
        $category = Category::findOrFail($id);

        return view('shopboss::categories.edit', compact('category'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'category_code' => 'required|unique:categories,category_code,' . $id . ',id|max:32',
            'category_name' => 'required|max:32'
        ]);

        Category::findOrFail($id)->update([
            'category_code' => $request->category_code,
            'category_name' => $request->category_name,
        ]);

        return redirect()->route('product-categories.index')->withSuccess('Product Category Updated!');
    }

    public function destroy($id) {
        $category = Category::findOrFail($id);

        if ($category->products->isNotEmpty()) {
            return redirect()->route('product-categories.index')->withErrors('Can\'t delete beacuse there are products associated with this category.');
        }

        $category->delete();

        return redirect()->route('product-categories.index')->withSuccess('Product Category Deleted!');
    }
}
