@extends('shop::customers.account.index')

@section('page_title')
    Приглашение на встречу
@endsection

@section('account-content')
    <div class="flex flex-col items-center justify-center min-h-[60vh] px-4">
        <div class="w-full max-w-md bg-white border-4 border-black p-8 md:p-12 shadow-[12px_12px_0px_0px_rgba(0,0,0,1)] rotate-1 transition-transform hover:rotate-0">
            <div class="mb-10 flex justify-center">
                 <div class="w-20 h-20 bg-[#7C45F5] border-4 border-black flex items-center justify-center -rotate-6 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)]">
                     <span class="text-4xl">📸</span>
                 </div>
            </div>

            <h1 class="text-4xl font-black uppercase tracking-tighter leading-none mb-6 text-black">
                Вас пригласили <br/>на встречу
            </h1>

            <p class="text-lg font-bold text-black/60 mb-10 leading-snug text-center">
                Безопасный видеозвонок с крипто-защитой. <br/>
                Присоединяйтесь в один клик.
            </p>

            <div class="flex flex-col gap-4">
                <a href="{{ route('shop.customer.register.index') }}" 
                   class="w-full text-center px-8 py-5 bg-[#7C45F5] text-white text-xl font-black uppercase tracking-widest border-4 border-black shadow-[8px_8px_0px_0px_rgba(0,0,0,1)] active:shadow-none active:translate-x-1 active:translate-y-1 transition-all">
                    Войти и начать
                </a>
                
                <p class="text-center text-[10px] font-black uppercase tracking-[0.2em] text-black/40 mt-4">
                    Не требует установки приложений
                </p>
            </div>
        </div>
    </div>
@endsection
