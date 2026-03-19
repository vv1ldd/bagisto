<?php

use Illuminate\Support\Facades\Route;
use Webkul\Admin\Http\Controllers\KnowledgeBaseController;

Route::get('magic-ai/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('admin.magic_ai.knowledge_base.index');
Route::get('magic-ai/knowledge-base/create', [KnowledgeBaseController::class, 'create'])->name('admin.magic_ai.knowledge_base.create');
Route::post('magic-ai/knowledge-base/create', [KnowledgeBaseController::class, 'store'])->name('admin.magic_ai.knowledge_base.store');
Route::get('magic-ai/knowledge-base/edit/{id}', [KnowledgeBaseController::class, 'edit'])->name('admin.magic_ai.knowledge_base.edit');
Route::put('magic-ai/knowledge-base/edit/{id}', [KnowledgeBaseController::class, 'update'])->name('admin.magic_ai.knowledge_base.update');
Route::delete('magic-ai/knowledge-base/delete/{id}', [KnowledgeBaseController::class, 'destroy'])->name('admin.magic_ai.knowledge_base.delete');
