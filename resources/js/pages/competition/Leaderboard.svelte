<script module lang="ts">
    import { index } from '@/routes/leaderboard';
    export const layout = {
        breadcrumbs: [
            { title: 'Leaderboard', href: index() }
        ]
    };
</script>

<script lang="ts">
    import { router } from '@inertiajs/svelte';
    import { Trophy, Medal, Lock } from 'lucide-svelte';
    import { onMount, onDestroy } from 'svelte';
    import AppHead from '@/components/AppHead.svelte';
    import LeaderboardChart from '@/components/LeaderboardChart.svelte';
    import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card';
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
                router.reload({ only: ['leaderboard'] });
            }, 30000); // Poll every 30 seconds
        }
    });

    onDestroy(() => {
        if (interval) {
clearInterval(interval);
}
    });

    function getRankColor(rank: number | string) {
        if (rank === 1) {
return 'text-yellow-500';
} // Gold

        if (rank === 2) {
return 'text-gray-400';
}   // Silver

        if (rank === 3) {
return 'text-amber-700';
}  // Bronze

        return 'text-muted-foreground';
    }
</script>

<AppHead title="Leaderboard" />

<div class="mx-auto max-w-7xl w-full p-6">
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold tracking-tight">Papan Peringkat</h1>
            <p class="text-muted-foreground">Peringkat tim berdasarkan total poin.</p>
        </div>
        <Trophy class="h-10 w-10 text-primary opacity-50" />
    </div>

    {#if status === 'no_event'}
        <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center">
            <div class="max-w-md">
                <Lock class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">Kompetisi Belum Dimulai</h3>
                <p class="text-sm text-muted-foreground">Papan peringkat akan tersedia saat kompetisi dimulai.</p>
            </div>
        </div>
    {:else}
        {#if graphData.length > 0}
            <Card class="mb-8 overflow-hidden border-border bg-card">
                <CardHeader class="pb-2">
                    <CardTitle class="text-xl">Grafik Poin (Top 10)</CardTitle>
                </CardHeader>
                <CardContent>
                    <LeaderboardChart {graphData} />
                </CardContent>
            </Card>
        {/if}

        <Card class="overflow-hidden border-border/60 bg-card shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-transparent border-b border-border/40 text-muted-foreground">
                        <tr>
                            <th class="px-6 py-4 font-semibold w-24 text-center">Peringkat</th>
                            <th class="px-6 py-4 font-semibold">Nama Tim</th>
                            <th class="px-6 py-4 font-semibold text-center">Tantangan Diselesaikan</th>
                            <th class="px-6 py-4 font-semibold text-right">Total Poin</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-border">
                        {#each leaderboard as entry (entry.team.id)}
                            <tr class={`transition-colors hover:bg-muted/20 ${entry.is_current_team ? 'bg-muted/10' : ''}`}>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {#if Number(entry.rank) <= 3}
                                            <Medal class={`h-5 w-5 ${getRankColor(entry.rank)}`} />
                                        {:else}
                                            <span class="font-bold text-muted-foreground">#{entry.rank}</span>
                                        {/if}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class={`font-semibold ${entry.is_current_team ? 'text-primary' : ''}`}>
                                        {entry.team.name}
                                    </span>
                                    {#if entry.is_current_team}
                                        <span class="ml-2 inline-flex items-center rounded-full bg-muted px-2 py-0.5 text-[10px] uppercase font-bold text-muted-foreground tracking-wider">
                                            Tim Anda
                                        </span>
                                    {/if}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    {entry.solved_count}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-primary">
                                    {entry.total_score} pts
                                </td>
                            </tr>
                        {/each}
                        
                        {#if leaderboard.length === 0}
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-muted-foreground">
                                    Belum ada data skor tim saat ini.
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>
        </Card>
    {/if}
</div>
