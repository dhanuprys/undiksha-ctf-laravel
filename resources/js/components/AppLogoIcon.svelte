<script lang="ts">
    let {
        class: className = '',
        ...rest
    }: {
        class?: string;
        [key: string]: unknown;
    } = $props();
</script>

<img
    src="/images/ganesha-ctf-platform-square.webp"
    alt="Ganesha CTF Logo"
    class={className}
    {...rest}
/>
