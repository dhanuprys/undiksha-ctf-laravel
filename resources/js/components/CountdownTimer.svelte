<script lang="ts">
    import { onMount, onDestroy } from 'svelte';
    import { Badge } from '@/components/ui/badge';

    let {
        targetDate,
        label = '',
    }: {
        targetDate: string | null;
        label?: string;
    } = $props();

    let timeRemaining = $state('');
    let interval: ReturnType<typeof setInterval>;

    function calculateTimeRemaining() {
        if (!targetDate) {
            timeRemaining = 'Tidak ada waktu yang ditentukan';

            return;
        }

        const target = new Date(targetDate).getTime();
        const now = new Date().getTime();
        const difference = target - now;

        if (difference < 0) {
            timeRemaining = 'Selesai';
            clearInterval(interval);

            return;
        }

        const days = Math.floor(difference / (1000 * 60 * 60 * 24));
        const hours = Math.floor((difference % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((difference % (1000 * 60)) / 1000);

        let formatted = '';

        if (days > 0) {
formatted += `${days}h `;
}

        if (hours > 0 || days > 0) {
formatted += `${hours}j `;
}

        if (minutes > 0 || hours > 0 || days > 0) {
formatted += `${minutes}m `;
}

        formatted += `${seconds}d`;

        timeRemaining = formatted;
    }

    onMount(() => {
        calculateTimeRemaining();

        if (targetDate && new Date(targetDate).getTime() > new Date().getTime()) {
            interval = setInterval(calculateTimeRemaining, 1000);
        }
    });

    onDestroy(() => {
        if (interval) {
clearInterval(interval);
}
    });
</script>

{#if label}
    <div class="flex items-center gap-2">
        <span class="text-sm font-medium">{label}:</span>
        <Badge variant="outline" class="font-mono">{timeRemaining}</Badge>
    </div>
{:else}
    <Badge variant="outline" class="font-mono">{timeRemaining}</Badge>
{/if}
