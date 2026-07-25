<script module lang="ts">
    export const layout = {
        title: 'Buat akun',
        description: 'Masukkan detail Anda di bawah ini untuk membuat akun',
    };
</script>

<script lang="ts">
    import { useForm } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import PasswordInput from '@/components/PasswordInput.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Button } from '@/components/ui/button';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { login } from '@/routes';

    let { passwordRules }: { passwordRules: string } = $props();

    const form = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit = (e: Event) => {
        e.preventDefault();
        form.post('/register');
    };
</script>

<AppHead title="Daftar" />

<form onsubmit={submit} class="flex flex-col gap-6">
    <div class="grid gap-6">
        <div class="grid gap-2">
            <Label for="name">Nama</Label>
            <Input
                id="name"
                type="text"
                required
                autocomplete="name"
                name="name"
                placeholder="Nama lengkap"
                bind:value={form.name}
            />
            <InputError message={form.errors.name} />
        </div>

        <div class="grid gap-2">
            <Label for="email">Alamat Email</Label>
            <Input
                id="email"
                type="email"
                required
                autocomplete="email"
                name="email"
                placeholder="email@example.com"
                bind:value={form.email}
            />
            <InputError message={form.errors.email} />
        </div>

        <div class="grid gap-2">
            <Label for="password">Kata Sandi</Label>
            <PasswordInput
                id="password"
                required
                autocomplete="new-password"
                name="password"
                placeholder="Kata Sandi"
                passwordrules={passwordRules}
                bind:value={form.password}
            />
            <InputError message={form.errors.password} />
        </div>

        <div class="grid gap-2">
            <Label for="password_confirmation">Konfirmasi kata sandi</Label>
            <PasswordInput
                id="password_confirmation"
                required
                autocomplete="new-password"
                name="password_confirmation"
                placeholder="Konfirmasi kata sandi"
                passwordrules={passwordRules}
                bind:value={form.password_confirmation}
            />
            <InputError message={form.errors.password_confirmation} />
        </div>

        <Button
            type="submit"
            class="mt-2 w-full"
            disabled={form.processing}
            data-test="register-user-button"
        >
            {#if form.processing}<Spinner />{/if}
            Buat akun
        </Button>
    </div>

    <div class="text-center text-sm text-muted-foreground">
        Sudah punya akun?
        <TextLink href={login()} class="underline underline-offset-4">
            Masuk
        </TextLink>
    </div>
</form>
