@push('scripts')
    <script>
    // ═══════════════════════════════════════════════════════════════════════
    // CRYPTO ADDRESS VALIDATORS — per-network checksum verification
    // ═══════════════════════════════════════════════════════════════════════

    // ── Tiny SHA-256 (browser crypto-free, synchronous) ──────────────────
    const SHA256 = (() => {
        const K=[0x428a2f98,0x71374491,0xb5c0fbcf,0xe9b5dba5,0x3956c25b,0x59f111f1,0x923f82a4,0xab1c5ed5,0xd807aa98,0x12835b01,0x243185be,0x550c7dc3,0x72be5d74,0x80deb1fe,0x9bdc06a7,0xc19bf174,0xe49b69c1,0xefbe4786,0x0fc19dc6,0x240ca1cc,0x2de92c6f,0x4a7484aa,0x5cb0a9dc,0x76f988da,0x983e5152,0xa831c66d,0xb00327c8,0xbf597fc7,0xc6e00bf3,0xd5a79147,0x06ca6351,0x14292967,0x27b70a85,0x2e1b2138,0x4d2c6dfc,0x53380d13,0x650a7354,0x766a0abb,0x81c2c92e,0x92722c85,0xa2bfe8a1,0xa81a664b,0xc24b8b70,0xc76c51a3,0xd192e819,0xd6990624,0xf40e3585,0x106aa070,0x19a4c116,0x1e376c08,0x2748774c,0x34b0bcb5,0x391c0cb3,0x4ed8aa4a,0x5b9cca4f,0x682e6ff3,0x748f82ee,0x78a5636f,0x84c87814,0x8cc70208,0x90befffa,0xa4506ceb,0xbef9a3f7,0xc67178f2];
        function hash(msg) {
            let H=[0x6a09e667,0xbb67ae85,0x3c6ef372,0xa54ff53a,0x510e527f,0x9b05688c,0x1f83d9ab,0x5be0cd19];
            msg=Array.from(msg);
            const l=msg.length*8;
            msg.push(0x80);
            while((msg.length%64)!==56)msg.push(0);
            for(let i=7;i>=0;i--)msg.push((l/(2**(i*8)))&0xFF);
            for(let c=0;c<msg.length;c+=64){
                const W=[];
                for(let i=0;i<16;i++)W[i]=(msg[c+i*4]<<24)|(msg[c+i*4+1]<<16)|(msg[c+i*4+2]<<8)|msg[c+i*4+3];
                for(let i=16;i<64;i++){const s0=((W[i-15]>>>7)|(W[i-15]<<25))^((W[i-15]>>>18)|(W[i-15]<<14))^(W[i-15]>>>3);const s1=((W[i-2]>>>17)|(W[i-2]<<15))^((W[i-2]>>>19)|(W[i-2]<<13))^(W[i-2]>>>10);W[i]=(W[i-16]+s0+W[i-7]+s1)>>>0;}
                let[a,b,d,e,f,g,h,i]=[...H,H[6],H[7]];
                for(let j=0;j<64;j++){const S1=((f>>>6)|(f<<26))^((f>>>11)|(f<<21))^((f>>>25)|(f<<7));const ch=(f&g)^(~f&h);const t1=(i+S1+ch+K[j]+W[j])>>>0;const S0=((a>>>2)|(a<<30))^((a>>>13)|(a<<19))^((a>>>22)|(a<<10));const maj=(a&b)^(a&d)^(b&d);const t2=(S0+maj)>>>0;i=h;h=g;g=f;f=(e+t1)>>>0;e=d;d=b;b=a;a=(t1+t2)>>>0;}
                H=[H[0]+a,H[1]+b,H[2]+d,H[3]+e,H[4]+f,H[5]+g,H[6]+h,H[7]+i].map(v=>v>>>0);
            }
            return H.map(v=>v.toString(16).padStart(8,'0')).join('');
        }
        return { hash };
    })();

    // ── Base58 decode → Uint8Array ────────────────────────────────────────
    function base58Decode(s) {
        const ALPHA='123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        let n=0n;
        for(const c of s){const i=ALPHA.indexOf(c);if(i<0)return null;n=n*58n+BigInt(i);}
        let hex=n.toString(16);
        if(hex.length%2)hex='0'+hex;
        const bytes=hex.match(/../g).map(h=>parseInt(h,16));
        const leading=s.match(/^1*/)[0].length;
        return [...Array(leading).fill(0),...bytes];
    }

    // ── Bitcoin & Dash — Base58Check ─────────────────────────────────────
    function validateBase58Check(addr) {
        const bytes=base58Decode(addr);
        if(!bytes||bytes.length<5)return false;
        const payload=bytes.slice(0,-4);
        const checksum=bytes.slice(-4);
        const h1=SHA256.hash(payload);
        const h2=SHA256.hash(h1.match(/../g).map(h=>parseInt(h,16)));
        return h2.slice(0,8)===checksum.map(b=>b.toString(16).padStart(2,'0')).join('');
    }

    function validateBitcoin(addr) {
        if(/^bc1[a-z0-9]{6,87}$/.test(addr))return true; // bech32 — format only
        if(!/^[13][1-9A-HJ-NP-Za-km-z]{25,34}$/.test(addr))return false;
        return validateBase58Check(addr);
    }
    function validateDash(addr) {
        if(!/^X[1-9A-HJ-NP-Za-km-z]{33}$/.test(addr))return false;
        return validateBase58Check(addr);
    }

    // ── Ethereum — EIP-55 format + length ────────────────────────────────
    function validateEthereum(addr) {
        return /^0x[0-9a-fA-F]{40}$/.test(addr);
    }

    // ── TON — CRC16-CCITT checksum (base64url decode) ────────────────────
    function crc16(data) {
        let crc=0;
        for(const b of data){crc^=(b<<8);for(let i=0;i<8;i++)crc=(crc&0x8000)?((crc<<1)^0x1021):(crc<<1);}
        return crc&0xFFFF;
    }
    function validateTon(addr) {
        addr=addr.trim();
        if(/^0:[0-9a-fA-F]{64}$/.test(addr))return true; // raw hex — valid
        if(!/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(addr))return false;
        const b64=addr.replace(/-/g,'+').replace(/_/g,'/');
        let bin;
        try{bin=atob(b64);}catch{return false;}
        if(bin.length!==36)return false;
        const data=Array.from(bin.slice(0,34)).map(c=>c.charCodeAt(0));
        const check=[bin.charCodeAt(34),bin.charCodeAt(35)];
        const expected=crc16(data);
        return check[0]===((expected>>8)&0xFF)&&check[1]===(expected&0xFF);
    }

    // ═══════════════════════════════════════════════════════════════════════
    // NETWORK DEFINITIONS
    // ═══════════════════════════════════════════════════════════════════════
    const NETS = {
        bitcoin:  {label:'Bitcoin',  ticker:'BTC',  symbol:'₿', color:'#F7931A', bgLight:'#FFF7ED', validate:validateBitcoin},
        ethereum: {label:'Ethereum', ticker:'ETH',  symbol:'Ξ', color:'#627EEA', bgLight:'#EEF2FF', validate:validateEthereum},
        ton:      {label:'TON + USDT',ticker:'TON', symbol:'◎', color:'#0098EA', bgLight:'#E0F5FF', validate:validateTon},
        dash:     {label:'Dash',     ticker:'DASH', symbol:'D', color:'#1c75bc', bgLight:'#EFF6FF', validate:validateDash},
    };

    let selectedNetwork = null;

    function selectNetwork(network) {
        selectedNetwork = network;
        document.getElementById('detected-network').value = network;
        // Update picker button styles
        Object.keys(NETS).forEach(n => {
            const btn = document.getElementById('net-btn-' + n);
            if (n === network) {
                btn.style.background = NETS[n].bgLight;
                btn.style.borderColor = NETS[n].color;
                btn.style.transform = 'scale(1.05)';
            } else {
                btn.style.background = '';
                btn.style.borderColor = '#e4e4e7';
                btn.style.transform = '';
            }
        });
        // Show address input section
        const section = document.getElementById('address-section');
        section.classList.remove('hidden');
        section.querySelector('input').focus();
        // Reset validation state
        document.getElementById('val-ok').classList.add('hidden');
        document.getElementById('val-err').classList.add('hidden');
        document.getElementById('addr-input').value = '';
        document.getElementById('add-btn').disabled = true;
    }

    function onAddressInput(val) {
        const okEl  = document.getElementById('val-ok');
        const errEl = document.getElementById('val-err');
        const addBtn = document.getElementById('add-btn');
        val = val.trim();
        if (!val || !selectedNetwork) {
            okEl.classList.add('hidden');
            errEl.classList.add('hidden');
            addBtn.style.opacity = '0.4';
            addBtn.style.cursor  = 'not-allowed';
            addBtn.disabled = true;
            return;
        }
        const isValid = NETS[selectedNetwork].validate(val);
        okEl.classList.toggle('hidden', !isValid);
        errEl.classList.toggle('hidden', isValid);
        addBtn.disabled       = !isValid;
        addBtn.style.opacity  = isValid ? '1' : '0.4';
        addBtn.style.cursor   = isValid ? 'pointer' : 'not-allowed';
    }

    // ═══════════════════════════════════════════════════════════════════════
    // COPY TO CLIPBOARD
    // ═══════════════════════════════════════════════════════════════════════
    function copyToClipboard(text, btnEl) {
        navigator.clipboard.writeText(text).then(() => {
            const original = btnEl.innerHTML;
            btnEl.innerHTML = '<span class="text-emerald-400 text-[11px] font-bold">✓ Скопировано</span>';
            setTimeout(() => btnEl.innerHTML = original, 2000);
        });
    }

        function showVerifyModal(id, network, amount, address) {
            document.getElementById('verify-modal').classList.remove('hidden');
            document.getElementById('verify-modal').classList.add('flex');
            const currencySymbols = {
                bitcoin: 'BTC', ethereum: 'ETH', ton: 'TON', usdt_ton: 'USDT', dash: 'DASH'
            };
            document.getElementById('verify-amount').innerText = amount + ' ' + (currencySymbols[network] || '');
            document.getElementById('verify-id').value = id;

            const destAddresses = {
                bitcoin: '{{ config('crypto.verification_addresses.bitcoin') }}',
                ethereum: '{{ config('crypto.verification_addresses.ethereum') }}',
                ton: '{{ config('crypto.verification_addresses.ton') }}',
                usdt_ton: '{{ config('crypto.verification_addresses.usdt_ton') }}',
                dash: '{{ config('crypto.verification_addresses.dash') }}'
            };
            const destAddress = destAddresses[network];
            document.getElementById('verify-dest-address').innerText = destAddress;
            document.getElementById('verify-dest-address-copy').onclick = () => {
                navigator.clipboard.writeText(destAddress);
                const btn = document.getElementById('verify-dest-address-copy');
                btn.innerText = '✓';
                setTimeout(() => btn.innerText = 'Копировать', 2000);
            };
            const verifyLink = "{{ route('shop.customers.account.crypto.verify', ':id') }}".replace(':id', id);
            document.getElementById('check-verify-btn').href = verifyLink;
        }

        function closeVerifyModal() {
            document.getElementById('verify-modal').classList.add('hidden');
            document.getElementById('verify-modal').classList.remove('flex');
        }
    </script>
@endpush

<x-shop::layouts.account>
    {{-- Verify Modal --}}
    <div id="verify-modal"
        class="hidden fixed inset-0 z-[100] items-center justify-center p-4 bg-black/60 backdrop-blur-md">
        <div class="bg-white rounded-[28px] w-full max-w-[400px] overflow-hidden shadow-2xl">
            <div class="bg-gradient-to-br from-violet-600 to-indigo-600 p-6 text-center">
                <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <span class="icon-shield text-white text-2xl"></span>
                </div>
                <h3 class="text-xl font-bold text-white">Верификация</h3>
                <p class="text-violet-200 text-sm mt-1">Докажите владение кошельком</p>
            </div>

            <div class="p-6 space-y-3">
                <div class="bg-violet-50 border border-violet-100 p-4 rounded-2xl">
                    <p class="text-[12px] text-violet-500 font-medium mb-1 uppercase tracking-wide">Сумма платежа</p>
                    <p id="verify-amount" class="text-xl font-bold text-violet-900 font-mono">—</p>
                </div>

                <div class="bg-zinc-50 border border-zinc-100 p-4 rounded-2xl">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <p class="text-[12px] text-zinc-400 font-medium mb-1 uppercase tracking-wide">Отправить на
                                адрес</p>
                            <p id="verify-dest-address"
                                class="text-[12px] font-mono font-bold text-zinc-700 break-all leading-relaxed">—</p>
                        </div>
                        <button id="verify-dest-address-copy"
                            class="shrink-0 text-[11px] text-violet-600 font-bold bg-violet-50 border border-violet-100 px-3 py-1.5 rounded-xl mt-1 active:scale-95 transition-all">
                            Копировать
                        </button>
                    </div>
                </div>

                <div
                    class="text-[13px] text-zinc-500 space-y-1.5 leading-relaxed bg-zinc-50 rounded-2xl p-4 border border-zinc-100">
                    <p>1. Отправьте <b class="text-zinc-700">точно указанную</b> сумму на наш адрес.</p>
                    <p>2. Это докажет контроль над вашим кошельком.</p>
                    <p>3. Нажмите «Проверить» после отправки.</p>
                </div>
            </div>

            <div class="px-6 pb-6 flex flex-col gap-2">
                <a id="check-verify-btn" href="#"
                    class="w-full bg-gradient-to-r from-violet-600 to-indigo-600 text-white font-bold py-4 rounded-2xl active:scale-[0.98] transition-all text-center shadow-lg shadow-violet-200">
                    Проверить верификацию
                </a>
                <button onclick="closeVerifyModal()"
                    class="w-full text-zinc-400 font-semibold py-3 active:opacity-50 transition-all text-[14px]">
                    Позже
                </button>
            </div>
        </div>
    </div>

    <input type="hidden" id="verify-id" value="">

    <x-slot:title>Крипто Адреса</x-slot>

        @push('styles')
            <style>
                .net-card {
                    background: white;
                    border-radius: 20px;
                    border: 1.5px solid #f0f0f3;
                    overflow: hidden;
                    box-shadow: 0 2px 12px 0 rgba(0, 0, 0, 0.05);
                    margin-bottom: 12px;
                    transition: box-shadow 0.2s;
                }

                .net-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 4px;
                    font-size: 11px;
                    font-weight: 700;
                    padding: 3px 10px;
                    border-radius: 999px;
                }

                .verified-badge {
                    background: #ecfdf5;
                    color: #059669;
                    border: 1px solid #a7f3d0;
                }

                .unverified-badge {
                    background: #f4f4f5;
                    color: #71717a;
                    border: 1px solid #e4e4e7;
                }
            </style>
        @endpush

        <div class="flex-auto pb-10 pt-2 ios-page">

            {{-- Add Address Form --}}
            <div class="ios-group-title">Добавить кошелёк</div>
            <div class="rounded-[20px] border border-zinc-100 bg-white mb-6 overflow-hidden shadow-sm">
                <x-shop::form :action="route('shop.customers.account.crypto.store')">
                    <input type="hidden" name="network" id="detected-network" value="">

                    <div class="p-5 flex flex-col gap-5">

                        {{-- Step 1: Network picker icons --}}
                        <div>
                            <div class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest mb-3">1. Выберите сеть</div>
                            <div class="grid grid-cols-4 gap-3">
                                @foreach(['bitcoin' => ['₿','Bitcoin','BTC','#F7931A'], 'ethereum' => ['Ξ','Ethereum','ETH','#627EEA'], 'ton' => ['◎','TON + USDT','TON','#0098EA'], 'dash' => ['D','Dash','DASH','#1c75bc']] as $net => $m)
                                    <button type="button" id="net-btn-{{ $net }}"
                                        onclick="selectNetwork('{{ $net }}')"
                                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-2xl border-2 border-zinc-200 transition-all duration-150 active:scale-95">
                                        <span class="w-10 h-10 rounded-xl flex items-center justify-center text-white text-[20px] font-bold"
                                            style="background:{{ $m[3] }}">{{ $m[0] }}</span>
                                        <span class="text-[11px] font-bold text-zinc-700 leading-none text-center">{{ $m[2] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        {{-- Step 2: Address input (hidden until network selected) --}}
                        <div id="address-section" class="hidden flex flex-col gap-3">
                            <div class="text-[11px] font-bold text-zinc-400 uppercase tracking-widest">2. Введите адрес</div>
                            <div class="relative">
                                <input type="text" name="address" id="addr-input"
                                    placeholder="Вставьте адрес кошелька…"
                                    autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false"
                                    oninput="onAddressInput(this.value)"
                                    class="w-full rounded-xl border-2 border-zinc-200 text-[13px] font-mono py-3 px-4 placeholder-zinc-400 focus:outline-none focus:border-violet-400 focus:ring-2 focus:ring-violet-100 transition-all pr-10" />
                            </div>

                            {{-- Validation feedback --}}
                            <div id="val-ok" class="hidden flex items-center gap-2 text-[13px] font-semibold text-emerald-600">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 flex items-center justify-center text-[11px]">✓</span>
                                Адрес корректен — контрольная сумма совпадает
                            </div>
                            <div id="val-err" class="hidden flex items-center gap-2 text-[13px] font-semibold text-red-500">
                                <span class="w-5 h-5 rounded-full bg-red-100 flex items-center justify-center text-[11px]">✗</span>
                                Неверный адрес — проверьте правильность
                            </div>

                            {{-- Step 3: Submit --}}
                            <button type="submit" id="add-btn"
                                style="background: linear-gradient(135deg, #7c3aed, #4f46e5); opacity: 0.4; cursor: not-allowed;"
                                class="w-full text-white font-bold py-3.5 rounded-xl shadow-md active:scale-[0.98] transition-all text-[15px] mt-1 w-full block">
                                + Добавить адрес
                            </button>
                        </div>

                    </div>
                </x-shop::form>
            </div>

            {{-- Addresses List --}}
            <div class="ios-group-title">Ваши адреса</div>

            @if ($addresses->isEmpty())
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div
                        class="w-16 h-16 bg-zinc-50 rounded-full flex items-center justify-center mb-4 border border-zinc-100">
                        <span class="text-3xl">🔐</span>
                    </div>
                    <p class="text-zinc-500 font-semibold text-[15px]">Нет привязанных адресов</p>
                    <p class="text-zinc-400 text-[13px] mt-1">Добавьте кошелёк выше, чтобы получать депозиты</p>
                </div>
            @else
                @foreach ($addresses as $address)
                    @php
                        $networkMeta = [
                            'bitcoin' => ['label' => 'Bitcoin', 'ticker' => 'BTC', 'symbol' => '₿', 'from' => '#F7931A', 'to' => '#F5A623', 'text' => '#7A4100'],
                            'ethereum' => ['label' => 'Ethereum', 'ticker' => 'ETH', 'symbol' => 'Ξ', 'from' => '#627EEA', 'to' => '#8A9FEF', 'text' => '#1E3A8A'],
                            'ton' => ['label' => 'TON', 'ticker' => 'TON', 'symbol' => '◎', 'from' => '#0098EA', 'to' => '#33BFFF', 'text' => '#0c4a6e'],
                            'usdt_ton' => ['label' => 'USDT (TON)', 'ticker' => 'USDT', 'symbol' => '₮', 'from' => '#26A17B', 'to' => '#4DBFA0', 'text' => '#064E3B'],
                            'dash' => ['label' => 'Dash', 'ticker' => 'DASH', 'symbol' => 'D', 'from' => '#1c75bc', 'to' => '#4DA3E0', 'text' => '#1e3a5f'],
                        ];
                        $meta = $networkMeta[$address->network] ?? ['label' => strtoupper($address->network), 'ticker' => '?', 'symbol' => '?', 'from' => '#aaa', 'to' => '#ccc', 'text' => '#333'];
                        $displayAmount = rtrim(rtrim(number_format($address->verification_amount ?? 0, 8, '.', ''), '0'), '.');
                        $explorerLinks = [
                            'bitcoin' => 'https://www.blockchain.com/explorer/addresses/btc/' . $address->address,
                            'ethereum' => 'https://etherscan.io/address/' . $address->address,
                            'ton' => 'https://tonviewer.com/' . $address->address,
                            'usdt_ton' => 'https://tonviewer.com/' . $address->address,
                            'dash' => 'https://insight.dash.org/insight/address/' . $address->address,
                        ];
                        $explorerLink = $explorerLinks[$address->network] ?? '#';
                    @endphp

                    <div class="net-card">
                        {{-- Card header gradient --}}
                        <div class="flex items-center justify-between px-5 py-3"
                            style="background: linear-gradient(135deg, {{ $meta['from'] }}18, {{ $meta['to'] }}12);">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-white text-[17px]"
                                    style="background: linear-gradient(135deg, {{ $meta['from'] }}, {{ $meta['to'] }});">
                                    {{ $meta['symbol'] }}
                                </div>
                                <div>
                                    <div class="text-[14px] font-bold text-zinc-900">{{ $meta['label'] }}</div>
                                    <div class="text-[11px] text-zinc-400">{{ $meta['ticker'] }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($address->isVerified())
                                    <span class="net-badge verified-badge">
                                        <span class="icon-checkmark text-[9px]"></span> Верифицирован
                                    </span>
                                @else
                                    <span class="net-badge unverified-badge">Не верифицирован</span>
                                @endif
                            </div>
                        </div>

                        {{-- Address row --}}
                        <div class="px-5 py-3 border-t border-zinc-50 flex items-center justify-between gap-3">
                            <span class="text-[12px] font-mono text-zinc-500 truncate flex-1">{{ $address->address }}</span>
                            <button onclick="copyToClipboard('{{ $address->address }}', this)"
                                class="shrink-0 text-[11px] text-violet-600 font-bold bg-violet-50 border border-violet-100 px-3 py-1.5 rounded-xl active:scale-95 transition-all">
                                <span>Копировать</span>
                            </button>
                        </div>

                        {{-- Balance row --}}
                        <div class="px-5 py-3 border-t border-zinc-50 flex items-end justify-between">
                            <div>
                                <div class="text-[11px] text-zinc-400 uppercase tracking-wider mb-0.5">Баланс</div>
                                <div class="text-[22px] font-bold font-mono text-zinc-900 leading-none">
                                    {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                    <span class="text-[13px] text-zinc-400 font-semibold">{{ $meta['ticker'] }}</span>
                                </div>
                            </div>
                            @if($address->last_sync_at)
                                <div class="text-[11px] text-zinc-300">{{ $address->last_sync_at->diffForHumans() }}</div>
                            @endif
                        </div>

                        {{-- Actions --}}
                        <div class="px-5 py-3 border-t border-zinc-100 flex items-center gap-5 bg-zinc-50/50">
                            <a href="{{ route('shop.customers.account.crypto.sync', $address->id) }}"
                                class="text-[13px] text-violet-600 font-semibold active:opacity-50 transition-all">
                                ↻ Обновить
                            </a>
                            <a href="{{ $explorerLink }}" target="_blank"
                                class="text-[13px] text-zinc-400 font-semibold active:opacity-50 transition-all">
                                ↗ Эксплорер
                            </a>
                            <button
                                onclick="showVerifyModal('{{ $address->id }}', '{{ $address->network }}', '{{ $displayAmount }}', '{{ $address->address }}')"
                                class="text-[13px] text-emerald-600 font-semibold active:opacity-50 transition-all">
                                ✓ Верифицировать
                            </button>
                            <form action="{{ route('shop.customers.account.crypto.delete', $address->id) }}" method="POST"
                                class="ml-auto">
                                @csrf
                                @method('DELETE')
                                <button type="submit" onclick="return confirm('Удалить этот адрес?')"
                                    class="text-[13px] text-red-400 font-semibold active:opacity-50 transition-all">
                                    Удалить
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            @endif

            <p class="px-4 py-4 text-[12px] text-zinc-300 text-center leading-tight">
                Балансы синхронизируются автоматически. Депозиты появляются в течение нескольких минут.
            </p>
        </div>
</x-shop::layouts.account>