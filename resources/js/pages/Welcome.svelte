<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login, register } from '@/routes';

    const auth = $derived(page.props.auth);
    const appName = import.meta.env.VITE_APP_NAME || 'CTF Undiksha';
</script>

<AppHead title="Welcome" />

<div class="flex min-h-screen flex-col bg-background text-foreground">
    <!-- Navbar -->
    <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container mx-auto flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-2">
                <span class="text-xl font-bold tracking-tight text-primary">{appName}</span>
            </div>
            <nav class="flex items-center gap-4">
                {#if auth.user}
                    {#if (auth.user as any).is_admin}
                        <a href="/admin" class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
                            Admin Dashboard
                        </a>
                    {:else}
                        <Link href={toUrl(dashboard())} class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
                            Participant Dashboard
                        </Link>
                    {/if}
                {:else}
                    <Link href={toUrl(login())} class="text-sm font-medium transition-colors hover:text-primary">
                        Log in
                    </Link>
                    <Link href={toUrl(register())} class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
                        Register
                    </Link>
                {/if}
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <main class="flex-1 flex flex-col items-center justify-center relative overflow-hidden">
        <!-- Abstract background elements -->
        <div class="absolute inset-0 -z-10 h-full w-full bg-white dark:bg-zinc-950 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] dark:bg-[radial-gradient(#27272a_1px,transparent_1px)] [background-size:16px_16px]"></div>
        <div class="absolute left-1/2 top-1/2 -z-10 -translate-x-1/2 -translate-y-1/2 h-[600px] w-[600px] rounded-full bg-primary/10 blur-[100px]"></div>

        <div class="container mx-auto px-4 py-24 text-center sm:px-6 lg:px-8 relative z-10">
            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent bg-secondary text-secondary-foreground hover:bg-secondary/80 mb-8">
                Welcome to the Competition
            </div>
            
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl lg:text-7xl mb-6">
                Ready to <span class="text-primary">Capture</span> the Flag?
            </h1>
            
            <p class="mx-auto mt-6 max-w-2xl text-lg sm:text-xl text-muted-foreground mb-10">
                Join the ultimate cybersecurity challenge. Test your skills, solve complex puzzles, collaborate with your team, and climb the leaderboard!
            </p>
            
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                {#if auth.user}
                    {#if (auth.user as any).is_admin}
                        <a href="/admin" class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-base font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
                            Go to Admin Panel
                        </a>
                    {:else}
                        <Link href={toUrl(dashboard())} class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-base font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90">
                            Enter Dashboard
                        </Link>
                    {/if}
                {:else}
                    <Link href={toUrl(register())} class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-base font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 w-full sm:w-auto">
                        Get Started
                    </Link>
                    <Link href={toUrl(login())} class="inline-flex h-12 items-center justify-center rounded-md border border-input bg-background px-8 text-base font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground w-full sm:w-auto">
                        Log in
                    </Link>
                {/if}
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t py-6 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
        <div class="container mx-auto flex flex-col items-center justify-center gap-4 md:h-10 md:flex-row px-4">
            <p class="text-center text-sm text-muted-foreground">
                © {new Date().getFullYear()} {appName}. All rights reserved.
            </p>
        </div>
    </footer>
</div>
