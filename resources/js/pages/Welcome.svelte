<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import { Terminal, ArrowRight } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { toUrl } from '@/lib/utils';
    import { dashboard, login, register } from '@/routes';

    const auth = $derived(page.props.auth);
    const appName = import.meta.env.VITE_APP_NAME || 'CTF Undiksha';
</script>

<AppHead title="Welcome" />

<!-- Force cold light theme (slate background with subtle blue grid) -->
<div class="flex min-h-screen flex-col bg-slate-50 text-slate-900 relative overflow-hidden selection:bg-cyan-100 selection:text-cyan-900">
    
    <!-- Background Grid -->
    <div class="absolute inset-0 -z-20 bg-[radial-gradient(#cbd5e1_1px,transparent_1px)] bg-[size:32px_32px] pointer-events-none"></div>
    <div class="absolute inset-0 -z-20 bg-[radial-gradient(ellipse_60%_50%_at_50%_50%,rgba(248,250,252,0.6)_60%,transparent_100%)] pointer-events-none"></div>
    
    <!-- Cold Blue & Indigo Glow Orbs (Light Mode) -->
    <div class="absolute top-1/4 left-1/4 -z-10 h-[400px] w-[400px] rounded-full bg-cyan-200/20 blur-[120px] pointer-events-none"></div>
    <div class="absolute bottom-1/3 right-1/4 -z-10 h-[500px] w-[500px] rounded-full bg-indigo-200/20 blur-[150px] pointer-events-none"></div>

    <!-- HUD Frame Accents (Corner brackets for mysterious sci-fi vibe in light mode) -->
    <div class="absolute top-6 left-6 w-8 h-8 border-t border-l border-slate-300 pointer-events-none"></div>
    <div class="absolute top-6 right-6 w-8 h-8 border-t border-r border-slate-300 pointer-events-none"></div>
    <div class="absolute bottom-6 left-6 w-8 h-8 border-b border-l border-slate-300 pointer-events-none"></div>
    <div class="absolute bottom-6 right-6 w-8 h-8 border-b border-r border-slate-300 pointer-events-none"></div>

    <!-- Navbar (No bottom border, transparent blur) -->
    <header class="sticky top-0 z-50 w-full bg-slate-50/40 backdrop-blur-md">
        <div class="container mx-auto flex h-16 items-center justify-between px-6 sm:px-8">
            <div class="flex items-center gap-2 font-mono">
                <Terminal class="h-5 w-5 text-cyan-600" />
                <span class="text-sm font-bold tracking-[0.2em] uppercase text-slate-800">
                    UNDIKSHA CTF<span class="animate-pulse text-cyan-600">_</span>
                </span>
            </div>
            
            <nav class="flex items-center gap-4">
                {#if auth.user}
                    {#if (auth.user as any).is_admin}
                        <a href="/admin" class="inline-flex h-9 items-center justify-center rounded-lg bg-white border border-slate-200 px-4 text-xs font-mono font-bold tracking-wider text-slate-700 hover:text-slate-900 hover:border-cyan-500/30 transition-all shadow-sm">
                            ADMIN_PANEL
                        </a>
                    {:else}
                        <Link href={toUrl(dashboard())} class="inline-flex h-9 items-center justify-center rounded-lg bg-white border border-slate-200 px-4 text-xs font-mono font-bold tracking-wider text-slate-700 hover:text-slate-900 hover:border-cyan-500/30 transition-all shadow-sm">
                            PARTICIPANT_DASHBOARD
                        </Link>
                    {/if}
                {:else}
                    <Link href={toUrl(login())} class="text-xs font-mono font-bold tracking-wider text-slate-500 hover:text-slate-900 transition-colors">
                        SIGN_IN
                    </Link>
                    <Link href={toUrl(register())} class="inline-flex h-9 items-center justify-center rounded-lg bg-cyan-600 px-4 text-xs font-mono font-bold tracking-wider text-white shadow-[0_4px_12px_rgba(8,145,178,0.15)] hover:shadow-[0_4px_20px_rgba(8,145,178,0.3)] hover:bg-cyan-500 transition-all">
                        REGISTER
                    </Link>
                {/if}
            </nav>
        </div>
    </header>

    <!-- Hero Section with split-screen layout for improved desktop experience -->
    <main class="flex-1 flex items-center justify-center relative px-6 py-12 md:py-20">
        <div class="container mx-auto max-w-7xl grid lg:grid-cols-12 gap-12 items-center relative z-10">
            
            <!-- Left Side Details -->
            <div class="lg:col-span-7 text-left">
                <!-- Cyber Title -->
                <h1 class="text-4xl font-light tracking-tight sm:text-5xl lg:text-6xl mb-8 leading-tight uppercase">
                    <span class="block font-mono text-xs tracking-[0.4em] text-slate-400 mb-4">INITIATING COMPETITION ENVIRONMENT</span>
                    <span class="font-extrabold text-slate-800 tracking-tighter">
                        DECRYPT. SOLVE.
                    </span>
                    <span class="block font-black text-cyan-600 drop-shadow-[0_0_20px_rgba(8,145,178,0.15)] tracking-tighter mt-1">
                        CAPTURE THE FLAG.
                    </span>
                </h1>
                
                <!-- Mysterious Terminal/Decrypt Text -->
                <p class="max-w-xl text-sm font-mono text-slate-500 leading-relaxed mb-10 border-l-2 border-slate-200 pl-4">
                    // MAIN RUNTIME: 2026.ctf.platform.exe <br/>
                    // CHALLENGE SUITE IS ONLINE. RUNNING DECRYPTION...<br/>
                    // TEAM ACCESS RESTRICTED TO VALIDATED CREDENTIALS.
                </p>
                
                <!-- Action Trigger -->
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    {#if auth.user}
                        {#if (auth.user as any).is_admin}
                            <a href="/admin" class="group inline-flex h-12 items-center justify-center rounded-xl bg-cyan-600 px-8 text-sm font-mono font-bold tracking-wider text-white shadow-[0_4px_12px_rgba(8,145,178,0.15)] hover:shadow-[0_4px_25px_rgba(8,145,178,0.35)] hover:bg-cyan-500 transition-all w-full sm:w-auto">
                                ACCESS_ADMIN_INTERFACE
                                <ArrowRight class="h-4 w-4 ml-2 transition-transform group-hover:translate-x-1" />
                            </a>
                        {:else}
                            <Link href={toUrl(dashboard())} class="group inline-flex h-12 items-center justify-center rounded-xl bg-cyan-600 px-8 text-sm font-mono font-bold tracking-wider text-white shadow-[0_4px_12px_rgba(8,145,178,0.15)] hover:shadow-[0_4px_25px_rgba(8,145,178,0.35)] hover:bg-cyan-500 transition-all w-full sm:w-auto">
                                INITIALIZE_DASHBOARD
                                <ArrowRight class="h-4 w-4 ml-2 transition-transform group-hover:translate-x-1" />
                            </Link>
                        {/if}
                    {:else}
                        <Link href={toUrl(register())} class="group inline-flex h-12 items-center justify-center rounded-xl bg-cyan-600 px-8 text-sm font-mono font-bold tracking-wider text-white shadow-[0_4px_12px_rgba(8,145,178,0.15)] hover:shadow-[0_4px_25px_rgba(8,145,178,0.35)] hover:bg-cyan-500 transition-all w-full sm:w-auto">
                            CREATE_CREDENTIALS
                            <ArrowRight class="h-4 w-4 ml-2 transition-transform group-hover:translate-x-1" />
                        </Link>
                        <Link href={toUrl(login())} class="inline-flex h-12 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:text-slate-900 hover:border-slate-300 shadow-sm px-8 text-sm font-mono font-bold tracking-wider transition-all w-full sm:w-auto">
                            AUTHENTICATE
                        </Link>
                    {/if}
                </div>
            </div>

            <!-- Right Side (Cool Interactive Mock Terminal Component) -->
            <div class="lg:col-span-5 hidden lg:block relative group">
                <!-- Icy ambient glow behind the card -->
                <div class="absolute -inset-1 rounded-3xl bg-gradient-to-tr from-cyan-300 to-indigo-300 opacity-20 blur-xl group-hover:opacity-35 transition duration-1000"></div>
                
                <div class="relative rounded-3xl border border-white/80 bg-white/70 backdrop-blur-xl p-6 shadow-[0_20px_50px_rgba(0,0,0,0.03)] font-mono text-xs text-slate-600 space-y-4">
                    <!-- Window Header -->
                    <div class="flex items-center justify-between pb-3 border-b border-slate-200/60">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                            <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                            <div class="w-3 h-3 rounded-full bg-slate-200"></div>
                        </div>
                        <span class="text-[9px] text-slate-400 font-bold uppercase tracking-wider">terminal_session.sh</span>
                    </div>
                    
                    <!-- Terminal Logs -->
                    <div class="space-y-3">
                        <div class="flex gap-2">
                            <span class="text-cyan-600 font-bold">$</span>
                            <span>initialize --mode=ctf_competition</span>
                        </div>
                        <div class="text-slate-400 pl-4 space-y-1">
                            <div>[INFO] Loading cryptography module... <span class="text-cyan-600 font-semibold">OK</span></div>
                            <div>[INFO] Connecting to central server... <span class="text-cyan-600 font-semibold">Connected</span></div>
                            <div>[INFO] Preparing security challenges... <span class="text-cyan-600 font-semibold">Ready</span></div>
                        </div>
                        <div class="flex gap-2 pt-1">
                            <span class="text-cyan-600 font-bold">$</span>
                            <span>check_status --port=443</span>
                        </div>
                        <div class="pl-4 text-emerald-600 font-bold flex items-center gap-1.5">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                            STATUS: SYSTEM RUNNING OPTIMALLY
                        </div>
                        <div class="flex gap-2 pt-1">
                            <span class="text-cyan-600 font-bold">$</span>
                            <span>cat flag.txt</span>
                        </div>
                        <div class="pl-4 font-bold text-slate-800 tracking-wider">
                            {'UNDIKSHA{myst3r10us_l1ght_m0d3}'}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </main>

    <!-- Footer (No top border, transparent background) -->
    <footer class="py-6 bg-slate-50/10 backdrop-blur-md">
        <div class="container mx-auto flex flex-col items-center justify-center gap-4 md:h-10 md:flex-row px-6 sm:px-8">
            <p class="text-center text-xs font-mono text-slate-400">
                © {new Date().getFullYear()} {appName} // [STATUS_PORT: ACTIVE] // ALL RIGHTS RESERVED
            </p>
        </div>
    </footer>
</div>
