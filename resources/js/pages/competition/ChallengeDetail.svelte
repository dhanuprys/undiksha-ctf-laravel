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
    import { CheckCircle2, Download, ArrowLeft, Terminal, Users, FolderOpen } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import DifficultyBadge from '@/components/DifficultyBadge.svelte';
    import FlagSubmitForm from '@/components/FlagSubmitForm.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { index as challengesIndexRoute } from '@/routes/challenges';
    import type { Challenge } from '@/types/ctf';

    let {
        challenge,
    }: {
        challenge: Challenge;
    } = $props();
</script>

<AppHead title={challenge.title} />

<div class="mx-auto max-w-7xl w-full p-6 space-y-8">
    <!-- Back Button -->
    <div class="flex items-center justify-between">
        <Button variant="ghost" size="sm" class="group" asChild>
            {#snippet children(props)}
                <Link href={challengesIndexRoute().url} {...props} class="flex items-center gap-2 text-muted-foreground hover:text-foreground transition-colors">
                    <ArrowLeft class="h-4 w-4 transition-transform group-hover:-translate-x-1" />
                    Kembali ke Daftar Tantangan
                </Link>
            {/snippet}
        </Button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content (Left Column) -->
        <div class="lg:col-span-2 space-y-8">
            <Card class={`relative overflow-hidden border-border/60 shadow-sm transition-all duration-500 ${challenge.solved_by_team ? 'ring-1 ring-green-500/50 shadow-green-500/10' : 'hover:shadow-md'}`}>
                
                <!-- Decorative Top Gradient -->
                <div class={`absolute top-0 left-0 right-0 h-1.5 w-full ${challenge.solved_by_team ? 'bg-gradient-to-r from-green-400 to-emerald-600' : 'bg-gradient-to-r from-primary/60 to-primary/40'}`}></div>
                
                <CardHeader class="border-b border-border/40 pb-8 pt-10 bg-muted/10 relative">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-4">
                        <div class="space-y-4">
                            <DifficultyBadge difficulty={challenge.difficulty} />
                            <CardTitle class="text-3xl sm:text-4xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-br from-foreground to-foreground/70">
                                {challenge.title}
                            </CardTitle>
                        </div>
                        <div class="flex shrink-0 items-center justify-center h-20 w-20 sm:h-24 sm:w-24 rounded-2xl bg-primary/10 border border-primary/20 shadow-inner">
                            <div class="flex flex-col items-center justify-center text-primary">
                                <span class="text-3xl sm:text-4xl font-black leading-none">{challenge.base_score}</span>
                                <span class="text-xs font-semibold uppercase tracking-widest mt-1 opacity-80">pts</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 text-sm font-medium mt-6">
                        <div class="flex items-center gap-2 bg-background px-3.5 py-1.5 rounded-full border border-border/60 shadow-sm text-muted-foreground">
                            <FolderOpen class="h-4 w-4 text-primary/80" />
                            <span>Kategori: <span class="text-foreground">{challenge.category?.name}</span></span>
                        </div>
                        {#if challenge.solve_count !== null}
                            <div class="flex items-center gap-2 bg-background px-3.5 py-1.5 rounded-full border border-border/60 shadow-sm text-muted-foreground">
                                <Users class="h-4 w-4 text-blue-500/80" />
                                <span>Diselesaikan <span class="text-foreground">{challenge.solve_count}</span> tim</span>
                            </div>
                        {/if}
                    </div>
                </CardHeader>
                
                <CardContent class="p-8">
                    <div class="flex items-center gap-2 mb-6 text-xl font-bold tracking-tight text-foreground">
                        <Terminal class="h-5 w-5 text-muted-foreground" />
                        <h3>Deskripsi Tantangan</h3>
                    </div>
                    <div class="prose prose-sm sm:prose-base dark:prose-invert max-w-none text-muted-foreground leading-relaxed prose-headings:text-foreground prose-a:text-primary hover:prose-a:text-primary/80 prose-code:text-primary prose-code:bg-primary/10 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded-md prose-code:before:content-none prose-code:after:content-none">
                        <!-- eslint-disable-next-line svelte/no-at-html-tags -->
                        {@html challenge.description}
                    </div>

                    {#if challenge.attachments && challenge.attachments.length > 0}
                        <div class="mt-8 pt-8 border-t border-border/40">
                            <div class="flex items-center gap-2 mb-4 text-base font-bold tracking-tight text-foreground">
                                <Download class="h-4.5 w-4.5 text-muted-foreground" />
                                <h4>File Lampiran</h4>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                {#each challenge.attachments as attachment (attachment.id)}
                                    <a 
                                        href={attachment.download_url} 
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group relative flex items-center gap-4 rounded-xl border border-border/60 bg-card p-4 transition-all duration-300 hover:border-primary/40 hover:bg-primary/5 hover:shadow-sm"
                                    >
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-muted border border-border/50 text-muted-foreground group-hover:text-primary group-hover:border-primary/30 group-hover:bg-background transition-colors shadow-sm">
                                            <Download class="h-5 w-5 group-hover:-translate-y-0.5 transition-transform" />
                                        </div>
                                        <div class="flex flex-col min-w-0">
                                            <span class="font-semibold text-sm truncate text-foreground group-hover:text-primary transition-colors">{attachment.file_name}</span>
                                            <span class="text-xs text-muted-foreground font-medium mt-0.5">Unduh file</span>
                                        </div>
                                    </a>
                                {/each}
                            </div>
                        </div>
                    {/if}
                </CardContent>
            </Card>
        </div>

        <!-- Submission Sidebar (Right Column) -->
        <div class="lg:col-span-1">
            <div class="sticky top-6">
                <Card class={`border-border/60 shadow-sm overflow-hidden transition-all duration-500 ${challenge.solved_by_team ? 'bg-gradient-to-b from-green-500/10 to-emerald-500/5 border-green-500/30' : 'bg-card hover:shadow-md'}`}>
                    <CardHeader class={`pb-4 ${challenge.solved_by_team ? '' : 'border-b border-border/40 bg-muted/5'}`}>
                        <CardTitle class="text-lg flex items-center gap-2">
                            {#if challenge.solved_by_team}
                                <CheckCircle2 class="h-5 w-5 text-green-600 dark:text-green-500" />
                                Status: Selesai
                            {:else}
                                <Terminal class="h-5 w-5 text-primary" />
                                Submit Flag
                            {/if}
                        </CardTitle>
                    </CardHeader>
                    <CardContent class={`p-6 ${challenge.solved_by_team ? 'pt-2' : ''}`}>
                        {#if challenge.solved_by_team}
                            <div class="flex flex-col items-center text-center space-y-5 py-4">
                                <div class="relative flex h-24 w-24 items-center justify-center rounded-full bg-green-500/10 border-2 border-green-500/30 shadow-inner">
                                    <div class="absolute inset-0 rounded-full animate-ping bg-green-500/20 opacity-75"></div>
                                    <CheckCircle2 class="h-12 w-12 text-green-600 dark:text-green-500 relative z-10" />
                                </div>
                                <div class="space-y-1.5">
                                    <h3 class="font-black text-2xl tracking-tight text-green-600 dark:text-green-400">Tepat Sekali!</h3>
                                    <p class="text-sm text-muted-foreground">
                                        Tim Anda telah menyelesaikan tantangan ini dan mendapatkan <span class="font-bold text-foreground">{challenge.points_awarded ?? challenge.base_score} poin</span>.
                                    </p>
                                </div>
                            </div>
                        {:else}
                            <div class="space-y-6">
                                <div class="rounded-lg bg-muted/40 p-4 border border-border/50 text-sm text-muted-foreground space-y-2">
                                    <p>
                                        Masukkan flag yang Anda temukan untuk mendapatkan poin.
                                    </p>
                                    <p class="text-xs">
                                        Format: <code class="px-1.5 py-0.5 rounded bg-background border border-border/60 font-mono text-foreground font-semibold">CTF&#123;...&#125;</code>
                                    </p>
                                </div>
                                <FlagSubmitForm challengeId={challenge.id} />
                            </div>
                        {/if}
                    </CardContent>
                </Card>
            </div>
        </div>
    </div>
</div>
