<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { ArrowRight } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login } from '@/routes';

    const auth = $derived(page.props.auth);
    const appName = import.meta.env.VITE_APP_NAME || 'Ganesha CTF Platform';
</script>

<AppHead title="Welcome" />

<div
    class="flex min-h-screen flex-col bg-background text-foreground relative overflow-hidden selection:bg-primary/20 selection:text-primary"
>
    <!-- Clean Background Accent -->
    <div
        class="absolute inset-0 -z-20 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] dark:bg-[radial-gradient(#1e293b_1px,transparent_1px)] bg-[size:32px_32px] pointer-events-none opacity-50"
    ></div>

    <!-- Minimalist Red Glow -->
    <div
        class="absolute top-0 right-0 -z-10 h-[500px] w-[500px] translate-x-1/3 -translate-y-1/4 rounded-full bg-primary/10 blur-[120px] pointer-events-none"
    ></div>

    <!-- Navbar -->
    <header
        class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/80 backdrop-blur-md"
    >
        <div
            class="container mx-auto flex h-16 items-center justify-between px-6 sm:px-8"
        >
            <div class="flex items-center">
                <img
                    src="/images/ganesha-ctf-platform-logo.webp"
                    alt={appName}
                    class="h-14 w-auto object-contain"
                />
            </div>

            <nav class="flex items-center gap-4">
                {#if auth.user}
                    {#if (auth.user as any).is_admin}
                        <a
                            href="/admin"
                            class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                        >
                            Admin Panel
                        </a>
                    {:else}
                        <Link
                            href={toUrl(dashboard())}
                            class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                        >
                            Dashboard
                        </Link>
                    {/if}
                {:else}
                    <Link
                        href={toUrl(login())}
                        class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90"
                    >
                        Sign In
                    </Link>
                {/if}
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main
        class="flex-1 flex items-center justify-center relative px-6 py-12 md:py-24"
    >
        <div class="container mx-auto max-w-4xl text-center relative z-10">
            <div
                class="inline-flex items-center rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-sm font-medium text-primary mb-8"
            >
                <span>Cybersecurity Challenge</span>
            </div>

            <h1
                class="text-4xl font-extrabold tracking-tight sm:text-6xl mb-6 text-foreground"
            >
                Capture The Flag. <br />
                <span class="text-primary">Defend the Core.</span>
            </h1>

            <p
                class="max-w-2xl mx-auto text-lg text-muted-foreground mb-10 leading-relaxed"
            >
                Test your skills in a modern, secure, and competitive
                environment. Decrypt, exploit, and secure systems to rise to the
                top of the leaderboard.
            </p>

            <div
                class="flex flex-col sm:flex-row items-center justify-center gap-4"
            >
                {#if auth.user}
                    {#if (auth.user as any).is_admin}
                        <a
                            href="/admin"
                            class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 w-full sm:w-auto"
                        >
                            Go to Admin Panel
                            <ArrowRight class="h-4 w-4 ml-2" />
                        </a>
                    {:else}
                        <Link
                            href={toUrl(dashboard())}
                            class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 w-full sm:w-auto"
                        >
                            Enter Dashboard
                            <ArrowRight class="h-4 w-4 ml-2" />
                        </Link>
                    {/if}
                {:else}
                    <Link
                        href={toUrl(login())}
                        class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 w-full sm:w-auto"
                    >
                        Join the Competition
                        <ArrowRight class="h-4 w-4 ml-2" />
                    </Link>
                {/if}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="py-6 border-t border-border/40">
        <div
            class="container mx-auto flex flex-col items-center justify-center px-6 sm:px-8"
        >
            <p class="text-center text-sm text-muted-foreground">
                © {new Date().getFullYear()}
                {appName}. All rights reserved.
            </p>
        </div>
    </footer>
</div>
