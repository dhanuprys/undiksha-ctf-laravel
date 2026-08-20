<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { ArrowRight } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import BootSequence from '@/components/BootSequence.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login } from '@/routes';

    const auth = $derived(page.props.auth);
    const appName = import.meta.env.VITE_APP_NAME || 'Ganesha CTF Platform';

    let bootCompleted = $state(false);

    function handleBootComplete() {
        bootCompleted = true;
    }

    function hoverFlag(node: HTMLElement, trigger: boolean = false) {
        // Store original text
        const originalText = node.innerText.trim();
        let interval: ReturnType<typeof setInterval>;
        let initialTimeout: ReturnType<typeof setTimeout>;
        const charset =
            'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';

        let isScrambling = false;

        function start() {
            if (isScrambling) {
                return;
            }
            
            isScrambling = true;
            let len = originalText.length - 5;

            if (len < 4) {
                len = 4;
            }

            if (len > 16) {
                len = 16;
            }

            interval = setInterval(() => {
                let r = '';

                for (let i = 0; i < len; i++) {
                    r += charset.charAt(
                        Math.floor(Math.random() * charset.length),
                    );
                }

                node.innerText = `CTF{${r}}`;
            }, 50);
        }

        function stop() {
            isScrambling = false;
            clearInterval(interval);
            node.innerText = originalText;
        }

        function triggerDecode() {
            start();
            initialTimeout = setTimeout(
                () => {
                    stop();
                },
                600 + Math.random() * 1200,
            ); // 0.6s to 1.8s random decode time
        }

        if (trigger) {
            triggerDecode();
        }

        node.addEventListener('mouseenter', start);
        node.addEventListener('mouseleave', stop);

        return {
            update(newTrigger: boolean) {
                if (newTrigger && !trigger) {
                    trigger = newTrigger;
                    triggerDecode();
                }
            },
            destroy() {
                clearInterval(interval);
                clearTimeout(initialTimeout);
                node.removeEventListener('mouseenter', start);
                node.removeEventListener('mouseleave', stop);
            },
        };
    }
</script>

<AppHead title="Welcome" />

{#if !bootCompleted}
    <BootSequence on:bootComplete={handleBootComplete} />
{/if}

<div
    class="transition-opacity duration-1000 {bootCompleted
        ? 'opacity-100'
        : 'opacity-0 h-0 overflow-hidden'}"
>
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
                    <div class="relative group flex items-center h-14">
                        <img
                            src="/images/ganesha-ctf-platform-logo.webp"
                            alt={appName}
                            class="h-14 w-auto object-contain transition-opacity duration-200 group-hover:opacity-0"
                        />
                        <span
                            class="absolute inset-0 flex items-center justify-start opacity-0 group-hover:opacity-100 font-bold text-xl text-foreground whitespace-nowrap"
                            use:hoverFlag={bootCompleted}
                        >
                            Ganesha CTF
                        </span>
                    </div>
                </div>

                <nav class="flex items-center gap-4">
                    {#if auth.user}
                        {#if (auth.user as any).is_admin}
                            <a
                                href="/admin"
                                class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                            >
                                <span use:hoverFlag={bootCompleted}
                                    >Admin Panel</span
                                >
                            </a>
                        {:else}
                            <Link
                                href={toUrl(dashboard())}
                                class="text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
                            >
                                <span use:hoverFlag={bootCompleted}
                                    >Dashboard</span
                                >
                            </Link>
                        {/if}
                    {:else}
                        <Link
                            href={toUrl(login())}
                            class="inline-flex h-9 items-center justify-center rounded-md bg-primary px-4 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90"
                        >
                            <span use:hoverFlag={bootCompleted}>Sign In</span>
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
                <h1
                    class="text-4xl font-extrabold tracking-tight sm:text-6xl mb-6 text-foreground"
                >
                    <span use:hoverFlag={bootCompleted}>Capture The Flag.</span>
                    <br />
                    <span
                        class="text-primary cursor-default"
                        use:hoverFlag={bootCompleted}
                        >{'{'}Def3nd_tHe_C0r3{'}'}</span
                    >
                </h1>

                <p
                    class="max-w-2xl mx-auto text-lg text-muted-foreground mb-10 leading-relaxed cursor-default"
                    use:hoverFlag={bootCompleted}
                >
                    Test your skills in a modern, secure, and competitive
                    environment. Decrypt, exploit, and secure systems to rise to
                    the top of the leaderboard.
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
                                <span use:hoverFlag={bootCompleted}
                                    >Go to Admin Panel</span
                                >
                                <ArrowRight class="h-4 w-4 ml-2" />
                            </a>
                        {:else}
                            <Link
                                href={toUrl(dashboard())}
                                class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 w-full sm:w-auto"
                            >
                                <span use:hoverFlag={bootCompleted}
                                    >Enter Dashboard</span
                                >
                                <ArrowRight class="h-4 w-4 ml-2" />
                            </Link>
                        {/if}
                    {:else}
                        <Link
                            href={toUrl(login())}
                            class="inline-flex h-12 items-center justify-center rounded-md bg-primary px-8 text-sm font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90 w-full sm:w-auto"
                        >
                            <span use:hoverFlag={bootCompleted}
                                >Join the Competition</span
                            >
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
                <p
                    class="text-center text-sm text-muted-foreground cursor-default"
                    use:hoverFlag={bootCompleted}
                >
                    © {new Date().getFullYear()}
                    {appName}. All rights reserved.
                </p>
            </div>
        </footer>
    </div>
</div>
