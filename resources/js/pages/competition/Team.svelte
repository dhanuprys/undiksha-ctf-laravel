<script module lang="ts">
    import { show } from '@/routes/team';
    export const layout = {
        breadcrumbs: [
            { title: 'Tim Saya', href: show() }
        ]
    };
</script>

<script lang="ts">
    import { useForm, Link } from '@inertiajs/svelte';
    import { Users, KeyRound, CheckCircle2, Shield, Trophy, Target, Clock, Activity, Star, Copy, ArrowRight } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { join } from '@/routes/team';
    import type { Team } from '@/types/ctf';

    let {
        team,
        maxTeamSize = 5,
    }: {
        team: Team | null;
        maxTeamSize: number;
    } = $props();

    const form = useForm({
        join_code: '',
    });

    function joinTeam() {
        form.post(join().url, {
            preserveScroll: true,
        });
    }

    let copied = $state(false);
    
    function copyJoinCode() {
        if (team?.join_code) {
            navigator.clipboard.writeText(team.join_code);
            copied = true;
            setTimeout(() => {
                copied = false;
            }, 2000);
        }
    }
</script>

<AppHead title="Tim Saya" />

<div class="mx-auto max-w-7xl w-full p-6">
    {#if team}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold tracking-tight bg-clip-text text-transparent bg-gradient-to-r from-foreground to-foreground/70 mb-2">
                Manajemen Tim
            </h1>
            <p class="text-muted-foreground text-lg">Kelola anggota dan pantau progres tim Anda.</p>
        </div>

        <div class="grid gap-8 lg:grid-cols-3">
            <!-- Team Overview -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Profile Card -->
                <Card class="relative overflow-hidden border-border/60 shadow-sm transition-all duration-300 hover:shadow-md bg-gradient-to-b from-muted/20 to-card">
                    <!-- Decorative background -->
                    <div class="absolute top-0 inset-x-0 h-24 bg-gradient-to-r from-primary/20 via-blue-500/20 to-purple-500/20"></div>
                    
                    <CardContent class="pt-12 pb-6 px-6 text-center relative z-10 flex flex-col items-center">
                        <div class="h-28 w-28 rounded-full bg-background border-4 border-background shadow-md flex items-center justify-center mb-4 relative z-10 overflow-hidden group">
                            <div class="absolute inset-0 bg-primary/10 transition-colors group-hover:bg-primary/20"></div>
                            <Shield class="h-12 w-12 text-primary group-hover:scale-110 transition-transform duration-300" />
                        </div>
                        
                        <h2 class="text-3xl font-black tracking-tight mb-2">{team.name}</h2>
                        
                        <div class="flex items-center gap-1.5 justify-center mb-6">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold text-primary border border-primary/20">
                                <Star class="h-3.5 w-3.5" />
                                Didirikan {new Date(team.created_at).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })}
                            </span>
                        </div>
                        
                        <div class="w-full grid grid-cols-2 gap-3 mt-2">
                            <div class="rounded-xl bg-card border border-border/60 p-4 shadow-sm relative overflow-hidden group hover:border-amber-500/30 transition-colors">
                                <div class="absolute -right-2 -top-2 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <Trophy class="h-12 w-12 text-amber-500" />
                                </div>
                                <p class="text-[10px] font-bold text-muted-foreground tracking-widest uppercase mb-1">Total Poin</p>
                                <p class="text-3xl font-black text-foreground">{team.total_score ?? 0}</p>
                            </div>
                            <div class="rounded-xl bg-card border border-border/60 p-4 shadow-sm relative overflow-hidden group hover:border-green-500/30 transition-colors">
                                <div class="absolute -right-2 -top-2 opacity-5 group-hover:opacity-10 transition-opacity">
                                    <Target class="h-12 w-12 text-green-500" />
                                </div>
                                <p class="text-[10px] font-bold text-muted-foreground tracking-widest uppercase mb-1">Tantangan</p>
                                <p class="text-3xl font-black text-foreground">{team.submissions ? team.submissions.filter(s => s.is_correct).length : 0}</p>
                            </div>
                        </div>
                    </CardContent>
                </Card>

                <!-- Join Code Card (if exists/applicable) -->
                {#if team.join_code}
                    <Card class="border-border/60 shadow-sm hover:shadow-md transition-all">
                        <CardHeader class="pb-3 border-b border-border/40 bg-muted/5">
                            <CardTitle class="flex items-center gap-2 text-lg">
                                <KeyRound class="h-5 w-5 text-primary" />
                                Kode Undangan
                            </CardTitle>
                        </CardHeader>
                        <CardContent class="pt-4">
                            <p class="text-sm text-muted-foreground mb-3">Bagikan kode ini untuk mengundang anggota baru (maks {maxTeamSize} anggota).</p>
                            <div class="flex items-center gap-2">
                                <code class="flex-1 text-center bg-muted/50 border border-border/60 rounded-lg py-2.5 font-mono text-lg font-bold tracking-widest">
                                    {team.join_code}
                                </code>
                                <Button variant="outline" size="icon" class="shrink-0 h-[46px] w-[46px]" onclick={copyJoinCode} aria-label="Copy join code">
                                    {#if copied}
                                        <CheckCircle2 class="h-4 w-4 text-green-500" />
                                    {:else}
                                        <Copy class="h-4 w-4" />
                                    {/if}
                                </Button>
                            </div>
                        </CardContent>
                    </Card>
                {/if}
            </div>

            <div class="lg:col-span-2 space-y-8">
                <!-- Team Members -->
                <Card class="border-border/60 shadow-sm">
                    <CardHeader class="pb-4 border-b border-border/40 bg-muted/5">
                        <div class="flex items-center justify-between">
                            <CardTitle class="flex items-center gap-2 text-xl">
                                <Users class="h-5 w-5 text-primary" />
                                Anggota Tim
                            </CardTitle>
                            <span class="inline-flex items-center justify-center rounded-full bg-primary/10 px-2.5 py-0.5 text-xs font-bold text-primary border border-primary/20">
                                {team.users ? team.users.length : 0} / {maxTeamSize}
                            </span>
                        </div>
                    </CardHeader>
                    <CardContent class="pt-6">
                        <div class="grid gap-4 sm:grid-cols-2">
                            {#each team.users || [] as member, i (member.id)}
                                <div class="flex items-center gap-4 rounded-xl border border-border/60 bg-card p-4 hover:bg-muted/10 transition-colors shadow-sm group">
                                    <div class="relative flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-primary/20 to-primary/5 border border-primary/20 font-bold text-primary shadow-sm text-lg">
                                        {member.name.charAt(0).toUpperCase()}
                                        {#if i === 0}
                                            <div class="absolute -bottom-1 -right-1 h-5 w-5 rounded-full bg-amber-500 border-2 border-card flex items-center justify-center text-white" title="Ketua Tim">
                                                <Star class="h-3 w-3 fill-current" />
                                            </div>
                                        {/if}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <p class="font-bold text-foreground truncate group-hover:text-primary transition-colors">{member.name}</p>
                                        <p class="text-xs font-medium text-muted-foreground truncate">{member.email || 'Anggota'}</p>
                                    </div>
                                </div>
                            {/each}
                            
                            <!-- Empty slots visualization -->
                            {#each Array(Math.max(0, maxTeamSize - (team.users?.length || 0))) as _, index (index)}
                                <div class="flex items-center gap-4 rounded-xl border border-dashed border-border/60 bg-muted/5 p-4 opacity-70">
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-muted border border-border font-bold text-muted-foreground text-lg">
                                        ?
                                    </div>
                                    <div class="flex flex-col">
                                        <p class="font-medium text-muted-foreground">Slot Tersedia</p>
                                        <p class="text-xs text-muted-foreground/70">Menunggu anggota</p>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </CardContent>
                </Card>

                <!-- Activity / Completed Challenges -->
                <Card class="border-border/60 shadow-sm overflow-hidden bg-card">
                    <CardHeader class="pb-4 border-b border-border/40 bg-muted/5">
                        <CardTitle class="flex items-center gap-2 text-xl">
                            <Activity class="h-5 w-5 text-primary" />
                            Riwayat Penyelesaian
                        </CardTitle>
                    </CardHeader>
                    <CardContent class="p-0">
                        {#if team.submissions && team.submissions.length > 0}
                            <div class="divide-y divide-border/40 relative">
                                <!-- Vertical timeline line -->
                                <div class="absolute left-[31px] top-6 bottom-6 w-px bg-border/60"></div>
                                
                                {#each team.submissions as submission (submission.id)}
                                    <div class="p-5 group hover:bg-muted/30 transition-colors flex gap-4 items-start relative z-10">
                                        <div class="mt-0.5 shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-green-500/10 border border-green-500/20 text-green-600 dark:text-green-500 shadow-sm bg-card">
                                            <CheckCircle2 class="h-4 w-4" />
                                        </div>
                                        <div class="flex-1 min-w-0 bg-background/50 p-2.5 -my-2.5 rounded-md transition-colors group-hover:bg-transparent">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-1 gap-2">
                                                <h4 class="font-bold text-sm leading-tight text-foreground group-hover:text-primary transition-colors">
                                                    {submission.challenge?.title}
                                                </h4>
                                                <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-green-600 dark:text-green-400 bg-green-500/10 rounded border border-green-500/20 whitespace-nowrap">
                                                    +{submission.points_awarded ?? 0} pts
                                                </span>
                                            </div>
                                            <div class="flex items-center text-xs text-muted-foreground mt-2">
                                                <div class="flex items-center gap-1.5">
                                                    <Clock class="h-3.5 w-3.5" />
                                                    <span>{new Date(submission.created_at).toLocaleString('id-ID', { dateStyle: 'medium', timeStyle: 'short' })}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        {:else}
                            <div class="flex flex-col items-center justify-center p-12 text-center">
                                <div class="h-16 w-16 rounded-full bg-muted/50 border border-border/60 flex items-center justify-center mb-4 shadow-inner">
                                    <CheckCircle2 class="h-8 w-8 text-muted-foreground/50" />
                                </div>
                                <p class="text-base font-bold text-foreground mb-1">Belum ada tantangan diselesaikan</p>
                                <p class="text-sm text-muted-foreground max-w-sm">Mulai kerjakan tantangan untuk mendapatkan poin dan meningkatkan peringkat tim Anda.</p>
                                <Button variant="outline" class="mt-6 gap-2 rounded-full" asChild>
                                    {#snippet children(props)}
                                        <Link href="/challenges" {...props}>
                                            Mulai Kerjakan Tantangan
                                            <ArrowRight class="h-4 w-4" />
                                        </Link>
                                    {/snippet}
                                </Button>
                            </div>
                        {/if}
                    </CardContent>
                </Card>
            </div>
        </div>
    {:else}
        <!-- No Team State -->
        <div class="mx-auto max-w-xl mt-10">
            <div class="relative overflow-hidden rounded-3xl border border-border/60 bg-gradient-to-br from-card to-muted/20 shadow-sm transition-all duration-500 hover:shadow-md">
                <!-- Decorative background elements -->
                <div class="absolute top-0 right-0 -mt-16 -mr-16 h-64 w-64 rounded-full bg-primary/5 blur-3xl"></div>
                <div class="absolute bottom-0 left-0 -mb-16 -ml-16 h-64 w-64 rounded-full bg-blue-500/5 blur-3xl"></div>
                
                <div class="px-6 py-12 sm:p-12 relative z-10">
                    <div class="text-center mb-10">
                        <div class="mx-auto h-20 w-20 bg-background shadow-sm border border-border/60 rounded-2xl flex items-center justify-center mb-6 rotate-3 transition-transform hover:rotate-6">
                            <KeyRound class="h-10 w-10 text-primary" />
                        </div>
                        <h2 class="text-3xl font-extrabold tracking-tight mb-3">Bergabung dengan Tim</h2>
                        <p class="text-muted-foreground max-w-md mx-auto leading-relaxed text-base">
                            Masukkan kode undangan dari ketua tim Anda untuk bergabung dan mulai berkompetisi bersama.
                        </p>
                    </div>

                    <form onsubmit={(e) => {
 e.preventDefault(); joinTeam(); 
}} class="space-y-6 bg-card border border-border/60 p-6 rounded-2xl shadow-sm">
                        <div class="space-y-3">
                            <Label for="join_code" class="text-sm font-bold uppercase tracking-wider text-muted-foreground">Kode Undangan</Label>
                            <Input
                                id="join_code"
                                type="text"
                                bind:value={form.join_code}
                                placeholder="Contoh: X8A9B2PZ"
                                class="text-center text-xl uppercase tracking-[0.25em] font-mono h-14 bg-muted/30 border-border/60 focus-visible:ring-primary/30"
                            />
                            <InputError message={form.errors.join_code} />
                        </div>
                        <Button type="submit" size="lg" class="w-full h-12 rounded-xl text-base shadow-sm font-semibold transition-all hover:-translate-y-0.5" disabled={form.processing || !form.join_code}>
                            {#if form.processing}<Spinner class="mr-2 h-5 w-5" />{/if}
                            Bergabung Sekarang
                        </Button>
                    </form>
                </div>
            </div>
        </div>
    {/if}
</div>
