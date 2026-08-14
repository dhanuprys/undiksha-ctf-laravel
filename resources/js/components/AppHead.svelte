<script lang="ts">
    import { onMount } from 'svelte';
    import type { Snippet } from 'svelte';

    let {
        title = '',
        children,
    }: {
        title?: string;
        children?: Snippet;
    } = $props();

    const appName = import.meta.env.VITE_APP_NAME || 'Ganesha CTF Platform';
    const fullTitle = $derived(title ? `${title} - ${appName}` : appName);

    onMount(() => {
        // a big and styled console log
        console.log(
            '%cJalan-jalan naik pesawat ✈️\nPesawatnya terbang ke Bali 🌴\nKukira kamu pergi sesaat\nTernyata tidak pernah kembali 💔',
            'color: #22c55e; font-size: 13px; font-weight: bold; background-color: #1f2937; padding: 10px; border-radius: 5px',
        );
        console.log(
            '%c~DedanLabs',
            'color: #22c55e; font-size: 9px; font-weight: bold; background-color: #1f2937; padding: 10px; border-radius: 5px',
        );
    });
</script>

<svelte:head>
    <title>{fullTitle}</title>
    <meta property="og:title" content={fullTitle} />
    <meta name="twitter:title" content={fullTitle} />
    {#if typeof window !== 'undefined'}
        <meta property="og:url" content={window.location.href} />
    {/if}
    {@render children?.()}
</svelte:head>
