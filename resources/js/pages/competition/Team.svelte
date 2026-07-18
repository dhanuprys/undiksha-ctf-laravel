<script module lang="ts">
    import { show } from '@/routes/team';
    export const layout = {
        breadcrumbs: [
            { title: 'Tim Saya', href: show() }
        ]
    };
</script>

<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import { Users, KeyRound, CheckCircle2 } from 'lucide-svelte';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { join } from '@/routes/team';
    import type { Team } from '@/types/ctf';

    let {
        team,
    }: {
        team: Team | null;
    } = $props();

    const form = useForm({
        join_code: '',
    });

    function joinTeam() {
        form.post(join().url, {
            preserveScroll: true,
        });
    }
</script>

<AppHead title="Tim Saya" />

<div class="mx-auto max-w-4xl p-6">
    <div class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Tim Saya</h1>
        <p class="text-muted-foreground">Kelola dan lihat informasi tim Anda untuk kompetisi saat ini.</p>
    </div>

    {#if team}
        <div class="grid gap-6 md:grid-cols-2">
            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <Users class="h-5 w-5 text-primary" />
                        Detail Tim
                    </CardTitle>
                </CardHeader>
                <CardContent>
                    <div class="space-y-4">
                        <div>
                            <Label class="text-muted-foreground">Nama Tim</Label>
                            <p class="text-xl font-bold">{team.name}</p>
                        </div>
                        <div>
                            <Label class="text-muted-foreground">Total Poin</Label>
                            <p class="text-2xl font-bold text-primary">{team.total_score} pts</p>
                        </div>
                        <div>
                            <Label class="text-muted-foreground">Anggota Tim ({team.users?.length || 0})</Label>
                            <ul class="mt-2 space-y-2">
                                {#each team.users || [] as member (member.id)}
                                    <li class="flex items-center gap-2 rounded-md bg-muted/50 px-3 py-2 text-sm">
                                        <div class="flex h-6 w-6 items-center justify-center rounded-full bg-primary/20 font-bold text-primary">
                                            {member.name.charAt(0).toUpperCase()}
                                        </div>
                                        <span class="font-medium">{member.name}</span>
                                    </li>
                                {/each}
                            </ul>
                        </div>
                    </div>
                </CardContent>
            </Card>

            <Card>
                <CardHeader>
                    <CardTitle class="flex items-center gap-2">
                        <CheckCircle2 class="h-5 w-5 text-green-500" />
                        Tantangan Selesai
                    </CardTitle>
                    <CardDescription>Riwayat penyelesaian tantangan oleh tim Anda.</CardDescription>
                </CardHeader>
                <CardContent>
                    {#if team.submissions && team.submissions.length > 0}
                        <ul class="space-y-3">
                            {#each team.submissions as submission (submission.id)}
                                {#if submission.is_correct}
                                    <li class="flex flex-col gap-1 rounded-md border p-3">
                                        <div class="flex items-center justify-between">
                                            <span class="font-semibold">{submission.challenge?.title}</span>
                                            <span class="font-bold text-primary">+{submission.points_awarded}</span>
                                        </div>
                                        <span class="text-xs text-muted-foreground">{new Date(submission.created_at).toLocaleString('id-ID')}</span>
                                    </li>
                                {/if}
                            {/each}
                        </ul>
                    {:else}
                        <div class="py-8 text-center text-sm text-muted-foreground">
                            Belum ada tantangan yang diselesaikan.
                        </div>
                    {/if}
                </CardContent>
            </Card>
        </div>
    {:else}
        <Card class="mx-auto max-w-md border-primary/20">
            <CardHeader class="text-center">
                <div class="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-primary/10">
                    <KeyRound class="h-6 w-6 text-primary" />
                </div>
                <CardTitle>Bergabung ke Tim</CardTitle>
                <CardDescription>Masukkan kode rahasia tim yang diberikan oleh admin untuk bergabung.</CardDescription>
            </CardHeader>
            <CardContent>
                <form onsubmit={(e) => {
 e.preventDefault(); joinTeam(); 
}} class="space-y-4">
                    <div class="space-y-2">
                        <Label for="join_code">Kode Tim</Label>
                        <Input
                            id="join_code"
                            type="text"
                            bind:value={form.join_code}
                            placeholder="Contoh: X8A9B2PZ"
                            class="text-center text-lg uppercase tracking-widest font-mono"
                        />
                        <InputError message={form.errors.join_code} />
                    </div>
                    <Button type="submit" class="w-full" disabled={form.processing || !form.join_code}>
                        {#if form.processing}<Spinner class="mr-2 h-4 w-4" />{/if}
                        Bergabung Sekarang
                    </Button>
                </form>
            </CardContent>
        </Card>
    {/if}
</div>
