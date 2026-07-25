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

    function getRankStyle(rank: number | string) {
        if (rank === 1) {
            return 'border-yellow-500/50 bg-yellow-500/5';
        }

        if (rank === 2) {
            return 'border-slate-400/50 bg-slate-400/5';
        }

        if (rank === 3) {
            return 'border-amber-700/50 bg-amber-700/5';
        }

        return 'border-border/60 bg-muted/5';
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
        <!-- Top 3 Section -->
        {#if podiumEntries.length > 0}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                {#each podiumEntries as entry, i (i)}
                    {#if entry}
                        <div
                            class={`flex flex-col items-center p-6 rounded-2xl border transition-colors ${getRankStyle(entry.rank)} ${entry.is_current_team ? 'ring-2 ring-primary/50' : ''}`}
                        >
                            <div class="text-4xl mb-3">{getRankLabel(entry.rank)}</div>
                            <h3 class={`font-bold text-xl text-center truncate w-full mb-1 ${entry.is_current_team ? 'text-primary' : 'text-foreground'}`}>
                                {entry.team.name}
                            </h3>
                            
                            {#if entry.is_current_team}
                                <span class="mb-3 inline-flex items-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-semibold text-primary">
                                    Tim Anda
                                </span>
                            {:else}
                                <div class="h-6 mb-3"></div> <!-- Spacer to keep heights aligned -->
                            {/if}

                            <div class="text-4xl font-black text-foreground mt-2">
                                {entry.total_score} <span class="text-sm font-semibold text-muted-foreground uppercase tracking-wider">pts</span>
                            </div>

                            <div class="flex items-center justify-center gap-4 mt-5 text-sm text-muted-foreground w-full border-t border-border/50 pt-4">
                                <span class="flex items-center gap-1.5">
                                    <Trophy class="h-4 w-4" />
                                    {entry.solved_count} soal
                                </span>
                                {#if entry.last_solve_time}
                                    <span class="flex items-center gap-1.5">
                                        <Clock class="h-4 w-4" />
                                        {formatTime(entry.last_solve_time)}
                                    </span>
                                {/if}
                            </div>
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
