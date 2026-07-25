<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import { Flag, Send } from 'lucide-svelte';
    import InputError from '@/components/InputError.svelte';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { store as submitFlagRoute } from '@/routes/submissions';

    let props: { challengeId: number; disabled?: boolean } = $props();
    
    let confirmed = $state(false);

    // svelte-ignore state_referenced_locally
    const form = useForm({
        challenge_id: props.challengeId,
        flag: '',
    });

    function submitFlag() {
        form.clearErrors();
        form.post(submitFlagRoute().url, {
            preserveScroll: true,
            onSuccess: () => {
                form.reset('flag');
            },
            onFinish: () => {
                confirmed = false;
            }
        });
    }
</script>

<form
    onsubmit={(e) => {
        e.preventDefault();
        submitFlag();
    }}
    class="flex flex-col gap-4 mt-6"
>
    <div class="grid gap-3">
        <Label for={`flag-${props.challengeId}`} class="text-sm font-semibold text-foreground/80">
            Kirim Flag
        </Label>
        
        <div class="relative group">
            <Flag 
                class="absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-muted-foreground group-focus-within:text-primary transition-colors" 
            />
            <Input
                id={`flag-${props.challengeId}`}
                type="text"
                placeholder={'CTF{...}'}
                bind:value={form.flag}
                disabled={props.disabled || form.processing}
                class="pl-10 h-11 font-mono text-sm bg-background/50 border-border/80 focus-visible:ring-primary focus-visible:border-primary transition-all shadow-sm w-full"
                oninput={() => form.clearErrors('flag')}
            />
        </div>

        <div class="flex items-start space-x-3 mt-1 p-3.5 rounded-lg border border-border/70 bg-muted/30 hover:bg-muted/50 transition-all shadow-sm hover:shadow-md">
            <Checkbox 
                id={`confirm-submit-${props.challengeId}`} 
                bind:checked={confirmed} 
                disabled={props.disabled || form.processing || !form.flag}
                class="mt-0.5"
            />
            <label
                for={`confirm-submit-${props.challengeId}`}
                class="grid gap-1.5 leading-none cursor-pointer select-none flex-1 peer-disabled:cursor-not-allowed peer-disabled:opacity-70"
            >
                <span class="text-sm font-medium leading-tight">
                    Konfirmasi Pengiriman
                </span>
                <span class="text-xs text-muted-foreground">
                    Saya yakin flag ini benar. (Penalti akan dikenakan jika salah)
                </span>
            </label>
        </div>

        <Button
            type="submit"
            disabled={form.processing || props.disabled || !form.flag || !confirmed}
            class="h-11 w-full sm:w-auto transition-all shadow-sm gap-2 mt-2"
        >
            {#if form.processing}
                <Spinner class="h-4 w-4" />
            {:else}
                <Send class="h-4 w-4" />
            {/if}
            Submit Flag
        </Button>

        {#if form.errors.flag}
            <div class="mt-1">
                <InputError message={form.errors.flag} />
            </div>
        {/if}
    </div>
</form>
