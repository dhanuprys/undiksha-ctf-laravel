<script lang="ts">
    import { onMount } from 'svelte';
    import Breadcrumbs from '@/components/Breadcrumbs.svelte';
    import { SidebarTrigger } from '@/components/ui/sidebar';
    import { getServerOffset, getTimezoneLabel } from '@/lib/formatDate';
    import type { BreadcrumbItem } from '@/types';

    let {
        breadcrumbs = [],
    }: {
        breadcrumbs?: BreadcrumbItem[];
    } = $props();

    // Live server clock
    let serverClockDisplay = $state(
        new Date(Date.now() + getServerOffset()).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        }),
    );
    let clockInterval: ReturnType<typeof setInterval>;

    function updateClock() {
        const serverNow = new Date(Date.now() + getServerOffset());
        serverClockDisplay = serverNow.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false,
        });
    }

    onMount(() => {
        updateClock();
        clockInterval = setInterval(updateClock, 1000);

        return () => {
            if (clockInterval) {
                clearInterval(clockInterval);
            }
        };
    });
</script>

<header
    class="flex w-full h-16 shrink-0 items-center justify-between border-b border-sidebar-border/70 px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
>
    <div class="flex items-center gap-2">
        <SidebarTrigger class="-ml-1" />
        {#if breadcrumbs && breadcrumbs.length > 0}
            <Breadcrumbs {breadcrumbs} />
        {/if}
    </div>

    <div
        class="flex items-center gap-1.5 rounded-md bg-muted/50 px-2.5 py-1 border border-border/50 text-xs font-mono text-muted-foreground"
    >
        <span class="font-semibold tabular-nums">{serverClockDisplay}</span>
        <span class="text-[10px] font-bold opacity-60"
            >{getTimezoneLabel()}</span
        >
    </div>
</header>
