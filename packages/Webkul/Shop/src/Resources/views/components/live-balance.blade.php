@props(['user', 'class' => ''])

<div class="live-mc-balance-container {{ $class }}" 
     data-user-address="{{ $user->credits_id ?? '' }}"
     data-rpc-url="{{ config('crypto.rpc_url_arbitrum', 'https://arb1.arbitrum.io/rpc') }}"
     data-token-address="{{ config('crypto.meanly_coin_address') }}">
    <span class="live-mc-balance tabular-nums">
        <span class="animate-pulse opacity-50">...</span>
    </span>
    <span class="ml-0.5">MC</span>
</div>

@pushOnce('scripts')
<script type="module">
    import { ethers } from "https://cdnjs.cloudflare.com/ajax/libs/ethers/6.7.0/ethers.min.js";

    async function updateLiveBalances() {
        const containers = document.querySelectorAll('.live-mc-balance-container');
        if (containers.length === 0) return;

        const rpcUrl = containers[0].dataset.rpcUrl;
        const tokenAddress = containers[0].dataset.tokenAddress;
        const abi = ["function balanceOf(address owner) view returns (uint256)"];

        try {
            const provider = new ethers.JsonRpcProvider(rpcUrl);
            const contract = new ethers.Contract(tokenAddress, abi, provider);

            for (const container of containers) {
                const userAddress = container.dataset.userAddress;
                if (!userAddress || userAddress === 'null' || userAddress === '') {
                    container.querySelector('.live-mc-balance').innerText = '0.00';
                    continue;
                }

                try {
                    const balanceWei = await contract.balanceOf(userAddress);
                    const balanceDec = parseFloat(ethers.formatEther(balanceWei));
                    container.querySelector('.live-mc-balance').innerText = balanceDec.toFixed(2);
                } catch (err) {
                    console.error('Error fetching balance for', userAddress, err);
                    container.querySelector('.live-mc-balance').innerText = '0.00';
                }
            }
        } catch (globalErr) {
            console.error('Blockchain provider error:', globalErr);
        }
    }

    // Initial fetch
    document.addEventListener('DOMContentLoaded', updateLiveBalances);
    
    // Refresh every 30 seconds
    setInterval(updateLiveBalances, 30000);
    
    // Also listen for a custom event if we want to force refresh
    document.addEventListener('mc-balance-refresh', updateLiveBalances);
</script>
@endPushOnce
