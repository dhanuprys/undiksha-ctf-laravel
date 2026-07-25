<script module lang="ts">
    import { index } from '@/routes/leaderboard';
    export const layout = {
        breadcrumbs: [{ title: 'Leaderboard', href: index() }],
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Trophy, Lock, Clock, TrendingUp } from 'lucide-svelte';
    import { onMount, onDestroy } from 'svelte';
    import AppHead from '@/components/AppHead.svelte';
    import LeaderboardChart from '@/components/LeaderboardChart.svelte';
    import {
        Card,
        CardHeader,
        CardTitle,
        CardContent,
    } from '@/components/ui/card';
    import { formatTime } from '@/lib/formatDate';
    import type { LeaderboardEntry, LeaderboardGraphData } from '@/types/ctf';

    let {
        leaderboard = [],
        graphData = [],
        status,
    }: {
        leaderboard: LeaderboardEntry[];
        graphData: LeaderboardGraphData[];
        status: 'active' | 'no_event';
    } = $props();

    let interval: ReturnType<typeof setInterval>;

    onMount(() => {
        if (status === 'active') {
            interval = setInterval(() => {
                router.reload({ only: ['leaderboard', 'graphData'] });
            }, 30000);
        }
    });

    onDestroy(() => {
        if (interval) {
            clearInterval(interval);
        }
    });

    // Split leaderboard into podium (top 3) and rest
    let podiumEntries = $derived(leaderboard.slice(0, 3));
    let restEntries = $derived(leaderboard.slice(3));

    function getRankBg(rank: number | string) {
        if (rank === 1) {
            return 'bg-yellow-500/10 border-yellow-500/30';
        }

        if (rank === 2) {
            return 'bg-slate-300/10 border-slate-400/30';
        }

        if (rank === 3) {
            return 'bg-amber-700/10 border-amber-700/30';
        }

        return 'bg-muted/10 border-border/60';
    }

    function getRankLabel(rank: number | string) {
        if (rank === 1) {
            return '🥇';
        }

        if (rank === 2) {
            return '🥈';
        }

        if (rank === 3) {
            return '🥉';
        }

        return `#${rank}`;
    }
</script>

<AppHead title="Leaderboard" />

<div class="mx-auto max-w-7xl w-full p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Papan Peringkat</h1>
        <p class="text-muted-foreground">
            Peringkat tim berdasarkan total poin.
        </p>
    </div>

    {#if status === 'no_event'}
        <div
            class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center"
        >
            <div class="max-w-md">
                <Lock class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">
                    Kompetisi Belum Dimulai
                </h3>
                <p class="text-sm text-muted-foreground">
                    Papan peringkat akan tersedia saat kompetisi dimulai.
                </p>
            </div>
        </div>
    {:else}
        <!-- Podium Section (Top 3) -->
        {#if podiumEntries.length > 0}
            <div
                class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8"
                style="grid-template-areas: 'second first third';"
            >
                <!-- Render in visual order: 2nd, 1st, 3rd -->
                {#each [podiumEntries[1], podiumEntries[0], podiumEntries[2]] as entry, i (i)}
                    {#if entry}
                        {@const isFirst = i === 1}
                        <div
                            class="order-{i} sm:order-none"
                            style="grid-area: {['second', 'first', 'third'][
                                i
                            ]};"
                        >
                            <Card
                                class={`relative overflow-hidden border shadow-sm transition-all duration-300 hover:shadow-md ${getRankBg(entry.rank)} ${entry.is_current_team ? 'ring-2 ring-primary/50' : ''} ${isFirst ? 'sm:-mt-4' : 'sm:mt-4'}`}
                            >
                                <CardContent
                                    class="p-5 flex flex-col items-center text-center"
                                >
                                    <!-- Rank emoji -->
                                    <div
                                        class={`text-3xl mb-2 ${isFirst ? 'text-4xl' : ''}`}
                                    >
                                        {getRankLabel(entry.rank)}
                                    </div>

                                    <!-- Team initial -->
                                    <div
                                        class={`shrink-0 rounded-full flex items-center justify-center font-black text-primary border-2 border-primary/20 bg-primary/10 mb-3 ${isFirst ? 'h-16 w-16 text-2xl' : 'h-12 w-12 text-lg'}`}
                                    >
                                        {entry.team.name
                                            .charAt(0)
                                            .toUpperCase()}
                                    </div>

                                    <!-- Team name -->
                                    <h3
                                        class={`font-bold tracking-tight truncate max-w-full ${isFirst ? 'text-lg' : 'text-base'} ${entry.is_current_team ? 'text-primary' : 'text-foreground'}`}
                                    >
                                        {entry.team.name}
                                    </h3>
                                    {#if entry.is_current_team}
                                        <span
                                            class="mt-1 inline-flex items-center rounded-full bg-primary/20 px-2 py-0.5 text-[10px] uppercase font-bold text-primary tracking-wider border border-primary/30"
                                        >
                                            Tim Anda
                                        </span>
                                    {/if}

                                    <!-- Score -->
                                    <div
                                        class={`mt-3 font-black text-primary ${isFirst ? 'text-3xl' : 'text-2xl'}`}
                                    >
                                        {entry.total_score}
                                        <span
                                            class="text-xs font-semibold uppercase tracking-wider opacity-70"
                                            >pts</span
                                        >
                                    </div>

                                    <!-- Meta -->
                                    <div
                                        class="flex items-center gap-3 mt-3 text-xs text-muted-foreground"
                                    >
                                        <span class="flex items-center gap-1">
                                            <Trophy class="h-3 w-3" />
                                            {entry.solved_count} soal
                                        </span>
                                        {#if entry.last_solve_time}
                                            <span
                                                class="flex items-center gap-1"
                                            >
                                                <Clock class="h-3 w-3" />
                                                {formatTime(
                                                    entry.last_solve_time,
                                                )}
                                            </span>
                                        {/if}
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    {/if}
                {/each}
            </div>
        {/if}

        <!-- Score Graph -->
        {#if graphData.length > 0}
            <Card
                class="mb-8 overflow-hidden border-border/60 bg-card shadow-sm"
            >
                <CardHeader class="pb-2 border-b border-border/40 bg-muted/5">
                    <CardTitle class="flex items-center gap-2 text-lg">
                        <TrendingUp class="h-5 w-5 text-primary" />
                        Grafik Poin (Top 10)
                    </CardTitle>
                </CardHeader>
                <CardContent class="pt-4">
                    <LeaderboardChart {graphData} />
                </CardContent>
            </Card>
        {/if}

        <!-- Full Table (Ranks 4+) -->
        {#if restEntries.length > 0}
            <Card class="overflow-hidden border-border/60 bg-card shadow-sm">
                <CardHeader class="pb-3 border-b border-border/40 bg-muted/5">
                    <CardTitle class="text-lg">Peringkat Lainnya</CardTitle>
                </CardHeader>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead
                            class="bg-transparent border-b border-border/40 text-muted-foreground"
                        >
                            <tr>
                                <th
                                    class="px-6 py-3 font-semibold w-20 text-center"
                                    >#</th
                                >
                                <th class="px-6 py-3 font-semibold">Nama Tim</th
                                >
                                <th
                                    class="px-6 py-3 font-semibold text-center hidden sm:table-cell"
                                    >Diselesaikan</th
                                >
                                <th class="px-6 py-3 font-semibold text-right"
                                    >Poin</th
                                >
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/40">
                            {#each restEntries as entry (entry.team.id)}
                                <tr
                                    class={`transition-colors ${entry.is_current_team ? 'bg-primary/10 hover:bg-primary/20' : 'hover:bg-muted/20'}`}
                                >
                                    <td
                                        class="px-6 py-3.5 text-center font-bold text-muted-foreground"
                                    >
                                        {entry.rank}
                                    </td>
                                    <td class="px-6 py-3.5">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="shrink-0 h-8 w-8 rounded-full bg-muted/50 border border-border/60 flex items-center justify-center text-xs font-bold text-muted-foreground"
                                            >
                                                {entry.team.name
                                                    .charAt(0)
                                                    .toUpperCase()}
                                            </div>
                                            <div class="min-w-0">
                                                <span
                                                    class={`font-semibold truncate block ${entry.is_current_team ? 'text-primary' : ''}`}
                                                >
                                                    {entry.team.name}
                                                </span>
                                                {#if entry.is_current_team}
                                                    <span
                                                        class="text-[10px] uppercase font-bold text-primary tracking-wider"
                                                    >
                                                        Tim Anda
                                                    </span>
                                                {/if}
                                            </div>
                                        </div>
                                    </td>
                                    <td
                                        class="px-6 py-3.5 text-center font-medium hidden sm:table-cell"
                                    >
                                        {entry.solved_count}
                                    </td>
                                    <td
                                        class="px-6 py-3.5 text-right font-bold text-primary"
                                    >
                                        {entry.total_score}
                                    </td>
                                </tr>
                            {/each}
                        </tbody>
                    </table>
                </div>
            </Card>
        {/if}

        <!-- Empty state -->
        {#if leaderboard.length === 0}
            <div
                class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center"
            >
                <div class="max-w-md">
                    <Trophy
                        class="mx-auto mb-4 h-12 w-12 text-muted-foreground"
                    />
                    <h3 class="mb-2 text-lg font-semibold">
                        Belum Ada Peringkat
                    </h3>
                    <p class="text-sm text-muted-foreground">
                        Belum ada data skor tim saat ini. Selesaikan tantangan
                        untuk memulai.
                    </p>
                </div>
            </div>
        {/if}
    {/if}
</div>
