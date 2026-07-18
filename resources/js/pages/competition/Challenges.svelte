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
    }: {
        categories: Category[];
        status: 'active' | 'not_started' | 'no_team';
    } = $props();
</script>

<AppHead title="Tantangan" />

<div class="p-6">
    {#if status === 'not_started'}
        <div class="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-center">
            <div class="max-w-md">
                <Lock class="mx-auto mb-4 h-12 w-12 text-muted-foreground" />
                <h3 class="mb-2 text-lg font-semibold">Kompetisi Belum Dimulai</h3>
                <p class="text-sm text-muted-foreground">Tantangan akan muncul di sini setelah waktu kompetisi dimulai.</p>
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
                                <Link href={show({ challenge: challenge.id }).url} class="block transition-transform hover:-translate-y-1">
                                    <Card class={`h-full border-2 ${challenge.solved_by_team ? 'border-green-500/50 bg-green-50/50 dark:bg-green-950/20' : 'border-transparent hover:border-primary/20'}`}>
                                        <CardHeader class="pb-3">
                                            <div class="flex items-start justify-between">
                                                <DifficultyBadge difficulty={challenge.difficulty} />
                                                <div class="text-lg font-bold text-primary">
                                                    {challenge.base_score} pts
                                                </div>
                                            </div>
                                            <CardTitle class="mt-2 text-xl leading-tight">
                                                {challenge.title}
                                            </CardTitle>
                                        </CardHeader>
                                        
                                        <CardContent>
                                            <div class="flex items-center gap-2 text-sm text-muted-foreground">
                                                <span>{challenge.solve_count} tim telah menyelesaikan</span>
                                            </div>
                                        </CardContent>
                                        
                                        {#if challenge.solved_by_team}
                                            <CardFooter class="pt-0">
                                                <div class="flex w-full items-center justify-center gap-2 rounded-md bg-green-100 py-2 text-sm font-semibold text-green-700 dark:bg-green-900/50 dark:text-green-400">
                                                    <CheckCircle2 class="h-4 w-4" />
                                                    Diselesaikan
                                                </div>
                                            </CardFooter>
                                        {/if}
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
