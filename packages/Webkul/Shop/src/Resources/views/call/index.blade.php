<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        Комната звонка — Meanly
    </x-slot:title>

    <div class="flex-grow flex flex-col items-center justify-center bg-zinc-950 text-white min-h-screen">
        <!-- The background is now intentionally empty as everything happens within the CallOverlay -->
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Prepare room data
                @php
                    $guestEmail = request()->get('email', 'Гость');
                    $participantHash = request()->get('h');
                    
                    if (auth()->guard('customer')->check()) {
                        $customer = auth()->guard('customer')->user();
                        $baseName = $customer->username ?? $customer->first_name;
                        $participantHash = $participantHash ?? md5($customer->email . $session->uuid);
                    } else {
                        $baseName = $guestEmail;
                    }
                @endphp

                @php
                    $isCaller = auth()->guard('customer')->check() && auth()->guard('customer')->user()->email === $session->caller_email;
                    $remoteName = $isCaller ? ($session->recipient_email ?? 'Гость') : ($session->caller_name ?? $session->caller_email);
                @endphp

                const roomData = {
                    uuid: "{{ $session->uuid }}",
                    userName: "{{ $baseName }}",
                    hash: "{{ $participantHash }}",
                    remoteName: "{{ $remoteName }}"
                };

                // Trigger overlay immediately
                if (window.$emitter) {
                    // Slight delay to ensure CallOverlay is mounted and listening
                    setTimeout(() => {
                        window.$emitter.emit('join-room', roomData);
                    }, 500);
                }
            });
        </script>
    @endpush
</x-shop::layouts>
