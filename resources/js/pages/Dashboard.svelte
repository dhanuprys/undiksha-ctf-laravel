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
    import { Trophy, Flag, Hash, Calendar, Shield, Activity, ArrowRight, Clock, Target, Play } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
    import { Button } from '@/components/ui/button';
    import { Progress } from '@/components/ui/progress';
    import CountdownTimer from '@/components/CountdownTimer.svelte';
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

<div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-6 max-w-7xl mx-auto w-full">
    
    <!-- Welcome Header & Quick Actions -->
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between mb-2">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Selamat Datang, {page.props.auth.user.name}!</h1>
            {#if activeEvent}
                <p class="text-muted-foreground mt-1 text-lg">Kompetisi: <span class="font-semibold text-foreground">{activeEvent.name} ({activeEvent.year})</span></p>
            {/if}
        </div>
        {#if activeEvent && currentTeam}
            <div class="flex gap-3">
                <Button href="/challenges" variant="default" class="gap-2 shadow-sm">
                    <Play class="h-4 w-4" />
                    Mulai Kerjakan
                </Button>
                <Button href="/leaderboard" variant="outline" class="gap-2">
                    <Trophy class="h-4 w-4" />
                    Papan Peringkat
                </Button>
            </div>
        {/if}
    </div>

    {#if !activeEvent}
        <Card class="flex h-64 flex-col items-center justify-center border-dashed text-center bg-muted/30">
            <Shield class="mx-auto mb-4 h-12 w-12 text-muted-foreground opacity-50" />
            <h3 class="mb-2 text-xl font-semibold">Tidak Ada Kompetisi Aktif</h3>
            <p class="text-muted-foreground max-w-md">Saat ini tidak ada kompetisi CTF yang sedang berlangsung. Silakan kembali lagi nanti.</p>
        </Card>
    {:else}
        
        {#if !currentTeam}
            <!-- Empty State / No Team Hero -->
            <Card class="relative overflow-hidden border-primary/20 bg-gradient-to-br from-primary/10 via-background to-background">
                <div class="absolute right-0 top-0 opacity-10 pointer-events-none transform translate-x-1/4 -translate-y-1/4">
                    <Shield class="w-64 h-64 text-primary" />
                </div>
                <CardContent class="p-10 flex flex-col items-center text-center relative z-10">
                    <div class="h-20 w-20 bg-primary/10 rounded-full flex items-center justify-center mb-6">
                        <Flag class="h-10 w-10 text-primary" />
                    </div>
                    <h2 class="text-3xl font-bold mb-3">Siap Untuk Berkompetisi?</h2>
                    <p class="text-lg text-muted-foreground max-w-xl mb-8">
                        Anda harus bergabung atau membuat tim terlebih dahulu sebelum dapat mengikuti kompetisi, melihat tantangan, atau masuk ke papan peringkat.
                    </p>
                    <Button href="/team" size="lg" class="gap-2 text-md px-8 shadow-md">
                        <Shield class="h-5 w-5" />
                        Kelola Tim Saya
                        <ArrowRight class="h-5 w-5 ml-1" />
                    </Button>
                </CardContent>
            </Card>
        {:else}
            <!-- Consolidated Stats Row -->
            <div class="grid gap-6 sm:grid-cols-3">
                <Card class="overflow-hidden relative group">
                    <div class="absolute right-0 top-0 h-full w-2 bg-gradient-to-b from-amber-400 to-amber-600 opacity-80"></div>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-medium text-muted-foreground">Total Poin</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/15 group-hover:bg-amber-500/25 transition-colors">
                                <Trophy class="h-5 w-5 text-amber-600 dark:text-amber-500" />
                            </div>
                        </div>
                        <h3 class="text-4xl font-black tracking-tight">{stats.total_points}</h3>
                    </CardContent>
                </Card>
                
                <Card class="overflow-hidden relative group">
                    <div class="absolute right-0 top-0 h-full w-2 bg-gradient-to-b from-blue-400 to-blue-600 opacity-80"></div>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-medium text-muted-foreground">Peringkat Tim</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-500/15 group-hover:bg-blue-500/25 transition-colors">
                                <Hash class="h-5 w-5 text-blue-600 dark:text-blue-500" />
                            </div>
                        </div>
                        <div class="flex items-baseline gap-1">
                            <span class="text-2xl font-bold text-muted-foreground">#</span>
                            <h3 class="text-4xl font-black tracking-tight">{stats.rank}</h3>
                        </div>
                    </CardContent>
                </Card>
                
                <Card class="overflow-hidden relative group">
                    <div class="absolute right-0 top-0 h-full w-2 bg-gradient-to-b from-green-400 to-green-600 opacity-80"></div>
                    <CardContent class="p-6">
                        <div class="flex items-center justify-between mb-4">
                            <p class="text-sm font-medium text-muted-foreground">Progres Tantangan</p>
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-500/15 group-hover:bg-green-500/25 transition-colors">
                                <Target class="h-5 w-5 text-green-600 dark:text-green-500" />
                            </div>
                        </div>
                        <div class="flex items-end gap-2 mb-2">
                            <h3 class="text-4xl font-black tracking-tight">{stats.solved_count}</h3>
                            <span class="text-muted-foreground font-medium mb-1">/ {stats.total_challenges}</span>
                        </div>
                        <Progress value={progressPercentage} class="h-2 w-full bg-secondary" />
                    </CardContent>
                </Card>
            </div>

            <!-- Split Content Area -->
            <div class="grid gap-6 lg:grid-cols-3">
                
                <!-- Left Column: Status & Info -->
                <div class="lg:col-span-2 flex flex-col gap-6">
                    <Card class="border-primary/20 bg-card shadow-sm">
                        <CardHeader class="pb-4">
                            <CardTitle class="flex items-center gap-2 text-lg">
                                <Calendar class="h-5 w-5 text-primary" />
                                Garis Waktu Kompetisi
                            </CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="grid gap-6 sm:grid-cols-2">
                                <div class="rounded-lg bg-muted/50 p-4 border border-border/50">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="h-2 w-2 rounded-full bg-blue-500"></div>
                                        <p class="text-sm font-semibold text-muted-foreground">WAKTU MULAI</p>
                                    </div>
                                    <p class="text-lg font-medium mb-3">
                                        {activeEvent.start_time ? new Date(activeEvent.start_time).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) : 'Belum ditentukan'}
                                    </p>
                                    <CountdownTimer targetDate={activeEvent.start_time} label="Mulai dalam" />
                                </div>
                                
                                <div class="rounded-lg bg-muted/50 p-4 border border-border/50">
                                    <div class="flex items-center gap-2 mb-2">
                                        <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                        <p class="text-sm font-semibold text-muted-foreground">WAKTU BERAKHIR</p>
                                    </div>
                                    <p class="text-lg font-medium mb-3">
                                        {activeEvent.end_time ? new Date(activeEvent.end_time).toLocaleString('id-ID', { dateStyle: 'long', timeStyle: 'short' }) : 'Belum ditentukan'}
                                    </p>
                                    <CountdownTimer targetDate={activeEvent.end_time} label="Sisa waktu" />
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
                
                <!-- Right Column: Recent Activity -->
                <div class="lg:col-span-1">
                    <Card class="h-full flex flex-col shadow-sm">
                        <CardHeader class="pb-3 border-b">
                            <CardTitle class="flex items-center gap-2 text-lg">
                                <Activity class="h-5 w-5 text-muted-foreground" />
                                Aktivitas Terakhir
                            </CardTitle>
                            <CardDescription>Tantangan yang baru saja diselesaikan oleh tim Anda.</CardDescription>
                        </CardHeader>
                        <CardContent class="flex-1 p-0">
                            {#if recentSubmissions.length > 0}
                                <div class="divide-y divide-border">
                                    {#each recentSubmissions as submission}
                                        <div class="p-4 hover:bg-muted/30 transition-colors">
                                            <div class="flex justify-between items-start mb-1">
                                                <h4 class="font-semibold text-md leading-tight">{submission.challenge?.title}</h4>
                                                <span class="inline-flex items-center rounded-full bg-primary/10 px-2 py-0.5 text-xs font-medium text-primary whitespace-nowrap ml-2">
                                                    +{submission.points_awarded} pts
                                                </span>
                                            </div>
                                            <div class="flex items-center justify-between text-xs text-muted-foreground mt-2">
                                                <span class="font-medium bg-secondary px-1.5 py-0.5 rounded text-secondary-foreground">{submission.challenge?.category?.name || 'Umum'}</span>
                                                <div class="flex items-center gap-1">
                                                    <Clock class="h-3 w-3" />
                                                    <span>{new Date(submission.created_at).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' })}</span>
                                                </div>
                                            </div>
                                        </div>
                                    {/each}
                                </div>
                                <div class="p-3 border-t bg-muted/20 text-center">
                                    <Link href="/challenges" class="text-xs text-primary font-medium hover:underline inline-flex items-center">
                                        Lihat semua tantangan <ArrowRight class="h-3 w-3 ml-1" />
                                    </Link>
                                </div>
                            {:else}
                                <div class="flex flex-col items-center justify-center h-full p-8 text-center min-h-[250px]">
                                    <div class="h-12 w-12 rounded-full bg-muted flex items-center justify-center mb-3">
                                        <Flag class="h-6 w-6 text-muted-foreground opacity-50" />
                                    </div>
                                    <p class="text-sm font-medium text-foreground mb-1">Belum ada aktivitas</p>
                                    <p class="text-xs text-muted-foreground">Tim Anda belum menyelesaikan tantangan apapun.</p>
                                </div>
                            {/if}
                        </CardContent>
                    </Card>
                </div>
            </div>
        {/if}
    {/if}
</div>
