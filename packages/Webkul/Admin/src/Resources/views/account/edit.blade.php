<x-admin::layouts>
    <x-slot:title>
        @lang('admin::app.account.edit.title')
    </x-slot>

    <!-- Input Form -->
    <x-admin::form
        :action="route('admin.account.update')"
        enctype="multipart/form-data"
        method="PUT"
        id="account-edit-form"
    >
        <input type="hidden" name="passkey_verification_response" id="passkey-verification-response">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('admin::app.account.edit.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                 <!-- Back Button -->
                <a
                    href="{{ route('admin.dashboard.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('admin::app.account.edit.back-btn')
                </a>

                <!-- Save Button -->
                <div class="flex items-center gap-x-2.5">
                    <button 
                        type="button"
                        class="primary-button"
                        onclick="handleProfileSave(event)"
                        id="save-profile-btn"
                    >
                        @lang('admin::app.account.edit.save-btn')
                    </button>
                </div>
            </div>
        </div>

        <!-- Full Panel -->
        <div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
             <!-- Left sub Component -->
             <div class="flex flex-1 flex-col gap-2">
                 <!-- General -->
                 <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('admin::app.account.edit.general')
                    </p>

                    <!-- Image -->
                    <x-admin::form.control-group>
                        <x-admin::media.images
                            name="image"
                            :uploaded-images="$user->image ? [['id' => 'image', 'url' => $user->image_url]] : []"
                        />
                    </x-admin::form.control-group>

                    <p class="mb-4 text-xs text-gray-600 dark:text-gray-300">
                        @lang('admin::app.account.edit.upload-image-info')
                    </p>

                    <!-- Name -->
                    <x-admin::form.control-group>
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.account.edit.name')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="text"
                            name="name"
                            rules="required"
                            :value="old('name') ?: $user->name"
                            :label="trans('admin::app.account.edit.name')"
                            :placeholder="trans('admin::app.account.edit.name')"
                        />

                        <x-admin::form.control-group.error control-name="name" />
                    </x-admin::form.control-group>

                    <!-- Email -->
                    <x-admin::form.control-group class="!mb-0">
                        <x-admin::form.control-group.label class="required">
                            @lang('admin::app.account.edit.email')
                        </x-admin::form.control-group.label>

                        <x-admin::form.control-group.control
                            type="email"
                            name="email"
                            id="email"
                            rules="required"
                            :value="old('email') ?: $user->email"
                            :label="trans('admin::app.account.edit.email')"
                        />

                        <x-admin::form.control-group.error control-name="email" />
                    </x-admin::form.control-group>
                </div>
             </div>

             <!-- Right sub-component -->
             <div class="flex w-[360px] max-w-full flex-col gap-2 max-md:w-full">
                @if (false) {{-- Hide Password management as requested --}}
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                            @lang('admin::app.account.edit.change-password')
                        </p>
                    </x-slot>

                     <!-- Change Account Password -->
                    <x-slot:content>
                        <!-- Current Password -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label class="required">
                                @lang('admin::app.account.edit.current-password')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="password"
                                name="current_password"
                                rules="required|min:6"
                                :label="trans('admin::app.account.edit.current-password')"
                                :placeholder="trans('admin::app.account.edit.current-password')"
                            />

                            <x-admin::form.control-group.error control-name="current_password" />
                        </x-admin::form.control-group>

                        <!-- Password -->
                        <x-admin::form.control-group>
                            <x-admin::form.control-group.label>
                                @lang('admin::app.account.edit.password')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="password"
                                name="password"
                                rules="min:6"
                                :placeholder="trans('admin::app.account.edit.password')"
                                ref="password"
                            />

                            <x-admin::form.control-group.error control-name="password" />
                        </x-admin::form.control-group>

                        <!-- Confirm Password -->
                        <x-admin::form.control-group class="!mb-0">
                            <x-admin::form.control-group.label>
                                @lang('admin::app.account.edit.confirm-password')
                            </x-admin::form.control-group.label>

                            <x-admin::form.control-group.control
                                type="password"
                                name="password_confirmation"
                                rules="confirmed:@password"
                                :label="trans('admin::app.account.edit.confirm-password')"
                                :placeholder="trans('admin::app.account.edit.confirm-password')"
                            />

                            <x-admin::form.control-group.error control-name="password_confirmation" />
                        </x-admin::form.control-group>
                    </x-slot>
                </x-admin::accordion>
                @endif

                <!-- Passkeys -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                            Passkeys
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="flex flex-col gap-2">
                             @if ($user->passkeys->count())
                                @foreach ($user->passkeys as $passkey)
                                    <div class="flex items-center justify-between gap-4 border-b pb-2 last:border-0 last:pb-0 dark:border-gray-800">
                                        <div class="flex flex-col gap-0.5 min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                                                {{ $passkey->name ?: 'Passkey' }}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                {{ $passkey->created_at->format('d/m/Y') }}
                                            </p>
                                        </div>

                                        <form action="{{ route('admin.passkey.destroy', $passkey->id) }}" method="POST" onsubmit="return confirm('Удалить этот ключ?')">
                                            @csrf
                                            @method('DELETE')
                                            
                                            <button type="submit" class="text-red-500 hover:text-red-600 transition-colors p-1" title="Удалить">
                                                <span class="icon-delete text-xl"></span>
                                            </button>
                                        </form>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    У вас пока нет привязанных ключей доступа.
                                </p>
                            @endif

                             <button type="button" id="add-passkey-button" onclick="startPasskeyRegistration()"
                                class="mt-4 flex w-full items-center justify-center gap-2 rounded-md border border-gray-200 bg-white px-3 py-2 text-xs font-semibold text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700">
                                <span class="icon-add-new text-lg"></span>
                                Добавить ключ (Passkey)
                            </button>
                        </div>
                    </x-slot>
                </x-admin::accordion>

                <!-- Security Status -->
                <x-admin::accordion>
                    <x-slot:header>
                        <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                            Безопасность
                        </p>
                    </x-slot>

                    <x-slot:content>
                        <div class="flex flex-col gap-4">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                        Seed-фраза (Recovery)
                                    </p>
                                    <p class="text-xs text-{{ $user->mnemonic_verified_at ? 'green' : 'red' }}-500">
                                        {{ $user->mnemonic_verified_at ? 'Активирована: ' . $user->mnemonic_verified_at->format('d/m/Y') : 'Не настроена! Ваш аккаунт под угрозой.' }}
                                    </p>
                                </div>

                                @if (!$user->mnemonic_verified_at)
                                    <a href="{{ route('admin.security.onboarding.index') }}" class="secondary-button text-xs py-1 px-3">
                                        Настроить
                                    </a>
                                @endif
                            </div>

                            <div class="flex items-center justify-between gap-4 border-t pt-2 dark:border-gray-800">
                                <div class="flex flex-col gap-1">
                                    <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                        Crypto Wallet
                                    </p>
                                    <p class="text-xs text-gray-500 truncate max-w-[200px]" title="{{ $user->credits_id }}">
                                        {{ $user->credits_id ?: 'Не привязан' }}
                                    </p>
                                </div>

                                @if ($user->credits_id)
                                    <span class="icon-done text-green-500 text-xl"></span>
                                @endif
                            </div>
                        </div>
                    </x-slot>
                </x-admin::accordion>
             </div>
        </div>
    </x-admin::form>
</x-admin::layouts>

@push('scripts')
    <script>
        /**
         * Handle Profile Save (with Passkey Step-up)
         */
        async function handleProfileSave(e) {
            const form = document.getElementById('account-edit-form');
            const passwordField = form.querySelector('input[name="current_password"]');
            const hasPasskeys = {{ $user->passkeys->count() > 0 ? 'true' : 'false' }};
            const saveBtn = document.getElementById('save-profile-btn');

            // If user filled the password, or doesn't have passkeys, just submit
            if ((passwordField && passwordField.value.trim() !== '') || !hasPasskeys) {
                form.submit();
                return;
            }

            // Otherwise, attempt Passkey Step-up
            const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
            if (!SimpleWebAuthn || !window.PublicKeyCredential) {
                // Fallback to normal submission (which will likely fail on server if password is required)
                form.submit();
                return;
            }

            const originalText = saveBtn.innerText;
            saveBtn.disabled = true;
            saveBtn.innerText = 'Подтверждение (Passkey)...';

            try {
                // 1. Get Targeted Options
                const response = await fetch('{{ route('admin.passkey.login_options') }}?targeted=true', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Ошибка получения параметров подтверждения.');

                const options = await response.json();
                
                // 2. Start Authentication
                const asseResp = await SimpleWebAuthn.startAuthentication(options);

                // 3. Inject Response and Submit
                document.getElementById('passkey-verification-response').value = JSON.stringify(asseResp);
                form.submit();
                
            } catch (err) {
                console.error('[Passkey Step-up] Error:', err);
                saveBtn.disabled = false;
                saveBtn.innerText = originalText;

                if (err.name !== 'NotAllowedError' && !err.message.includes('cancel')) {
                    alert('Для сохранения изменений необходимо подтвердить личность паролем или Passkey.');
                }
            }
        }

        /**
         * Start Passkey Registration
         */
        async function startPasskeyRegistration() {
            const SimpleWebAuthn = window.SimpleWebAuthnBrowser;
            const button = document.getElementById('add-passkey-button');
            const originalText = button.innerHTML;

            if (!SimpleWebAuthn) {
                alert('Библиотека WebAuthn не загружена.');
                return;
            }

            if (!window.PublicKeyCredential) {
                alert('Ваш браузер не поддерживает Passkey (требуется HTTPS).');
                return;
            }

            button.disabled = true;
            button.innerText = 'Подготовка...';

            try {
                const response = await fetch('{{ route('admin.passkey.register_options') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) throw new Error('Не удалось получить настройки с сервера.');

                const rawOptions = await response.json();
                const optionsJSON = rawOptions.publicKey ? rawOptions.publicKey : rawOptions;

                button.innerText = 'Ожидание устройства...';

                // Start WebAuthn registration
                const attResp = await SimpleWebAuthn.startRegistration(optionsJSON);

                button.innerText = 'Сохранение...';

                const saveRes = await fetch('{{ route('admin.passkey.register') }}', {
                    method: 'POST',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(attResp)
                });

                if (saveRes.ok) {
                    window.location.reload();
                    return;
                } else {
                    const errorData = await saveRes.json();
                    throw new Error(errorData.message || 'Ошибка сохранения Passkey');
                }
            } catch (error) {
                console.error('[Passkey] Error:', error);
                
                if (error.name === 'NotAllowedError' || error.message.includes('отмена') || error.name === 'AbortError' || error.message.includes('cancelled')) {
                    // Canceled by user, just silent return
                    return;
                }
                
                alert('Произошла ошибка при добавлении ключа: ' + error.message);
            } finally {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        }
    </script>
@endpush
