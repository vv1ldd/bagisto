<x-shop::layouts :has-header="false" :has-feature="false" :has-footer="false">
    <x-slot:title>
        Комната звонка — Meanly
    </x-slot:title>

    <div class="flex-grow flex flex-col items-center justify-center bg-zinc-950 text-white min-h-screen" data-echo-bootstrap>
        <!-- The background is now intentionally empty as everything happens within the CallOverlay -->
        <v-call-overlay></v-call-overlay>
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
                        // Use recipient email as fallback if guest name is generic
                        $baseName = ($guestEmail === 'Гость' && $session->recipient_email) 
                            ? $session->recipient_email 
                            : $guestEmail;
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
                    remoteName: "{{ $remoteName }}",
                    turnConfig: @json(config('services.turn'))
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
