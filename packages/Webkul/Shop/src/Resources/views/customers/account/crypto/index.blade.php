@push('scripts')
    <script>


        // ── Clipboard ────────────────────────────────────────────────────
        function copyAddr(text, btn) {
            navigator.clipboard.writeText(text).then(() => {
                const orig = btn.innerHTML;
                btn.innerHTML = '<span class="text-emerald-500 text-[11px] font-bold">✓ Скопировано</span>';
                setTimeout(() => btn.innerHTML = orig, 2000);
            });
        }

        // ── Per-network address validators ────────────────────────────────
        const _SHA256 = (() => { const K = [0x428a2f98, 0x71374491, 0xb5c0fbcf, 0xe9b5dba5, 0x3956c25b, 0x59f111f1, 0x923f82a4, 0xab1c5ed5, 0xd807aa98, 0x12835b01, 0x243185be, 0x550c7dc3, 0x72be5d74, 0x80deb1fe, 0x9bdc06a7, 0xc19bf174, 0xe49b69c1, 0xefbe4786, 0x0fc19dc6, 0x240ca1cc, 0x2de92c6f, 0x4a7484aa, 0x5cb0a9dc, 0x76f988da, 0x983e5152, 0xa831c66d, 0xb00327c8, 0xbf597fc7, 0xc6e00bf3, 0xd5a79147, 0x06ca6351, 0x14292967, 0x27b70a85, 0x2e1b2138, 0x4d2c6dfc, 0x53380d13, 0x650a7354, 0x766a0abb, 0x81c2c92e, 0x92722c85, 0xa2bfe8a1, 0xa81a664b, 0xc24b8b70, 0xc76c51a3, 0xd192e819, 0xd6990624, 0xf40e3585, 0x106aa070, 0x19a4c116, 0x1e376c08, 0x2748774c, 0x34b0bcb5, 0x391c0cb3, 0x4ed8aa4a, 0x5b9cca4f, 0x682e6ff3, 0x748f82ee, 0x78a5636f, 0x84c87814, 0x8cc70208, 0x90befffa, 0xa4506ceb, 0xbef9a3f7, 0xc67178f2]; function h(msg) { let H = [0x6a09e667, 0xbb67ae85, 0x3c6ef372, 0xa54ff53a, 0x510e527f, 0x9b05688c, 0x1f83d9ab, 0x5be0cd19]; msg = Array.from(msg); const l = msg.length * 8; msg.push(0x80); while ((msg.length % 64) !== 56) msg.push(0); for (let i = 7; i >= 0; i--)msg.push((l / (2 ** (i * 8))) & 0xFF); for (let c = 0; c < msg.length; c += 64) { const W = []; for (let i = 0; i < 16; i++)W[i] = (msg[c + i * 4] << 24) | (msg[c + i * 4 + 1] << 16) | (msg[c + i * 4 + 2] << 8) | msg[c + i * 4 + 3]; for (let i = 16; i < 64; i++) { const s0 = ((W[i - 15] >>> 7) | (W[i - 15] << 25)) ^ ((W[i - 15] >>> 18) | (W[i - 15] << 14)) ^ (W[i - 15] >>> 3); const s1 = ((W[i - 2] >>> 17) | (W[i - 2] << 15)) ^ ((W[i - 2] >>> 19) | (W[i - 2] << 13)) ^ (W[i - 2] >>> 10); W[i] = (W[i - 16] + s0 + W[i - 7] + s1) >>> 0; } let [a, b, d, e, f, g, hh, ii] = [...H, H[6], H[7]]; for (let j = 0; j < 64; j++) { const S1 = ((f >>> 6) | (f << 26)) ^ ((f >>> 11) | (f << 21)) ^ ((f >>> 25) | (f << 7)); const ch = (f & g) ^ (~f & hh); const t1 = (ii + S1 + ch + K[j] + W[j]) >>> 0; const S0 = ((a >>> 2) | (a << 30)) ^ ((a >>> 13) | (a << 19)) ^ ((a >>> 22) | (a << 10)); const maj = (a & b) ^ (a & d) ^ (b & d); const t2 = (S0 + maj) >>> 0; ii = hh; hh = g; g = f; f = (e + t1) >>> 0; e = d; d = b; b = a; a = (t1 + t2) >>> 0; } H = [H[0] + a, H[1] + b, H[2] + d, H[3] + e, H[4] + f, H[5] + g, H[6] + hh, H[7] + ii].map(v => v >>> 0); } return H.map(v => v.toString(16).padStart(8, '0')).join(''); } return { hash: h }; })();
        function _b58d(s) { const A = '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz'; let n = 0n; for (const c of s) { const i = A.indexOf(c); if (i < 0) return null; n = n * 58n + BigInt(i); } let h = n.toString(16); if (h.length % 2) h = '0' + h; const b = h.match(/../g).map(x => parseInt(x, 16)); return [...Array(s.match(/^1*/)[0].length).fill(0), ...b]; }
        function _b58chk(a) { const b = _b58d(a); if (!b || b.length < 5) return false; const p = b.slice(0, -4), cs = b.slice(-4), h1 = _SHA256.hash(p), h2 = _SHA256.hash(h1.match(/../g).map(x => parseInt(x, 16))); return h2.slice(0, 8) === cs.map(x => x.toString(16).padStart(2, '0')).join(''); }
        function _crc16(d) { let c = 0; for (const b of d) { c ^= (b << 8); for (let i = 0; i < 8; i++)c = (c & 0x8000) ? ((c << 1) ^ 0x1021) : (c << 1); } return c & 0xFFFF; }
        const ADDR_NETS = {
            bitcoin: { label: 'Bitcoin', ticker: 'BTC', symbol: '₿', color: '#F7931A', bg: '#FFF7ED', validate: a => { if (/^bc1[a-z0-9]{6,87}$/.test(a)) return true; if (!/^[13][1-9A-HJ-NP-Za-km-z]{25,34}$/.test(a)) return false; return _b58chk(a); } },
            ethereum: { label: 'Ethereum', ticker: 'ETH', symbol: 'Ξ', color: '#627EEA', bg: '#EEF2FF', validate: a => /^0x[0-9a-fA-F]{40}$/.test(a) },
            ton: { label: 'TON + USDT', ticker: 'TON', symbol: '◎', color: '#0098EA', bg: '#E0F5FF', validate: a => { a = a.trim(); if (/^0:[0-9a-fA-F]{64}$/.test(a)) return true; if (!/^(UQ|EQ|UW|EW)[a-zA-Z0-9\-_]{46}$/.test(a)) return false; const b64 = a.replace(/-/g, '+').replace(/_/g, '/'); let bin; try { bin = atob(b64); } catch { return false; } if (bin.length !== 36) return false; const data = Array.from(bin.slice(0, 34)).map(c => c.charCodeAt(0)); const chk = [bin.charCodeAt(34), bin.charCodeAt(35)]; const exp = _crc16(data); return chk[0] === ((exp >> 8) & 0xFF) && chk[1] === (exp & 0xFF); } },
            dash: { label: 'Dash', ticker: 'DASH', symbol: 'D', color: '#1c75bc', bg: '#EFF6FF', validate: a => { if (!/^X[1-9A-HJ-NP-Za-km-z]{33}$/.test(a)) return false; return _b58chk(a); } },
        };
        let _selNet = null;
        function selNet(n) {
            _selNet = n;
            // For TON, default to 'ton' but show the asset selector
            if (n === 'ton') {
                document.getElementById('ton-asset-selector').classList.remove('hidden');
                document.getElementById('wallet-net-input').value = 'ton';
                selTonAsset('ton');
            } else {
                document.getElementById('ton-asset-selector').classList.add('hidden');
                document.getElementById('wallet-net-input').value = n;
            }

            Object.keys(ADDR_NETS).forEach(k => {
                const b = document.getElementById('wnet-' + k);
                if (k === n) { b.style.background = ADDR_NETS[k].bg; b.style.borderColor = ADDR_NETS[k].color; b.style.transform = 'scale(1.05)'; }
                else { b.style.background = ''; b.style.borderColor = '#e4e4e7'; b.style.transform = ''; }
            });
            const s = document.getElementById('wallet-addr-section');
            s.classList.remove('hidden'); s.querySelector('input').focus();
            ['wallet-val-ok', 'wallet-val-err'].forEach(id => document.getElementById(id).classList.add('hidden'));
            document.getElementById('wallet-addr-input').value = '';
            const btn = document.getElementById('wallet-add-btn');
            btn.disabled = true; btn.style.opacity = '0.4'; btn.style.cursor = 'not-allowed';
        }

        function selTonAsset(asset) {
            document.getElementById('wallet-net-input').value = asset;
            document.querySelectorAll('.ton-asset-btn').forEach(btn => {
                const isActive = btn.dataset.asset === asset;
                btn.style.borderColor = isActive ? '#0098EA' : '#e4e4e7';
                btn.style.background = isActive ? '#E0F5FF' : '#fff';
                btn.querySelector('span').style.color = isActive ? '#0098EA' : '#71717a';
            });
        }
        function onWalletInput(val) {
            const ok = document.getElementById('wallet-val-ok'), err = document.getElementById('wallet-val-err'), btn = document.getElementById('wallet-add-btn');
            val = val.trim();
            if (!val || !_selNet) { ok.classList.add('hidden'); err.classList.add('hidden'); btn.disabled = true; btn.style.opacity = '0.4'; btn.style.cursor = 'not-allowed'; return; }
            const v = ADDR_NETS[_selNet].validate(val);
            ok.classList.toggle('hidden', !v); err.classList.toggle('hidden', v);
            btn.disabled = !v; btn.style.opacity = v ? '1' : '0.4'; btn.style.cursor = v ? 'pointer' : 'not-allowed';
        }

        // ── Auto-trigger verify modal ─────────────────────────────────────
        document.addEventListener('DOMContentLoaded', () => {
            @if(session('show_verify_id'))
                @php
                    $target = $addresses->firstWhere('id', session('show_verify_id'));
                @endphp
                @if($target)
                    showVerifyModal(
                        '{{ $target->id }}',
                        '{{ $target->network }}',
                        '{{ rtrim(rtrim(number_format($target->verification_amount ?? 0, 8, '.', ''), '0'), '.') }}',
                        '{{ $target->address }}'
                    );
                @endif
            @endif
        });
    </script>
@endpush

<x-shop::layouts.account>
    <x-slot:title>@lang('shop::app.layouts.crypto-wallets')</x-slot>



        <div class="flex-auto pb-10 pt-2 ios-page">

            <div class="rounded-[20px] border border-zinc-100 bg-white mb-6 overflow-hidden shadow-sm">
                <x-shop::form :action="route('shop.customers.account.crypto.store')">
                    <input type="hidden" name="network" id="wallet-net-input" value="">
                    <div class="p-4 space-y-4">
                        {{-- Network Picker (Compact) --}}
                        <div class="grid grid-cols-4 gap-2">
                            @foreach(['bitcoin' => ['₿', 'BTC', '#F7931A'], 'ethereum' => ['Ξ', 'ETH', '#627EEA'], 'ton' => ['◎', 'TON', '#0098EA'], 'dash' => ['D', 'DASH', '#1c75bc']] as $net => $m)
                                <button type="button" id="wnet-{{ $net }}" onclick="selNet('{{ $net }}')"
                                    class="flex flex-col items-center justify-center py-2 px-1 rounded-xl border-2 border-zinc-50 transition-all duration-200 hover:bg-zinc-50 active:scale-95 group">
                                    <span
                                        class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[16px] font-bold mb-1 shadow-sm transition-transform group-hover:scale-110"
                                        style="background:linear-gradient(135deg, {{ $m[2] }}, {{ $m[2] }}dd)">{{ $m[0] }}</span>
                                    <span
                                        class="text-[9px] font-bold text-zinc-500 uppercase tracking-tight">{{ $m[1] }}</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- Input Area (Hidden initially) --}}
                        <div id="wallet-addr-section"
                            class="hidden animate-in fade-in slide-in-from-top-2 duration-300">
                            
                            {{-- TON Asset Selection (Only for TON) --}}
                            <div id="ton-asset-selector" class="hidden mb-4 p-3 bg-zinc-50/50 rounded-2xl border border-zinc-100">
                                <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2 px-1">Актив для верификации</p>
                                <div class="grid grid-cols-2 gap-2">
                                    <button type="button" onclick="selTonAsset('ton')" data-asset="ton"
                                        class="ton-asset-btn flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 bg-white transition-all active:scale-95">
                                        <span class="text-[12px] font-bold text-zinc-600">TON Coin</span>
                                    </button>
                                    <button type="button" onclick="selTonAsset('usdt_ton')" data-asset="usdt_ton"
                                        class="ton-asset-btn flex items-center justify-center gap-2 py-2.5 rounded-xl border border-zinc-200 bg-white transition-all active:scale-95">
                                        <span class="text-[12px] font-bold text-zinc-600">USDT (TON)</span>
                                    </button>
                                </div>
                            </div>

                            <div class="relative group">
                                <input type="text" name="address" id="wallet-addr-input"
                                    placeholder="Вставьте адрес кошелька…" oninput="onWalletInput(this.value)"
                                    class="w-full rounded-xl border-zinc-100 bg-zinc-50/50 text-[12px] font-mono py-3.5 pl-4 pr-12 placeholder-zinc-400 focus:outline-none focus:border-violet-400 focus:ring-4 focus:ring-violet-50 focus:bg-white transition-all" />
                                <div class="absolute right-4 top-1/2 -translate-y-1/2 flex items-center">
                                    <div id="wallet-val-ok" class="hidden text-emerald-500">✓</div>
                                    <div id="wallet-val-err" class="hidden text-red-400">✗</div>
                                </div>
                            </div>

                            <button type="submit" id="wallet-add-btn"
                                style="background:linear-gradient(135deg,#7c3aed,#4f46e5);opacity:0.4;cursor:not-allowed;"
                                class="w-full text-white font-bold py-3.5 rounded-xl text-[14px] mt-3 shadow-lg shadow-violet-100 active:scale-[0.98] transition-all">
                                + Добавить кошелёк
                            </button>
                        </div>
                    </div>
                </x-shop::form>
            </div>

            {{-- Address Cards (Compact & Elegant) --}}
            @if(!$addresses->isEmpty())
                <div class="space-y-3">
                    @foreach($addresses as $address)
                        @php
                            $nm = ['bitcoin' => ['Bitcoin', 'BTC', '₿', '#F7931A', '#F5A623'], 'ethereum' => ['Ethereum', 'ETH', 'Ξ', '#627EEA', '#8A9FEF'], 'ton' => ['TON', 'TON', '◎', '#0098EA', '#33BFFF'], 'usdt_ton' => ['USDT (TON)', 'USDT', '₮', '#26A17B', '#4DBFA0'], 'dash' => ['Dash', 'DASH', 'D', '#1c75bc', '#4DA3E0']];
                            $m = $nm[$address->network] ?? [strtoupper($address->network), strtoupper($address->network), '?', '#aaa', '#ccc'];
                            $dAmt = rtrim(rtrim(number_format($address->verification_amount ?? 0, 8, '.', ''), '0'), '.');
                            $exp = ['bitcoin' => 'https://www.blockchain.com/explorer/addresses/btc/', 'ethereum' => 'https://etherscan.io/address/', 'ton' => 'https://tonviewer.com/', 'usdt_ton' => 'https://tonviewer.com/', 'dash' => 'https://insight.dash.org/insight/address/'];
                            $expLink = ($exp[$address->network] ?? '#') . $address->address;
                        @endphp

                        <div
                            class="group relative bg-white rounded-2xl border border-zinc-100 shadow-sm overflow-hidden hover:shadow-md hover:border-violet-100 transition-all">
                            {{-- Card Header: Icon, Name, Balance, Status --}}
                            <div class="flex items-center justify-between p-3 gap-3">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[14px] font-bold shadow-sm shrink-0"
                                        style="background:linear-gradient(135deg,{{ $m[3] }},{{ $m[4] }})">
                                        {{ $m[2] }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-baseline gap-1.5">
                                            <span class="text-[13px] font-bold text-zinc-900 truncate">{{ $m[0] }}</span>
                                            <span class="text-[13px] font-bold font-mono text-zinc-900">
                                                {{ rtrim(rtrim(number_format($address->balance ?? 0, 8, '.', ''), '0'), '.') ?: '0' }}
                                            </span>
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase">{{ $m[1] }}</span>
                                        </div>
                                        @if($address->last_sync_at)
                                            <div class="text-[9px] text-zinc-400 font-medium leading-none mt-0.5">
                                                {{ $address->last_sync_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="shrink-0">
                                    @if($address->isVerified())
                                        <div class="bg-emerald-50 text-emerald-600 p-1 rounded-full border border-emerald-100" title="Верифицирован">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                        </div>
                                    @else
                                        <div class="bg-zinc-50 text-zinc-300 p-1 rounded-full border border-zinc-100" title="Не верифицирован">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Address Row (Tight) --}}
                            <div class="px-3 pb-2 flex items-center justify-between gap-2">
                                <code class="text-[10px] font-mono text-zinc-400 truncate flex-1 select-all opacity-80">{{ $address->address }}</code>
                                <button onclick="copyAddr('{{ $address->address }}', this)"
                                    class="shrink-0 text-[10px] font-bold text-violet-500 hover:text-violet-700 transition-colors">Копировать</button>
                            </div>

                            {{-- Actions Row (Minimal) --}}
                            <div class="flex items-center border-t border-zinc-50 bg-zinc-50/30 px-3 py-1.5 gap-3">
                                <a href="{{ route('shop.customers.account.crypto.sync', $address->id) }}"
                                    class="text-[10px] text-violet-600 font-bold hover:underline">Обновить</a>
                                <div class="w-1 h-1 rounded-full bg-zinc-200"></div>
                                <a href="{{ $expLink }}" target="_blank"
                                    class="text-[10px] text-zinc-400 font-bold hover:underline">Эксплорер</a>

                                @if(!$address->isVerified())
                                    <div class="w-1 h-1 rounded-full bg-zinc-200"></div>
                                    <button
                                        onclick="showVerifyModal('{{ $address->id }}','{{ $address->network }}','{{ $dAmt }}','{{ $address->address }}')"
                                        class="text-[10px] text-emerald-600 font-bold hover:underline">Верифицировать</button>
                                @endif

                                <div class="flex-1"></div>
                                <form action="{{ route('shop.customers.account.crypto.delete', $address->id) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" onclick="return confirm('Удалить адрес?')"
                                        class="text-[10px] text-red-400 font-bold hover:underline">Удалить</button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 bg-zinc-50 rounded-full flex items-center justify-center mb-4 text-3xl">👛</div>
                    <p class="text-zinc-500 font-semibold text-[15px]">У вас пока нет кошельков</p>
                    <p class="text-zinc-400 text-[13px] mt-1">Добавьте адрес выше, чтобы начать</p>
                </div>
            @endif
        </div>
</x-shop::layouts.account>