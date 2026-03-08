<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Webkul\MagicAI\Repositories\KnowledgeItemRepository;
use Webkul\MagicAI\Repositories\EmbeddingRepository;
use Webkul\MagicAI\Services\EmbeddingService;

class KnowledgeBaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected KnowledgeItemRepository $knowledgeItemRepository,
        protected EmbeddingRepository $embeddingRepository,
        protected EmbeddingService $embeddingService
    ) {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(\Webkul\Admin\DataGrids\MagicAI\KnowledgeBaseDataGrid::class)->toJson();
        }

        return view('admin::magic_ai.knowledge_base.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin::magic_ai.knowledge_base.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'title' => 'nullable',
            'content' => 'required',
        ]);

        $item = $this->knowledgeItemRepository->create(request()->only([
            'title',
            'content',
        ]));

        // Generate embedding
        $this->generateEmbedding($item);

        session()->flash('success', trans('magic_ai::app.knowledge_base.create-success'));

        return redirect()->route('admin.magic_ai.knowledge_base.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        $item = $this->knowledgeItemRepository->findOrFail($id);

        return view('admin::magic_ai.knowledge_base.edit', compact('item'));
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
            'title' => 'nullable',
            'content' => 'required',
        ]);

        $item = $this->knowledgeItemRepository->update(request()->only([
            'title',
            'content',
        ]), $id);

        // Update embedding
        $this->generateEmbedding($item);

        session()->flash('success', trans('magic_ai::app.knowledge_base.update-success'));

        return redirect()->route('admin.magic_ai.knowledge_base.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->knowledgeItemRepository->delete($id);

        return response()->json(['message' => trans('magic_ai::app.knowledge_base.delete-success')]);
    }

    /**
     * Generate and store embedding for a knowledge item.
     */
    protected function generateEmbedding($item)
    {
        $model = core()->getConfigData('general.magic_ai.knowledge_base.embedding_model') ?: 'nomic-embed-text';

        $vector = $this->embeddingService->getEmbedding($item->content, $model);

        if (!empty($vector)) {
            $this->embeddingRepository->updateOrCreate(
                ['ai_knowledge_item_id' => $item->id, 'model' => $model],
                ['embedding' => json_encode($vector)]
            );
        }
    }
}
