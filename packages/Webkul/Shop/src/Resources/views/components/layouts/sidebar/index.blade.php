@inject('categoryRepository', 'Webkul\Category\Repositories\CategoryRepository')

@php
    $categories = $categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

    // Filter categories that should be shown in the push menu (Pinned by Admin)
    $pinnedCategories = $categories->filter(function ($category) {
        return $category->show_in_push_menu;
    });

    $customer = auth()->guard('customer')->user();

    // NEW: "For You" categories based on wishlist
    $personalizedCategories = collect();
    if ($customer) {
        $wishlistProductIds = $customer->wishlist_items->pluck('product_id');

        if ($wishlistProductIds->isNotEmpty()) {
            $categoryIds = \DB::table('product_categories')
                ->whereIn('product_id', $wishlistProductIds)
                ->pluck('category_id')
                ->unique();

            if ($categoryIds->isNotEmpty()) {
                $personalizedCategories = $categoryRepository->whereIn('id', $categoryIds)
                    ->where('status', 1)
                    ->get();
            }
        }
    }

    // Deduplicate: If a category is in personalized, remove it from pinned to avoid repeats
    $personalizedIds = $personalizedCategories->pluck('id');
    $displayPinnedCategories = $pinnedCategories->reject(function ($category) use ($personalizedIds) {
        return $personalizedIds->contains($category->id);
    });

    // Determine if the sidebar trigger should be shown
    $showTrigger = !request()->routeIs([
        'shop.customer.session.index',
        'shop.customers.register.index',
        'shop.customers.verify',
        'shop.customer.login.verify_identity',
    ]);
@endphp

<v-sidebar v-cloak :show-trigger="{{ $showTrigger ? 'true' : 'false' }}">
    <div class="flex flex-col h-full overflow-hidden relative">
        <!-- Close Button (Mobile Only) -->
        <div class="flex justify-end p-4 md:hidden relative z-10">
            <button @click="close"
                class="text-3xl icon-cross text-zinc-400 hover:text-purple-600 transition-colors p-2"></button>
        </div>

        @php
            $customer = auth()->guard('customer')->user();
        @endphp

        <!-- User Profile Card -->
        <div class="px-5 py-4 relative z-10">
            @if ($customer)
                <a href="{{ route('shop.customers.account.index') }}"
                    class="flex items-center w-full bg-white/70 backdrop-blur-md rounded-[24px] border border-white/40 p-4 shadow-sm transition-all active:scale-[0.98] group/card hover:bg-white/90">

                    <div class="flex-grow overflow-hidden relative z-10">
                        <div class="flex items-center gap-2">
                            <h2 class="text-[16px] font-bold text-zinc-900 tracking-tight leading-tight truncate">
                                {{ $customer->first_name }} {{ $customer->last_name }}
                            </h2>
                        </div>

                        @if ($customer->username)
                            <p class="text-[10px] font-bold text-[#7C45F5] mt-1 space-x-1 uppercase tracking-wider opacity-80">
                                <span class="text-zinc-400 font-mono">@</span>{{ $customer->username }}
                            </p>
                        @endif
                        <p class="text-zinc-500 text-[12px] leading-tight mt-1 truncate">
                            {{ $customer->email }}
                        </p>
                    </div>

                    <div
                        class="w-8 h-8 rounded-full bg-zinc-50 flex items-center justify-center text-zinc-300 group-hover/card:text-[#7C45F5] group-hover/card:bg-violet-50 transition-all shrink-0 ml-2">
                        <span class="icon-arrow-right text-lg"></span>
                    </div>
                </a>
            @else
                <div class="flex flex-col gap-3 py-2">
                    <a href="{{ route('shop.customer.session.create') }}"
                        class="flex w-full items-center justify-center rounded-[20px] bg-gradient-to-r from-[#7C45F5] to-[#7C45F5] px-6 py-4 text-center text-[15px] font-bold text-white shadow-lg shadow-purple-500/20 transition-all hover:shadow-purple-500/30 active:scale-[0.98]">
                        Войти / Регистрация
                    </a>
                </div>
            @endif
        </div>

        <!-- Scrollable Content -->
        <div class="flex-grow overflow-y-auto px-5 py-4 relative z-10 sidebar-nav-container hide-scrollbar">

            {{-- Section: FOR YOU --}}
            @if ($personalizedCategories->isNotEmpty())
                <div class="mb-8">
                    <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-[0.2em] px-2 mb-4 opacity-70">
                        @lang('shop::app.components.layouts.sidebar.for-you')
                    </p>
                    <nav class="space-y-3">
                        @foreach ($personalizedCategories as $category)
                            <a href="{{ $category->url }}"
                                class="flex items-center justify-between p-4 bg-white rounded-[20px] border border-zinc-50 shadow-[0_4px_16px_rgba(0,0,0,0.02)] transition-all hover:shadow-[0_8px_24px_rgba(0,0,0,0.04)] hover:translate-y-[-1px] active:scale-[0.98] group">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-zinc-50 flex items-center justify-center overflow-hidden shrink-0 group-hover:bg-violet-50 transition-colors">
                                        @if ($category->logo_url)
                                            <img src="{{ $category->logo_url }}"
                                                class="w-6 h-6 object-contain group-hover:scale-110 transition-transform"
                                                alt="{{ $category->name }}">
                                        @else
                                            <span class="text-xl">📦</span>
                                        @endif
                                    </div>
                                    <span class="text-[14px] font-bold text-[#7C45F5]">{{ $category->name }}</span>
                                </div>
                                <span
                                    class="icon-arrow-right text-base text-zinc-200 group-hover:text-[#7C45F5] transition-colors"></span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            @endif

            {{-- Section: CATALOG --}}
            <div class="mb-6">
                <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-[0.2em] px-2 mb-4 opacity-70">
                    @lang('shop::app.components.layouts.sidebar.catalog')
                </p>
                <nav class="space-y-1">
                    @foreach ($displayPinnedCategories as $category)
                        <a href="{{ $category->url }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl text-[14px] font-semibold text-zinc-600 hover:text-[#7C45F5] hover:bg-white/60 transition-all group">
                            <div class="flex items-center gap-3">
                                <div class="w-6 h-6 flex items-center justify-center shrink-0">
                                    @if ($category->logo_url)
                                        <img src="{{ $category->logo_url }}"
                                            class="w-5 h-5 object-contain grayscale opacity-50 group-hover:grayscale-0 group-hover:opacity-100 transition-all"
                                            alt="{{ $category->name }}">
                                    @else
                                        <span
                                            class="w-1.5 h-1.5 rounded-full bg-zinc-300 group-hover:bg-[#7C45F5] transition-colors"></span>
                                    @endif
                                </div>
                                {{ $category->name }}
                            </div>
                            <span
                                class="icon-arrow-right text-base text-zinc-200 group-hover:text-[#7C45F5] transition-colors"></span>
                        </a>
                    @endforeach

                    @if ($displayPinnedCategories->isEmpty() && $personalizedCategories->isEmpty())
                        <div class="px-4 py-8 text-center bg-white/30 rounded-2xl border border-dashed border-zinc-200">
                            <p class="text-zinc-400 text-sm">Нет категорий</p>
                        </div>
                    @endif
                </nav>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-8 pb-8 mt-auto relative z-10">
            <p
                class="text-[9px] text-zinc-400 font-bold uppercase tracking-[0.25em] text-center flex items-center justify-center gap-2.5 opacity-40">
                <span class="w-1 h-1 rounded-full bg-green-500 animate-pulse"></span>
                System Active 2.3
            </p>
        </div>
    </div>
</v-sidebar>


@pushOnce('scripts')
<script type="text/x-template" id="v-sidebar-template">
        <div class="relative">
            <!-- Floating Edge Trigger -->
            <transition name="fade">
                <button 
                    v-if="!isOpen && showTrigger"
                    @click="isOpen = true; updateBodyStyle()"
                    class="fixed left-0 top-1/2 -translate-y-1/2 z-[998] group/trigger flex items-center transition-all duration-300"
                    aria-label="Open Menu"
                >
                    <div class="bg-white/40 backdrop-blur-3xl border border-white/30 border-l-0 rounded-r-3xl py-10 px-3 shadow-[4px_0_24px_rgba(0,0,0,0.04)] transition-all group-hover/trigger:bg-white/70 group-hover/trigger:translate-x-1">
                        <span class="icon-arrow-right text-xl text-zinc-400 group-hover/trigger:text-[#7C45F5] transition-colors"></span>
                    </div>
                    
                    <!-- Text Hint -->
                    <span class="ml-3 px-4 py-2 bg-white/80 backdrop-blur-xl rounded-2xl text-[11px] font-bold text-zinc-600 uppercase tracking-[0.2em] opacity-0 -translate-x-4 group-hover/trigger:opacity-100 group-hover/trigger:translate-x-0 transition-all shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-white/40">
                        Меню
                    </span>
                </button>
            </transition>

            <aside 
                class="sidebar-container fixed top-0 bottom-0 left-0 z-[1000] w-[320px] max-md:w-[85%] bg-[#F8F7FF] shadow-[0_0_60px_rgba(0,0,0,0.05)] transition-transform duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] -translate-x-full border-r border-[#E8E4FF]"
                :class="{ 'translate-x-0': isOpen }"
            >
                <slot></slot>
            </aside>

            <div 
                v-if="isOpen"
                class="fixed inset-0 z-[999] bg-black/5 backdrop-blur-[2px] transition-opacity duration-500"
                @click="close"
            ></div>
        </div>
    </script>

<script type="module">
    app.component('v-sidebar', {
        template: '#v-sidebar-template',
        props: ['showTrigger'],
        data() {
            return {
                isOpen: false
            }
        },
        mounted() {
            window.addEventListener('keydown', this.handleEsc);
            this.$emitter.on('toggle-sidebar', () => {
                this.isOpen = !this.isOpen;
                this.updateBodyStyle();
            });
        },
        beforeUnmount() {
            window.removeEventListener('keydown', this.handleEsc);
        },
        methods: {
            close() {
                this.isOpen = false;
                this.updateBodyStyle();
            },
            handleEsc(e) {
                if (e.key === 'Escape' && this.isOpen) {
                    this.close();
                }
            },
            updateBodyStyle() {
                const wrapper = document.getElementById('content-push-wrapper');
                if (this.isOpen) {
                    const shiftWidth = window.innerWidth <= 767 ? '85%' : '320px';

                    wrapper.style.transform = `translateX(${shiftWidth}) scale(0.985)`;
                    wrapper.style.borderRadius = '32px';
                    wrapper.style.overflow = 'hidden';
                    wrapper.style.boxShadow = '0 40px 100px rgba(0,0,0,0.1)';
                    document.body.style.overflow = 'hidden';
                    document.body.style.backgroundColor = '#F0EFFF';
                } else {
                    wrapper.style.transform = 'translateX(0) scale(1)';
                    wrapper.style.borderRadius = '0';
                    wrapper.style.boxShadow = 'none';
                    setTimeout(() => {
                        if (!this.isOpen) {
                            document.body.style.overflow = '';
                            document.body.style.backgroundColor = '';
                        }
                    }, 500);
                }
            }
        }
    });
</script>

<style>
    .hide-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .hide-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.4s ease, transform 0.4s cubic-bezier(0.23, 1, 0.32, 1);
    }

    .fade-enter-from,
    .fade-leave-to {
        opacity: 0;
        transform: translateX(-30px) translateY(-50%);
    }
</style>
@endpushOnce