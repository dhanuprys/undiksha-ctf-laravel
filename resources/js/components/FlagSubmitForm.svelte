<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { store as submitFlagRoute } from '@/routes/submissions';

    let { challengeId, disabled = false }: { challengeId: number; disabled?: boolean } = $props();

    const form = useForm({
        challenge_id: challengeId,
        flag: '',
    });

    function submitFlag() {
        form.post(submitFlagRoute().url, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('flag');
            },
        });
    }
</script>

<form onsubmit={(e) => {
 e.preventDefault(); submitFlag(); 
}} class="flex flex-col gap-4 mt-4">
    <div class="grid gap-2">
        <Label for={`flag-${challengeId}`}>Kirim Flag</Label>
        <div class="flex flex-col sm:flex-row gap-2">
            <Input
                id={`flag-${challengeId}`}
                type="text"
                placeholder={'ctfundiksha{...}'}
                bind:value={form.flag}
                {disabled}
                class="flex-1 font-mono"
            />
            <Button type="submit" disabled={form.processing || disabled || !form.flag}>
                {#if form.processing}<Spinner class="mr-2 h-4 w-4" />{/if}
                Submit
            </Button>
        </div>
        {#if form.errors.flag}
            <InputError message={form.errors.flag} />
        {/if}
    </div>
</form>
