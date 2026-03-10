<?php

namespace Webkul\Admin\Http\Controllers\Settings;

use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Core\Repositories\BillingEntityRepository;
use Webkul\Admin\DataGrids\Settings\BillingEntityDataGrid;

class BillingEntityController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(protected BillingEntityRepository $billingEntityRepository)
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(BillingEntityDataGrid::class)->toJson();
        }

        return view('admin::settings.billing-entities.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::settings.billing-entities.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(): JsonResponse
    {
        $this->validate(request(), [
            'name' => 'required|string',
            'inn' => 'nullable|string',
            'kpp' => 'nullable|string',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bic' => 'nullable|string',
            'settlement_account' => 'nullable|string',
            'correspondent_account' => 'nullable|string',
            'director_name' => 'nullable|string',
            'accountant_name' => 'nullable|string',
        ]);

        $data = request()->only([
            'name',
            'inn',
            'kpp',
            'address',
            'bank_name',
            'bic',
            'settlement_account',
            'correspondent_account',
            'director_name',
            'accountant_name',
            'tax_regime',
            'seal',
        ]);

        $billingEntity = $this->billingEntityRepository->create($data);

        $this->billingEntityRepository->uploadSeal(request()->all(), $billingEntity);

        // If it's the first entity being created, make it default automatically
        if ($this->billingEntityRepository->count() === 1) {
            $this->billingEntityRepository->setDefault($billingEntity->id);
        }

        return response()->json([
            'message' => trans('admin::app.settings.billing-entities.create-success'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $billingEntity = $this->billingEntityRepository->findOrFail($id);

        return view('admin::settings.billing-entities.edit', compact('billingEntity'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $this->validate(request(), [
            'name' => 'required|string',
            'inn' => 'nullable|string',
            'kpp' => 'nullable|string',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'bic' => 'nullable|string',
            'settlement_account' => 'nullable|string',
            'correspondent_account' => 'nullable|string',
            'director_name' => 'nullable|string',
            'accountant_name' => 'nullable|string',
        ]);

        $data = request()->only([
            'name',
            'inn',
            'kpp',
            'address',
            'bank_name',
            'bic',
            'settlement_account',
            'correspondent_account',
            'director_name',
            'accountant_name',
            'tax_regime',
            'seal',
        ]);

        $this->billingEntityRepository->update($data, $id);

        $billingEntity = $this->billingEntityRepository->find($id);

        $this->billingEntityRepository->uploadSeal(request()->all(), $billingEntity);

        session()->flash('success', trans('admin::app.settings.billing-entities.update-success'));

        return redirect()->route('admin.settings.billing_entities.index');
    }

    /**
     * Set the specified billing entity as default.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function setDefault($id): JsonResponse
    {
        $this->billingEntityRepository->setDefault($id);

        return response()->json([
            'message' => trans('admin::app.settings.billing-entities.default-success'),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $billingEntity = $this->billingEntityRepository->findOrFail($id);

        if ($billingEntity->is_default) {
            return response()->json([
                'message' => trans('admin::app.settings.billing-entities.delete-default-error'),
            ], 400);
        }

        if ($billingEntity->categories()->count() > 0) {
            return response()->json([
                'message' => trans('admin::app.settings.billing-entities.delete-category-error'),
            ], 400);
        }

        try {
            $this->billingEntityRepository->delete($id);

            return response()->json([
                'message' => trans('admin::app.settings.billing-entities.delete-success'),
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => trans('admin::app.settings.billing-entities.delete-failed'),
            ], 500);
        }
    }
}
