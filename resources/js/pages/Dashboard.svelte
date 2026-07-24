<script module lang="ts">
    import { dashboard } from '@/routes';

    export const layout = {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: dashboard(),
            },
        ],
    };
</script>

<script lang="ts">
    import { page, Link } from '@inertiajs/svelte';
    import { Trophy, Flag, Hash, Calendar, Shield, Activity, ArrowRight, Clock, Target, Play, Zap, CheckCircle2 } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import CountdownTimer from '@/components/CountdownTimer.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Progress } from '@/components/ui/progress';
    import type { Team, Event } from '@/types/ctf';

    let {
        stats,
        recentSubmissions = [],
    }: {
        stats: {
            solved_count: number;
            total_points: number;
            rank: number | string;
            total_challenges: number;
        },
        recentSubmissions: any[]
    } = $props();

    let activeEvent = $derived(page.props.activeEvent as Event | null);
    let currentTeam = $derived(page.props.auth.user.current_team as Team | null);
    
    let progressPercentage = $derived(
        stats.total_challenges > 0 
        ? Math.round((stats.solved_count / stats.total_challenges) * 100) 
        : 0
    );
</script>

<AppHead title="Dashboard" />

<div class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-6 max-w-7xl mx-auto w-full">
    
    <!-- Welcome Header & Quick Actions -->
    <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-4xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-foreground to-foreground/70">
                Selamat Datang, {page.props.auth.user.name}!
            </h1>
            {#if activeEvent}
                <div class="flex items-center gap-2 mt-2">
                    <span class="relative flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                    </span>
                    <p class="text-muted-foreground text-lg">
                        Kompetisi Aktif: <span class="font-semibold text-foreground">{activeEvent.name} ({activeEvent.year})</span>
                    </p>
                </div>
            {/if}
        </div>
        {#if activeEvent && currentTeam}
            <div class="flex flex-wrap gap-3">
                <Button variant="outline" class="gap-2 h-11 px-6 rounded-full border-border/60 shadow-sm hover:bg-muted/50 transition-colors" asChild>
                    {#snippet children(props)}
                        <Link href="/leaderboard" {...props}>
                            <Trophy class="h-4 w-4 text-amber-500" />
                            Papan Peringkat
                        </Link>
                    {/snippet}
                </Button>
                <Button variant="default" class="gap-2 h-11 px-6 rounded-full shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5" asChild>
                    {#snippet children(props)}
                        <Link href="/challenges" {...props}>
                            <Play class="h-4 w-4" />
                            Mulai Kerjakan
                        </Link>
                    {/snippet}
                </Button>
            </div>
        {/if}
    </div>

    {#if !activeEvent}
        <Card class="flex h-72 flex-col items-center justify-center border-dashed border-border/60 bg-muted/10 text-center shadow-none">
            <div class="h-20 w-20 rounded-full bg-muted/50 flex items-center justify-center mb-6 border border-border/50">
                <Shield class="h-10 w-10 text-muted-foreground/60" />
            </div>
            <h3 class="mb-2 text-2xl font-bold tracking-tight">Tidak Ada Kompetisi Aktif</h3>
            <p class="text-muted-foreground max-w-md text-base">Saat ini tidak ada kompetisi CTF yang sedang berlangsung. Silakan kembali lagi nanti.</p>
        </Card>
    {:else}
        
        {#if !currentTeam}
            <!-- Empty State / No Team Hero -->
            <div class="relative overflow-hidden rounded-3xl border border-border/60 bg-gradient-to-br from-card to-muted/20 shadow-sm transition-all duration-500 hover:shadow-md">
                <!-- Decorative background elements -->
                <div class="absolute top-0 right-0 -mt-16 -mr-16 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-16 -ml-16 h-64 w-64 rounded-full bg-blue-500/5 blur-3xl"></div>
                
                <div class="px-6 py-16 sm:p-16 flex flex-col items-center text-center relative z-10">
                    <div class="h-20 w-20 bg-background shadow-sm border border-border/60 rounded-2xl flex items-center justify-center mb-8 rotate-3 transition-transform hover:rotate-6">
                        <Flag class="h-10 w-10 text-primary" />
                    </div>
                    <h2 class="text-4xl font-extrabold tracking-tight mb-4">Siap Untuk Berkompetisi?</h2>
                    <p class="text-lg text-muted-foreground max-w-2xl mb-10 leading-relaxed">
                        Anda harus bergabung atau membuat tim terlebih dahulu sebelum dapat mengikuti kompetisi, melihat tantangan, atau masuk ke papan peringkat.
                    </p>
                    <Button size="lg" class="gap-2 text-base px-8 h-12 rounded-full shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5" asChild>
                        {#snippet children(props)}
                            <Link href="/team" {...props}>
                                <Shield class="h-5 w-5" />
                                Kelola Tim Saya
                                <ArrowRight class="h-5 w-5 ml-1" />
                            </Link>
                        {/snippet}
                    </Button>
                </div>
            </div>
        {:else}
            <!-- Consolidated Stats Row -->
            <div class="grid gap-6 sm:grid-cols-3">
                <Card class="relative overflow-hidden transition-all duration-300 hover:shadow-md border-border/60 bg-gradient-to-br from-card to-muted/10 group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <Trophy class="h-24 w-24 text-amber-500 transform translate-x-4 -translate-y-4" />
                    </div>
                    <CardContent class="p-6 relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="h-10 w-10 rounded-xl bg-amber-500/10 flex items-center justify-center border border-amber-500/20">
                                <Trophy class="h-5 w-5 text-amber-500" />
                            </div>
                            <p class="text-sm font-semibold tracking-wider text-muted-foreground uppercase">Total Poin</p>
                        </div>
                        <div class="flex items-baseline gap-1.5">
                            <h3 class="text-5xl font-black tracking-tight">{stats.total_points}</h3>
                            <span class="text-lg font-bold text-muted-foreground">pts</span>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="relative overflow-hidden transition-all duration-300 hover:shadow-md border-border/60 bg-gradient-to-br from-card to-muted/10 group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <Hash class="h-24 w-24 text-blue-500 transform translate-x-4 -translate-y-4" />
                    </div>
                    <CardContent class="p-6 relative z-10">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="h-10 w-10 rounded-xl bg-blue-500/10 flex items-center justify-center border border-blue-500/20">
                                <Hash class="h-5 w-5 text-blue-500" />
                            </div>
                            <p class="text-sm font-semibold tracking-wider text-muted-foreground uppercase">Peringkat Tim</p>
                        </div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-bold text-muted-foreground/50">#</span>
                            <h3 class="text-5xl font-black tracking-tight">{stats.rank}</h3>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="relative overflow-hidden transition-all duration-300 hover:shadow-md border-border/60 bg-gradient-to-br from-card to-muted/10 group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <Target class="h-24 w-24 text-green-500 transform translate-x-4 -translate-y-4" />
                    </div>
                    <CardContent class="p-6 relative z-10">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="h-10 w-10 rounded-xl bg-green-500/10 flex items-center justify-center border border-green-500/20">
                                <Target class="h-5 w-5 text-green-500" />
                            </div>
                            <p class="text-sm font-semibold tracking-wider text-muted-foreground uppercase">Tantangan</p>
                        </div>
                        <div class="flex items-end justify-between mb-3">
                            <div class="flex items-baseline gap-1.5">
                                <h3 class="text-5xl font-black tracking-tight">{stats.solved_count}</h3>
                                <span class="text-lg font-bold text-muted-foreground">/ {stats.total_challenges}</span>
                            </div>
                            <span class="text-sm font-bold text-green-600 dark:text-green-400 bg-green-500/10 px-2 py-1 rounded-md">{progressPercentage}%</span>
                        </div>
                        <Progress value={progressPercentage} class="h-2.5 w-full bg-muted/50" />
                    </CardContent>
                </Card>
            </div>

            <!-- Split Content Area -->
            <div class="grid gap-6 lg:grid-cols-3">
                
                <!-- Left Column: Status & Info -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <Card class="shadow-sm border-border/60 bg-card overflow-hidden">
                        <div class="h-1.5 w-full bg-gradient-to-r from-blue-500/40 via-primary/40 to-purple-500/40"></div>
                        <CardHeader class="pb-6 pt-6">
                            <CardTitle class="flex items-center gap-2 text-xl">
                                <Calendar class="h-5 w-5 text-primary" />
                                Garis Waktu Kompetisi
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div class="rounded-xl bg-muted/20 p-6 border border-border/40 relative overflow-hidden group hover:border-primary/20 transition-colors">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-full blur-2xl -mr-10 -mt-10 transition-opacity opacity-0 group-hover:opacity-100"></div>
                                    <div class="text-xs font-bold text-muted-foreground tracking-widest mb-3 uppercase flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                        Waktu Mulai
                                    </div>
                                    <p class="text-xl font-semibold mb-4 text-foreground">
                                        {activeEvent.start_time ? new Date(activeEvent.start_time).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) : 'Belum ditentukan'}
                                    </p>
                                    <div class="bg-background/80 rounded-lg p-3 border border-border/50 shadow-sm">
                                        <CountdownTimer targetDate={activeEvent.start_time} label="Mulai dalam" />
                                    </div>
                                </div>
                                
                                <div class="rounded-xl bg-muted/20 p-6 border border-border/40 relative overflow-hidden group hover:border-red-500/20 transition-colors">
                                    <div class="absolute top-0 right-0 w-32 h-32 bg-red-500/5 rounded-full blur-2xl -mr-10 -mt-10 transition-opacity opacity-0 group-hover:opacity-100"></div>
                                    <div class="text-xs font-bold text-muted-foreground tracking-widest mb-3 uppercase flex items-center gap-2">
                                        <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                        Batas Waktu
                                    </div>
                                    <p class="text-xl font-semibold mb-4 text-foreground">
                                        {activeEvent.end_time ? new Date(activeEvent.end_time).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) : 'Belum ditentukan'}
                                    </p>
                                    <div class="bg-background/80 rounded-lg p-3 border border-border/50 shadow-sm">
                                        <CountdownTimer targetDate={activeEvent.end_time} label="Sisa waktu" />
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                
                <!-- Right Column: Recent Activity -->
                <div class="lg:col-span-1">
                    <Card class="h-full flex flex-col shadow-sm border-border/60 bg-card">
                        <CardHeader class="pb-4 border-b border-border/40 bg-muted/5">
                            <div class="flex items-center justify-between">
                                <CardTitle class="flex items-center gap-2 text-lg">
                                    <Activity class="h-5 w-5 text-primary" />
                                    Aktivitas Terakhir
                                </CardTitle>
                                <span class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-md">
                                    Top {recentSubmissions.length}
                                </span>
                            </div>
                        </CardHeader>
                        <CardContent class="flex-1 p-0">
                            {#if recentSubmissions.length > 0}
                                <div class="divide-y divide-border/40 relative">
                                    <!-- Vertical timeline line -->
                                    <div class="absolute left-[31px] top-6 bottom-6 w-px bg-border/60"></div>
                                    {#each recentSubmissions as submission (submission.id)}
                                        <div class="p-4 group hover:bg-muted/30 transition-colors flex gap-4 items-start relative z-10">
                                            <div class="mt-0.5 shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-500 shadow-sm bg-card">
                                                <CheckCircle2 class="h-4 w-4" />
                                            </div>
                                            <div class="flex-1 min-w-0 bg-background/50 p-2 -my-2 rounded-md transition-colors group-hover:bg-transparent">
                                                <div class="flex justify-between items-start mb-1 gap-2">
                                                    <h4 class="font-bold text-sm leading-tight text-foreground group-hover:text-primary transition-colors truncate">
                                                        {submission.challenge?.title}
                                                    </h4>
                                                    <span class="shrink-0 inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-green-600 dark:text-green-400 bg-green-500/10 rounded border border-green-500/20 whitespace-nowrap">
                                                        +{submission.points_awarded} pts
                                                    </span>
                                                </div>
                                                <div class="flex items-center justify-between text-xs text-muted-foreground mt-1.5">
                                                    <span class="font-medium flex items-center gap-1.5">
                                                        <div class="h-1.5 w-1.5 rounded-full bg-muted-foreground/50"></div>
                                                        {submission.challenge?.category?.name || 'Umum'}
                                                    </span>
                                                    <div class="flex items-center gap-1.5">
                                                        <Clock class="h-3 w-3" />
                                                        <span>{new Date(submission.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                                <div class="p-4 border-t border-border/40 bg-muted/5 text-center">
                                    <Link href="/challenges" class="text-xs text-muted-foreground font-semibold hover:text-primary transition-colors inline-flex items-center group">
                                        Lihat semua tantangan 
                                        <ArrowRight class="h-3 w-3 ml-1 transform group-hover:translate-x-1 transition-transform" />
                                    </Link>
                                </div>
                            {:else}
                                <div class="flex flex-col items-center justify-center h-full p-8 text-center min-h-[300px]">
                                    <div class="h-16 w-16 rounded-2xl bg-muted/50 border border-border/60 flex items-center justify-center mb-4 shadow-inner">
                                        <Zap class="h-8 w-8 text-muted-foreground/50" />
                                    </div>
                                    <p class="text-base font-bold text-foreground mb-1">Belum ada aktivitas</p>
                                    <p class="text-sm text-muted-foreground max-w-[200px]">Tim Anda belum menyelesaikan tantangan apapun.</p>
                                </div>
                            {/if}
                        </CardContent>
                    </Card>
                </div>
            </div>
        {/if}
    {/if}
</div>
