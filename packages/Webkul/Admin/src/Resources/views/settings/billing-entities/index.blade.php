<x-admin::layouts>
    <!-- Title of the page -->
    <x-slot:title>
        @lang('admin::app.settings.billing-entities.index.title')
        </x-slot>

        <div class="flex items-center justify-between mt-4">
            <p class="text-xl text-gray-800 dark:text-white font-bold">
                @lang('admin::app.settings.billing-entities.index.title')
            </p>

            <div class="flex items-center gap-x-[10px]">
                @if (bouncer()->hasPermission('settings.billing_entities.create'))
                    <a href="{{ route('admin.settings.billing_entities.create') }}"
                        class="primary-button relative overflow-hidden flex gap-[6px] items-center max-w-max mx-auto px-[12px] py-[6px] rounded-[6px] text-white">
                        @lang('admin::app.settings.billing-entities.index.create-btn')
                    </a>
                @endif
            </div>
        </div>

        <!-- DataGrid component -->
        <x-admin::datagrid :src="route('admin.settings.billing_entities.index')"></x-admin::datagrid>
</x-admin::layouts>