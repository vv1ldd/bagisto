<x-shop::dropdown position="bottom-left">
    <x-slot:toggle>
        <button class="flex w-full max-w-[200px] cursor-pointer items-center justify-between gap-4 border border-zinc-200 bg-white py-2.5 px-4 text-[14px] font-medium text-zinc-600 transition-all hover:bg-zinc-50 hover:border-zinc-300 focus:border-[#7C45F5] rounded-2xl">
            <span v-text="applied.pagination.perPage"></span>

            <span class="icon-arrow-down text-2xl text-zinc-400"></span>
        </button>
    </x-slot>

    <x-slot:menu class="max-md:!py-0 rounded-2xl border-zinc-200 shadow-xl overflow-hidden">
        <x-shop::dropdown.menu.item
            v-for="perPageOption in available.meta.per_page_options"
            v-text="perPageOption"
            class="text-[14px] py-2 px-4 hover:bg-zinc-50 hover:text-[#7C45F5] transition-colors"
            @click="changePerPageOption(perPageOption)"
        />
    </x-slot>
</x-shop::dropdown>
