<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.magic_ai.knowledge-base.create-title')
        </x-slot>

        <x-admin::form :action="route('admin.magic_ai.knowledge_base.store')">
            <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
                <p class="text-xl font-bold text-gray-800 dark:text-white">
                    @lang('admin::app.magic_ai.knowledge-base.create-title')
                </p>

                <div class="flex items-center gap-x-2.5">
                    <a href="{{ route('admin.magic_ai.knowledge_base.index') }}"
                        class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
                        @lang('admin::app.account.edit.back-btn')
                    </a>

                    <button type="submit" class="primary-button">
                        @lang('admin::app.magic_ai.knowledge-base.create.save-btn')
                    </button>
                </div>
            </div>

            <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
                <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">
                    <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.magic_ai.knowledge-base.create.general')
                        </p>

                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.magic_ai.knowledge-base.create.title')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="text" name="title" :value="old('title')"
                                :label="trans('admin::app.magic_ai.knowledge-base.create.title')"
                                :placeholder="trans('admin::app.magic_ai.knowledge-base.create.title')" />

                            <x-admin::form.control-group.error control-name="title" />
                        </x-admin::form.control-group>

                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.magic_ai.knowledge-base.create.content')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control type="textarea" name="content" rules="required"
                                :value="old('content')"
                                :label="trans('admin::app.magic_ai.knowledge-base.create.content')"
                                :placeholder="trans('admin::app.magic_ai.knowledge-base.create.content')" rows="10" />

                            <x-admin::form.control-group.error control-name="content" />
                        </x-admin::form.control-group>
                    </div>
                </div>
            </div>
        </x-admin::form>
</x-admin::layouts>