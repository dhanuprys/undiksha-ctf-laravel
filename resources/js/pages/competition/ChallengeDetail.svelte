<script module lang="ts">
    import { index } from '@/routes/challenges';
    export const layout = {
        breadcrumbs: [
            { title: 'Tantangan', href: index() },
            { title: 'Detail', href: '#' }
        ]
    };
</script>

<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import { CheckCircle2, Download, ArrowLeft } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import DifficultyBadge from '@/components/DifficultyBadge.svelte';
    import FlagSubmitForm from '@/components/FlagSubmitForm.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle, CardFooter } from '@/components/ui/card';
    import { index as challengesIndexRoute } from '@/routes/challenges';
    import type { Challenge } from '@/types/ctf';

    let {
        challenge,
    }: {
        challenge: Challenge;
    } = $props();
</script>

<AppHead title={challenge.title} />

<div class="mx-auto max-w-4xl p-6">
    <div class="mb-6">
        <Button variant="ghost" size="sm" asChild>
            {#snippet children(props)}
                <Link href={challengesIndexRoute().url} {...props} class="flex items-center gap-2">
                    <ArrowLeft class="h-4 w-4" />
                    Kembali ke Daftar Tantangan
                </Link>
            {/snippet}
        </Button>
    </div>

    <Card class={`border-2 ${challenge.solved_by_team ? 'border-green-500/50' : 'border-border'}`}>
        <CardHeader class="border-b bg-muted/20 pb-6">
            <div class="mb-4 flex items-center justify-between">
                <DifficultyBadge difficulty={challenge.difficulty} />
                <div class="text-2xl font-bold text-primary">
                    {challenge.base_score} pts
                </div>
            </div>
            
            <CardTitle class="text-3xl font-bold tracking-tight">
                {challenge.title}
            </CardTitle>
            
            <div class="mt-2 flex flex-wrap gap-4 text-sm text-muted-foreground">
                <span class="inline-flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-primary/50"></div>
                    Kategori: {challenge.category?.name}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <div class="h-1.5 w-1.5 rounded-full bg-primary/50"></div>
                    Diselesaikan oleh {challenge.solve_count} tim
                </span>
            </div>
        </CardHeader>
        
        <CardContent class="pt-6">
            <div class="prose prose-sm dark:prose-invert max-w-none">
                <!-- eslint-disable-next-line svelte/no-at-html-tags -->
                {@html challenge.description}
            </div>
            
            {#if challenge.attachments && challenge.attachments.length > 0}
                <div class="mt-8 rounded-lg border bg-muted/30 p-4">
                    <h3 class="mb-3 text-sm font-semibold text-muted-foreground uppercase tracking-wider">File Lampiran</h3>
                    <ul class="flex flex-col gap-2">
                        {#each challenge.attachments as attachment (attachment.id)}
                            <li>
                                <a 
                                    href={attachment.download_url} 
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="group flex items-center gap-3 rounded-md bg-background px-4 py-3 text-sm shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground border"
                                >
                                    <div class="rounded bg-primary/10 p-1.5 text-primary group-hover:bg-primary group-hover:text-primary-foreground transition-colors">
                                        <Download class="h-4 w-4" />
                                    </div>
                                    <span class="font-medium flex-1 truncate">{attachment.file_name}</span>
                                </a>
                            </li>
                        {/each}
                    </ul>
                </div>
            {/if}
        </CardContent>
        
        <CardFooter class="flex-col items-stretch border-t bg-muted/10 px-6 py-6 sm:px-10">
            {#if challenge.solved_by_team}
                <div class="flex items-center justify-center gap-3 rounded-lg bg-green-100 p-4 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800/50">
                    <CheckCircle2 class="h-6 w-6" />
                    <div>
                        <p class="font-bold">Tim Anda telah menyelesaikan tantangan ini!</p>
                        <p class="text-sm opacity-90">Anda mendapatkan {challenge.base_score} poin.</p>
                    </div>
                </div>
            {:else}
                <div class="mx-auto w-full max-w-lg">
                    <FlagSubmitForm challengeId={challenge.id} />
                </div>
            {/if}
        </CardFooter>
    </Card>
</div>
