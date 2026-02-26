@inject('categoryRepository', 'Webkul\Category\Repositories\CategoryRepository')

@php
    $categories = $categoryRepository->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id);

    // Filter categories that should be shown in the push menu (Pinned by Admin)
    $pinnedCategories = $categories->filter(function ($category) {
        return $category->show_in_push_menu;
    });

    $customer = auth()->guard('customer')->user();
    $personalizedCategories = $customer ? $categoryRepository->getPersonalizedCategoriesForCustomer($customer) : collect();

    // Deduplicate: If a category is in personalized, remove it from pinned to avoid repeats
    $personalizedIds = $personalizedCategories->pluck('id');
    $displayPinnedCategories = $pinnedCategories->reject(function($category) use ($personalizedIds) {
        return $personalizedIds->contains($category->id);
    });

    // Determine if the sidebar trigger should be shown
    $showTrigger = ! request()->routeIs([
        'shop.customer.session.index',
        'shop.customers.register.index',
        'shop.customers.verify',
        'shop.customer.login.verify_identity',
    ]);
@endphp

<v-sidebar 
    v-cloak
    :show-trigger="{{ $showTrigger ? 'true' : 'false' }}"
>
    <div class="flex flex-col h-full overflow-hidden relative">
        <!-- Inner Glow/Gradient Overlay -->
        <div
            class="absolute inset-0 bg-gradient-to-br from-white/40 via-purple-500/5 to-pink-500/5 pointer-events-none">
        </div>

        <!-- Close Button (Mobile Only) -->
        <div class="flex justify-end p-2 md:hidden relative z-10">
            <button @click="close"
                class="text-3xl icon-cross text-zinc-400 hover:text-purple-600 transition-colors p-3"></button>
        </div>

        @php
            $customer = auth()->guard('customer')->user();
        @endphp

        <!-- User Profile Card -->
        <div class="px-6 py-6 border-b border-white/10 relative z-10">
            @if ($customer)
                <a href="{{ route('shop.customers.account.index') }}"
                    class="flex items-center w-full bg-white/30 backdrop-blur-xl rounded-2xl border border-white/40 p-4 shadow-[0_8px_32px_rgba(0,0,0,0.04)] transition min-h-[84px] active:scale-[0.98] group/card hover:bg-white/50 overflow-hidden relative">
                    <!-- Subtle Brand Accent in Card -->
                    <div
                        class="absolute inset-0 bg-gradient-to-r from-purple-500/0 to-pink-500/5 opacity-0 group-hover/card:opacity-100 transition-opacity">
                    </div>

                    <div class="flex-grow overflow-hidden relative z-10">
                        <h2
                            class="text-[17px] font-bold text-zinc-900 tracking-tight leading-tight truncate group-hover/card:text-[#7C45F5] transition">
                            {{ $customer->first_name }} {{ $customer->last_name }}
                        </h2>
                        <p class="text-zinc-500 text-[13px] leading-tight mt-1 truncate">
                            {{ $customer->email }}
                        </p>
                    </div>
                    <span
                        class="icon-arrow-right text-xl text-zinc-300 shrink-0 ml-2 group-hover/card:text-[#7C45F5] transition relative z-10"></span>
                </a>
            @else
                <div class="flex flex-col gap-3">
                    <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-[0.25em] ml-1 opacity-70">Личный
                        кабинет</p>
                    <a href="{{ route('shop.customer.session.create') }}"
                        class="flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-[#7C45F5] to-[#FF4D6D] px-6 py-4 text-center text-[15px] font-bold text-white shadow-xl shadow-purple-500/25 transition-all hover:shadow-purple-500/40 active:scale-[0.98]">
                        Войти / Регистрация
                    </a>
                </div>
            @endif
        </div>

        <!-- Dynamic Category Links -->
        <div class="flex-grow overflow-y-auto px-6 py-8 relative z-10 sidebar-nav-container">
            @if ($personalizedCategories->isNotEmpty())
                <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-[0.25em] ml-5 mb-3 opacity-70">
                    @lang('shop::app.components.layouts.sidebar.for-you')
                </p>
                <nav class="space-y-1 mb-6">
                    @foreach ($personalizedCategories as $category)
                        <a href="{{ $category->url }}"
                            class="flex items-center justify-between px-4 py-2.5 rounded-xl text-[14px] font-semibold text-zinc-600 hover:text-[#7C45F5] hover:bg-white/40 transition-all group">
                            <div class="flex items-center gap-3">
                                @if ($category->logo_url)
                                    <img src="{{ $category->logo_url }}" class="w-5 h-5 object-contain group-hover:scale-110 transition-transform" alt="{{ $category->name }}">
                                @else
                                    <span class="w-1.5 h-1.5 rounded-full bg-purple-400 group-hover:bg-[#7C45F5] transition-colors shadow-[0_0_4px_rgba(124,69,245,0.4)]"></span>
                                @endif
                                {{ $category->name }}
                            </div>
                            <span class="icon-arrow-right text-base text-zinc-200 group-hover:translate-x-0.5 transition-transform"></span>
                        </a>
                    @endforeach
                </nav>
            @endif

            <p class="text-zinc-400 text-[10px] font-bold uppercase tracking-[0.25em] ml-5 mb-3 opacity-70">
                @lang('shop::app.components.layouts.sidebar.catalog')
            </p>
            <nav class="space-y-1">
                @foreach ($displayPinnedCategories as $category)
                    <a href="{{ $category->url }}"
                        class="flex items-center justify-between px-4 py-2.5 rounded-xl text-[14px] font-semibold text-zinc-600 hover:text-[#7C45F5] hover:bg-white/40 transition-all group">
                        <div class="flex items-center gap-3">
                            @if ($category->logo_url)
                                <img src="{{ $category->logo_url }}" class="w-5 h-5 object-contain group-hover:scale-110 transition-transform" alt="{{ $category->name }}">
                            @else
                                <span
                                    class="w-1.5 h-1.5 rounded-full bg-zinc-300 group-hover:bg-[#7C45F5] transition-colors"></span>
                            @endif
                            {{ $category->name }}
                        </div>
                        <span
                            class="icon-arrow-right text-base text-zinc-200 group-hover:translate-x-0.5 transition-transform"></span>
                    </a>
                @endforeach

                @if ($displayPinnedCategories->isEmpty() && $personalizedCategories->isEmpty())
                    <div class="px-4 py-8 text-center">
                        <p class="text-zinc-400 text-sm">Нет категорий для отображения</p>
                        <p class="text-zinc-300 text-[10px] mt-2 uppercase tracking-wider">Настройте их в админ-панели</p>
                    </div>
                @endif
            </nav>
        </div>

        <!-- Footer -->
        <div class="p-8 border-t border-white/10 relative z-10">
            <p
                class="text-[9px] text-zinc-500 font-bold uppercase tracking-[0.3em] text-center flex items-center justify-center gap-2.5 opacity-60">
                <span
                    class="w-1.5 h-1.5 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)] animate-pulse"></span>
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
                class="sidebar-container fixed top-0 bottom-0 left-0 z-[1000] w-[320px] max-md:w-[85%] bg-white/10 backdrop-blur-3xl shadow-[0_0_60px_rgba(0,0,0,0.05)] transition-transform duration-500 ease-[cubic-bezier(0.23,1,0.32,1)] -translate-x-full border-r border-white/20"
                :class="{ 'translate-x-0': isOpen }"
            >
                <slot></slot>
            </aside>

            <div 
                v-if="isOpen"
                class="fixed inset-0 z-[999] bg-black/5 backdrop-blur-[1px] transition-opacity duration-500"
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

                    wrapper.style.transform = `translateX(${shiftWidth}) scale(0.98)`;
                    wrapper.style.borderRadius = '32px';
                    wrapper.style.overflow = 'hidden';
                    wrapper.style.boxShadow = '0 40px 100px rgba(0,0,0,0.12)';
                    document.body.style.overflow = 'hidden';
                    document.body.style.backgroundColor = '#f8f8fa';
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