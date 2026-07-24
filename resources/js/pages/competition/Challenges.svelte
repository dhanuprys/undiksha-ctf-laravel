<script module lang="ts">
    import { index } from '@/routes/challenges';
    export const layout = {
        breadcrumbs: [
            { title: 'Tantangan', href: index() }
        ]
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { CheckCircle2, Lock } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import DifficultyBadge from '@/components/DifficultyBadge.svelte';
    import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
    import { show } from '@/routes/challenges';
    import type { Category } from '@/types/ctf';

    let {
        categories = [],
        status,
        start_time,
    }: {
        categories: Category[];
        status: 'active' | 'not_started' | 'no_team' | 'ended';
        start_time?: string | null;
    } = $props();

    let timeRemaining = $state('');

    $effect(() => {
        if (status === 'not_started' && start_time) {
            const target = new Date(start_time).getTime();
            
            const updateTimer = () => {
                const now = new Date().getTime();
                const diff = target - now;
                
                if (diff <= 0) {
                    timeRemaining = '00:00:00';
                    window.location.reload();

                    return;
                }
                
                const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const s = Math.floor((diff % (1000 * 60)) / 1000);
                
                timeRemaining = d > 0 
                    ? `${d} hari ${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`
                    : `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
            };
            
            updateTimer();
            const interval = setInterval(updateTimer, 1000);
            
            return () => clearInterval(interval);
        }
    });
</script>

<AppHead title="Tantangan" />

<div class="mx-auto max-w-7xl w-full p-6">
    {#if status === 'not_started'}
        <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center">
            <div class="max-w-md">
                <Lock class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">Kompetisi Belum Dimulai</h3>
                <p class="text-sm text-muted-foreground">Tantangan akan muncul di sini setelah waktu kompetisi dimulai.</p>
                {#if timeRemaining}
                    <div class="mt-6 inline-flex flex-col items-center justify-center gap-1 rounded-lg bg-primary/10 px-6 py-3 text-primary border border-primary/20 shadow-sm">
                        <span class="text-xs font-semibold uppercase tracking-widest opacity-80">Waktu Tersisa</span>
                        <span class="font-mono text-3xl font-bold tracking-[0.15em]">{timeRemaining}</span>
                    </div>
                {/if}
            </div>
        </div>
    {:else if status === 'ended'}
        <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center">
            <div class="max-w-md">
                <Lock class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">Kompetisi Telah Berakhir</h3>
                <p class="text-sm text-muted-foreground">Waktu kompetisi sudah habis. Terima kasih atas partisipasi Anda!</p>
            </div>
        </div>
    {:else if status === 'no_team'}
        <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center">
            <div class="max-w-md">
                <h3 class="mb-2 text-lg font-semibold">Anda Belum Bergabung dalam Tim</h3>
                <p class="text-sm text-muted-foreground">Silakan bergabung dengan tim terlebih dahulu untuk melihat tantangan.</p>
            </div>
        </div>
    {:else}
        <div class="flex flex-col gap-10">
            {#each categories as category (category.id)}
                {#if category.challenges && category.challenges.length > 0}
                    <section>
                        <div class="mb-6">
                            <h2 class="text-2xl font-bold tracking-tight">{category.name}</h2>
                            {#if category.description}
                                <p class="text-muted-foreground">{category.description}</p>
                            {/if}
                        </div>
                        
                        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            {#each category.challenges as challenge (challenge.id)}
                                <Link href={show({ challenge: challenge.id }).url} class="block h-full transition-all duration-300 hover:-translate-y-1">
                                    <Card class={`flex flex-col h-full border bg-card transition-shadow hover:shadow-md ${challenge.solved_by_team ? 'border-green-500/40 dark:border-green-500/30' : 'border-border/60 hover:border-border'}`}>
                                        <CardHeader class="pb-3">
                                            <div class="flex items-start justify-between">
                                                <DifficultyBadge difficulty={challenge.difficulty} />
                                                <div class="text-lg font-bold text-primary whitespace-nowrap ml-2">
                                                    {challenge.base_score} pts
                                                </div>
                                            </div>
                                            <CardTitle class="mt-2 text-xl leading-tight line-clamp-2 min-h-14">
                                                {challenge.title}
                                            </CardTitle>
                                        </CardHeader>
                                        
                                        <CardContent>
                                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                {#if challenge.solve_count !== null}
                                                    <span>{challenge.solve_count} tim telah menyelesaikan</span>
                                                {/if}
                                            </div>
                                        </CardContent>
                                        
                                        <CardFooter class="pt-0 mt-auto">
                                            {#if challenge.solved_by_team}
                                                <div class="flex w-full items-center justify-center gap-2 rounded-md bg-muted/40 py-2 text-sm font-medium text-green-600 dark:text-green-500 border border-green-500/20">
                                                    <CheckCircle2 class="h-4 w-4" />
                                                    Diselesaikan
                                                </div>
                                            {:else}
                                                <div class="flex w-full items-center justify-center gap-2 rounded-md bg-muted/10 py-2 text-sm font-medium text-muted-foreground/50 border border-dashed border-border/50">
                                                    Belum diselesaikan
                                                </div>
                                            {/if}
                                        </CardFooter>
                                    </Card>
                                </Link>
                            {/each}
                        </div>
                    </section>
                {/if}
            {/each}
            
            {#if categories.length === 0}
                <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center">
                    <p class="text-muted-foreground">Belum ada tantangan yang tersedia.</p>
                </div>
            {/if}
        </div>
    {/if}
</div>
