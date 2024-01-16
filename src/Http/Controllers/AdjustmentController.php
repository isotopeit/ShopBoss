<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Illuminate\Http\Request;
use Isotope\ShopBoss\Models\Product;
use Illuminate\Support\Facades\DB;
use Isotope\ShopBoss\Models\Adjustment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use Isotope\ShopBoss\Models\AdjustedProduct;
use Illuminate\Contracts\Support\Renderable;
use Modules\Product\Notifications\NotifyQuantityAlert;
use Isotope\ShopBoss\Http\Services\DataTables\AdjustmentsDataTable;

class AdjustmentController extends Controller
{
    public static $permissions = [
        'index'   => ['access_adjustments', 'Adjustment List'],
        'create'  => ['create_adjustments', 'Adjustment Create'],
        'store'   => ['store_adjustments', 'Adjustment Store'],
        'show'    => ['show_adjustments', 'Adjustment Show'],
        'edit'    => ['edit_adjustments', 'Adjustment Edit'],
        'update'  => ['update_adjustments', 'Adjustment Update'],
        'destroy' => ['delete_adjustments', 'Adjustment Delete'],
    ];

    public function index(AdjustmentsDataTable $dataTable) {
        abort_if(Gate::denies('access_adjustments'), 403);

        return $dataTable->render('pos::adjustment.index');
    }

    public function create() {
        abort_if(Gate::denies('create_adjustments'), 403);

        return view('pos::adjustment.create');
    }

    public function store(Request $request) {
        abort_if(Gate::denies('store_adjustments'), 403);

        $request->validate([
            'reference'   => 'required|string|max:255',
            'date'        => 'required|date',
            'note'        => 'nullable|string|max:1000',
            'product_ids' => 'required',
            'quantities'  => 'required',
            'types'       => 'required'
        ]);

        DB::transaction(function () use ($request) {
            $adjustment = Adjustment::create([
                'date' => $request->date,
                'note' => $request->note
            ]);

            foreach ($request->product_ids as $key => $id) {
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $id,
                    'quantity'      => $request->quantities[$key],
                    'type'          => $request->types[$key]
                ]);

                $product = Product::findOrFail($id);

                if ($request->types[$key] == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $request->quantities[$key]
                    ]);
                } elseif ($request->types[$key] == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $request->quantities[$key]
                    ]);
                }
            }
        });

        toast('Adjustment Created!', 'success');

        return redirect()->route('adjustments.index');
    }


    public function show(Adjustment $adjustment) {
        abort_if(Gate::denies('show_adjustments'), 403);

        return view('pos::adjustment.show', compact('adjustment'));
    }


    public function edit(Adjustment $adjustment) {
        abort_if(Gate::denies('edit_adjustments'), 403);

        return view('pos::adjustment.edit', compact('adjustment'));
    }


    public function update(Request $request, Adjustment $adjustment) {
        abort_if(Gate::denies('update_adjustments'), 403);

        $request->validate([
            'reference'   => 'required|string|max:255',
            'date'        => 'required|date',
            'note'        => 'nullable|string|max:1000',
            'product_ids' => 'required',
            'quantities'  => 'required',
            'types'       => 'required'
        ]);

        DB::transaction(function () use ($request, $adjustment) {
            $adjustment->update([
                'reference' => $request->reference,
                'date'      => $request->date,
                'note'      => $request->note
            ]);

            foreach ($adjustment->adjustedProducts as $adjustedProduct) {
                $product = Product::findOrFail($adjustedProduct->product->id);

                if ($adjustedProduct->type == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $adjustedProduct->quantity
                    ]);
                } elseif ($adjustedProduct->type == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $adjustedProduct->quantity
                    ]);
                }

                $adjustedProduct->delete();
            }

            foreach ($request->product_ids as $key => $id) {
                AdjustedProduct::create([
                    'adjustment_id' => $adjustment->id,
                    'product_id'    => $id,
                    'quantity'      => $request->quantities[$key],
                    'type'          => $request->types[$key]
                ]);

                $product = Product::findOrFail($id);

                if ($request->types[$key] == 'add') {
                    $product->update([
                        'product_quantity' => $product->product_quantity + $request->quantities[$key]
                    ]);
                } elseif ($request->types[$key] == 'sub') {
                    $product->update([
                        'product_quantity' => $product->product_quantity - $request->quantities[$key]
                    ]);
                }
            }
        });

        toast('Adjustment Updated!', 'info');

        return redirect()->route('adjustments.index');
    }


    public function destroy(Adjustment $adjustment) {
        abort_if(Gate::denies('delete_adjustments'), 403);

        $adjustment->delete();

        toast('Adjustment Deleted!', 'warning');

        return redirect()->route('adjustments.index');
    }
}
