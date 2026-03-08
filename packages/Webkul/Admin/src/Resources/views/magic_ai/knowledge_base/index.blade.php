<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.magic_ai.knowledge_base.title')
        </x-slot>

        <div class="flex items-center justify-between">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('admin::app.magic_ai.knowledge_base.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                <a href="{{ route('admin.magic_ai.knowledge_base.create') }}" class="primary-button">
                    @lang('admin::app.magic_ai.knowledge_base.create-btn')
                </a>
            </div>
        </div>

        <x-admin::datagrid :src="route('admin.magic_ai.knowledge_base.index')" />

</x-admin::layouts>