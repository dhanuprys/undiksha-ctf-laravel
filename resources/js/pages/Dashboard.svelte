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
    import {
        Trophy,
        Flag,
        Hash,
        Calendar,
        Shield,
        Activity,
        ArrowRight,
        Clock,
        Target,
        Play,
        Zap,
        CheckCircle2,
        XCircle,
        User,
    } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import CountdownTimer from '@/components/CountdownTimer.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Card,
        CardContent,
        CardHeader,
        CardTitle,
    } from '@/components/ui/card';
    import { Progress } from '@/components/ui/progress';
    import { formatDateTime, formatTime } from '@/lib/formatDate';
    import { index as challengesIndexRoute } from '@/routes/challenges';
    import { index as leaderboardIndexRoute } from '@/routes/leaderboard';
    import { show as showTeamRoute } from '@/routes/team';
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
        };
        recentSubmissions: any[];
    } = $props();

    let activeEvent = $derived(page.props.activeEvent as Event | null);
    let currentTeam = $derived(
        page.props.auth.user.current_team as Team | null,
    );

    let progressPercentage = $derived(
        stats.total_challenges > 0
            ? Math.round((stats.solved_count / stats.total_challenges) * 100)
            : 0,
    );
</script>

<AppHead title="Dashboard" />

<div
    class="flex h-full flex-1 flex-col gap-8 overflow-x-auto p-6 max-w-7xl mx-auto w-full"
>
    <!-- Welcome Header & Quick Actions -->
    <div
        class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between"
    >
        <div class="space-y-1">
            <h1 class="text-3xl font-bold tracking-tight text-foreground">
                Selamat Datang, {page.props.auth.user.name}!
            </h1>
            {#if activeEvent}
                <div class="flex items-center gap-2 mt-1.5">
                    <span class="relative flex h-2.5 w-2.5">
                        <span
                            class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"
                        ></span>
                    </span>
                    <p class="text-muted-foreground text-sm font-medium">
                        Kompetisi Aktif: <span
                            class="font-semibold text-foreground"
                            >{activeEvent.name} ({activeEvent.year})</span
                        >
                    </p>
                </div>
            {/if}
        </div>
        {#if activeEvent && currentTeam}
            <div class="flex flex-wrap gap-3">
                <Button
                    variant="outline"
                    class="gap-2 h-10 px-5 rounded-full border-border/60 shadow-sm hover:bg-muted/50 transition-colors text-sm font-semibold"
                    asChild
                >
                    {#snippet children(props)}
                        <Link href={leaderboardIndexRoute().url} {...props}>
                            <Trophy class="h-4 w-4 text-amber-500" />
                            Papan Peringkat
                        </Link>
                    {/snippet}
                </Button>
                <Button
                    variant="default"
                    class="gap-2 h-10 px-5 rounded-full shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5 text-sm font-semibold"
                    asChild
                >
                    {#snippet children(props)}
                        <Link href={challengesIndexRoute().url} {...props}>
                            <Play class="h-4 w-4" />
                            Mulai Kerjakan
                        </Link>
                    {/snippet}
                </Button>
            </div>
        {/if}
    </div>

    {#if !activeEvent}
        <Card
            class="flex h-72 flex-col items-center justify-center border-dashed border-border/60 bg-muted/10 text-center shadow-none"
        >
            <div
                class="h-20 w-20 rounded-full bg-muted/50 flex items-center justify-center mb-6 border border-border/50"
            >
                <Shield class="h-10 w-10 text-muted-foreground/60" />
            </div>
            <h3 class="mb-2 text-2xl font-bold tracking-tight">
                Tidak Ada Kompetisi Aktif
            </h3>
            <p class="text-muted-foreground max-w-md text-sm">
                Saat ini tidak ada kompetisi CTF yang sedang berlangsung.
                Silakan kembali lagi nanti.
            </p>
        </Card>
    {:else if !currentTeam}
        <!-- Empty State / No Team Hero -->
        <div
            class="relative overflow-hidden rounded-3xl border border-border/60 bg-card shadow-sm transition-all duration-500 hover:shadow-md"
        >
            <div
                class="px-6 py-12 sm:p-16 flex flex-col items-center text-center relative z-10"
            >
                <div
                    class="h-16 w-16 bg-background shadow-sm border border-border/60 rounded-2xl flex items-center justify-center mb-6 rotate-3 transition-transform hover:rotate-6"
                >
                    <Flag class="h-8 w-8 text-primary" />
                </div>
                <h2 class="text-3xl font-bold tracking-tight mb-3">
                    Siap Untuk Berkompetisi?
                </h2>
                <p
                    class="text-base text-muted-foreground max-w-xl mb-8 leading-relaxed"
                >
                    Anda harus bergabung atau membuat tim terlebih dahulu
                    sebelum dapat mengikuti kompetisi, melihat tantangan, atau
                    masuk ke papan peringkat.
                </p>
                <Button
                    size="lg"
                    class="gap-2 text-sm px-6 h-11 rounded-full shadow-md hover:shadow-lg transition-all hover:-translate-y-0.5 font-semibold"
                    asChild
                >
                    {#snippet children(props)}
                        <Link href={showTeamRoute().url} {...props}>
                            <Shield class="h-4 w-4" />
                            Kelola Tim Saya
                            <ArrowRight class="h-4 w-4 ml-1" />
                        </Link>
                    {/snippet}
                </Button>
            </div>
        </div>
    {:else}
        <!-- Consolidated Stats Row -->
        <div class="grid gap-6 sm:grid-cols-3">
            <Card
                class="relative overflow-hidden transition-all duration-300 hover:shadow-md border-border/60 bg-card"
            >
                <CardHeader class="pb-2">
                    <CardTitle
                        class="flex items-center gap-1.5 text-xs font-bold tracking-wider text-muted-foreground/80 uppercase"
                    >
                        <Trophy class="h-3.5 w-3.5 text-amber-500" />
                        Total Poin
                    </CardTitle>
                </CardHeader>
                <CardContent class="pb-6">
                    <div class="flex items-baseline gap-1.5">
                        <h3
                            class="text-5xl font-black tracking-tight text-foreground"
                        >
                            {stats.total_points}
                        </h3>
                        <span
                            class="text-sm font-semibold text-muted-foreground"
                            >pts</span
                        >
                    </div>
                </CardContent>
            </Card>

            <Card
                class="relative overflow-hidden transition-all duration-300 hover:shadow-md border-border/60 bg-card"
            >
                <CardHeader class="pb-2">
                    <CardTitle
                        class="flex items-center gap-1.5 text-xs font-bold tracking-wider text-muted-foreground/80 uppercase"
                    >
                        <Hash class="h-3.5 w-3.5 text-blue-500" />
                        Peringkat Tim
                    </CardTitle>
                </CardHeader>
                <CardContent class="pb-6">
                    <div class="flex items-baseline gap-1">
                        <span
                            class="text-2xl font-bold text-muted-foreground/50"
                            >#</span
                        >
                        <h3
                            class="text-5xl font-black tracking-tight text-foreground"
                        >
                            {stats.rank}
                        </h3>
                    </div>
                    <div
                        class="mt-2 text-[10px] font-medium text-muted-foreground/60 italic"
                    >
                        *Diperbarui setiap 60 detik
                    </div>
                </CardContent>
            </Card>

            <Card
                class="relative overflow-hidden transition-all duration-300 hover:shadow-md border-border/60 bg-card"
            >
                <CardHeader class="pb-2">
                    <CardTitle
                        class="flex items-center gap-1.5 text-xs font-bold tracking-wider text-muted-foreground/80 uppercase"
                    >
                        <Target class="h-3.5 w-3.5 text-green-500" />
                        Tantangan
                    </CardTitle>
                </CardHeader>
                <CardContent class="pb-6">
                    <div class="flex items-end justify-between mb-3 mt-1">
                        <div class="flex items-baseline gap-1.5">
                            <h3
                                class="text-4xl font-black tracking-tight text-foreground"
                            >
                                {stats.solved_count}
                            </h3>
                            <span
                                class="text-sm font-semibold text-muted-foreground"
                                >/ {stats.total_challenges}</span
                            >
                        </div>
                        <span
                            class="text-xs font-bold text-green-600 dark:text-green-400 bg-green-500/10 px-2 py-0.5 rounded border border-green-500/20"
                            >{progressPercentage}%</span
                        >
                    </div>
                    <Progress
                        value={progressPercentage}
                        class="h-1.5 w-full bg-muted"
                    />
                </CardContent>
            </Card>
        </div>

        <!-- Split Content Area -->
        <div class="grid gap-6 lg:grid-cols-3 items-start">
            <!-- Left Column: Status & Info -->
            <div class="lg:col-span-2 flex flex-col gap-6">
                <Card
                    class="shadow-sm border-border/60 bg-card overflow-hidden flex flex-col"
                >
                    <CardHeader
                        class="pb-4 pt-5 border-b border-border/40 bg-muted/5 shrink-0"
                    >
                        <CardTitle
                            class="flex items-center gap-2 text-xl font-bold text-foreground"
                        >
                            <Calendar class="h-5 w-5 text-primary" />
                            Jadwal Kompetisi
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-6">
                        <div class="grid gap-6 sm:grid-cols-2 relative">
                            <div
                                class="relative z-10 rounded-2xl bg-muted/20 p-5 border border-border/50 hover:bg-muted/30 transition-colors"
                            >
                                <div class="flex items-center gap-4 mb-5">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-600 dark:text-blue-400"
                                    >
                                        <Play class="h-5 w-5 ml-0.5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[10px] font-extrabold text-muted-foreground/80 uppercase tracking-widest mb-0.5"
                                        >
                                            Waktu Dimulai
                                        </p>
                                        <p
                                            class="text-sm font-semibold text-foreground truncate"
                                        >
                                            {formatDateTime(
                                                activeEvent.start_time,
                                            )}
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="bg-background rounded-xl p-3 border border-border/40 shadow-sm"
                                >
                                    <CountdownTimer
                                        targetDate={activeEvent.start_time}
                                        label="Dimulai Dalam"
                                        expiredText="Telah Dimulai"
                                    />
                                </div>
                            </div>

                            <div
                                class="relative z-10 rounded-2xl bg-muted/20 p-5 border border-border/50 hover:bg-muted/30 transition-colors"
                            >
                                <div class="flex items-center gap-4 mb-5">
                                    <div
                                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-400"
                                    >
                                        <Target class="h-5 w-5" />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-[10px] font-extrabold text-muted-foreground/80 uppercase tracking-widest mb-0.5"
                                        >
                                            Waktu Berakhir
                                        </p>
                                        <p
                                            class="text-sm font-semibold text-foreground truncate"
                                        >
                                            {formatDateTime(
                                                activeEvent.end_time,
                                            )}
                                        </p>
                                    </div>
                                </div>
                                <div
                                    class="bg-background rounded-xl p-3 border border-border/40 shadow-sm"
                                >
                                    <CountdownTimer
                                        targetDate={activeEvent.end_time}
                                        label="Sisa Waktu"
                                        expiredText="Telah Berakhir"
                                    />
                                </div>
                            </div>
                        </div>
                    </CardContent>
                </Card>
            </div>

            <!-- Right Column: Recent Activity -->
            <div class="lg:col-span-1">
                <Card
                    class="h-full flex flex-col shadow-sm border-border/60 bg-card"
                >
                    <CardHeader
                        class="pb-4 border-b border-border/40 bg-muted/5"
                    >
                        <div class="flex items-center justify-between">
                            <CardTitle
                                class="flex items-center gap-2 text-lg font-bold text-foreground"
                            >
                                <Activity class="h-5 w-5 text-primary" />
                                Aktivitas Terakhir
                            </CardTitle>
                            <span
                                class="bg-primary/10 text-primary text-[10px] uppercase tracking-wider font-extrabold px-2 py-0.5 rounded border border-primary/20"
                            >
                                Top {recentSubmissions.length}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="flex-1 p-0">
                        {#if recentSubmissions.length > 0}
                            <div class="divide-y divide-border/40 relative">
                                <!-- Vertical timeline line -->
                                <div
                                    class="absolute left-[31px] top-6 bottom-6 w-px bg-border/60"
                                ></div>
                                {#each recentSubmissions as submission (submission.id)}
                                    <div
                                        class="p-4 group hover:bg-muted/30 transition-colors flex gap-4 items-start relative z-10"
                                    >
                                        {#if submission.is_correct}
                                            <div
                                                class="mt-0.5 shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-500 shadow-sm bg-card"
                                            >
                                                <CheckCircle2 class="h-4 w-4" />
                                            </div>
                                        {:else}
                                            <div
                                                class="mt-0.5 shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-red-500/10 border border-red-500/20 text-red-600 dark:text-red-500 shadow-sm bg-card"
                                            >
                                                <XCircle class="h-4 w-4" />
                                            </div>
                                        {/if}
                                        <div
                                            class="flex-1 min-w-0 bg-background/50 p-2 -my-2 rounded-md transition-colors group-hover:bg-transparent"
                                        >
                                            <div
                                                class="flex justify-between items-start mb-1 gap-2"
                                            >
                                                <h4
                                                    class="font-bold text-sm leading-tight text-foreground group-hover:text-primary transition-colors truncate"
                                                >
                                                    {submission.challenge
                                                        ?.title}
                                                </h4>
                                                {#if submission.is_correct}
                                                    <span
                                                        class="shrink-0 inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-green-600 dark:text-green-400 bg-green-500/10 rounded border border-green-500/20 whitespace-nowrap"
                                                    >
                                                        +{submission.points_awarded ??
                                                            0} pts
                                                    </span>
                                                {:else}
                                                    <span
                                                        class="shrink-0 inline-flex items-center px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-red-600 dark:text-red-400 bg-red-500/10 rounded border border-red-500/20 whitespace-nowrap"
                                                    >
                                                        {submission.points_awarded ??
                                                            0} pts
                                                    </span>
                                                {/if}
                                            </div>
                                            <div
                                                class="flex items-center justify-between text-xs text-muted-foreground mt-1.5"
                                            >
                                                <span
                                                    class="font-medium flex items-center gap-1.5"
                                                >
                                                    <div
                                                        class="h-1.5 w-1.5 rounded-full bg-muted-foreground/50"
                                                    ></div>
                                                    {submission.challenge
                                                        ?.category?.name ||
                                                        'Umum'}
                                                </span>
                                                <div
                                                    class="flex items-center gap-2"
                                                >
                                                    <div
                                                        class="flex items-center gap-1.5"
                                                    >
                                                        <User class="h-3 w-3" />
                                                        <span
                                                            class="truncate max-w-[100px]"
                                                            title={submission
                                                                .user?.name ??
                                                                'Sistem'}
                                                            >{submission.user
                                                                ?.name ??
                                                                'Sistem'}</span
                                                        >
                                                    </div>
                                                    <div
                                                        class="h-1.5 w-1.5 rounded-full bg-muted-foreground/30"
                                                    ></div>
                                                    <div
                                                        class="flex items-center gap-1.5"
                                                    >
                                                        <Clock
                                                            class="h-3 w-3"
                                                        />
                                                        <span
                                                            >{formatTime(
                                                                submission.created_at,
                                                            )}</span
                                                        >
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                            <div
                                class="p-4 border-t border-border/40 bg-muted/5 text-center"
                            >
                                <Link
                                    href={challengesIndexRoute().url}
                                    class="text-xs text-muted-foreground font-semibold hover:text-primary transition-colors inline-flex items-center group"
                                >
                                    Lihat semua tantangan
                                    <ArrowRight
                                        class="h-3 w-3 ml-1 transform group-hover:translate-x-1 transition-transform"
                                    />
                                </Link>
                            </div>
                        {:else}
                            <div
                                class="flex flex-col items-center justify-center h-full p-8 text-center min-h-[300px]"
                            >
                                <div
                                    class="h-16 w-16 rounded-2xl bg-muted/50 border border-border/60 flex items-center justify-center mb-4 shadow-inner"
                                >
                                    <Zap
                                        class="h-8 w-8 text-muted-foreground/50"
                                    />
                                </div>
                                <p
                                    class="text-base font-bold text-foreground mb-1"
                                >
                                    Belum ada aktivitas
                                </p>
                                <p
                                    class="text-sm text-muted-foreground max-w-[200px]"
                                >
                                    Tim Anda belum menyelesaikan tantangan
                                    apapun.
                                </p>
                            </div>
                        {/if}
                    </CardContent>
                </Card>
            </div>
        </div>
    {/if}
</div>
