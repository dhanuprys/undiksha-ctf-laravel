<script module lang="ts">
    export const layout = {
        title: 'Masuk ke akun Anda',
        description:
            'Masukkan email dan kata sandi Anda di bawah ini untuk masuk',
    };
</script>

<script lang="ts">
    import { Form, page } from '@inertiajs/svelte';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import PasskeyVerify from '@/components/PasskeyVerify.svelte';
    import PasswordInput from '@/components/PasswordInput.svelte';
    import TextLink from '@/components/TextLink.svelte';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { store } from '@/routes/login';
    import { request } from '@/routes/password';

    let {
        status = '',
        canResetPassword,
        turnstileSiteKey,
    }: {
        status?: string;
        canResetPassword: boolean;
        turnstileSiteKey: string;
    } = $props();

    $effect(() => {
        if (Object.keys(page.props.errors || {}).length > 0) {
            if (typeof window !== 'undefined' && (window as any).turnstile) {
                (window as any).turnstile.reset();
            }
        }
    });
</script>

<AppHead title="Masuk" />
<svelte:head>
    <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
</svelte:head>

{#if status}
    <div class="mb-4 text-center text-sm font-medium text-green-600">
        {status}
    </div>
{/if}

<PasskeyVerify />

<Form
    {...store.form()}
    resetOnSuccess={['password']}
    resetOnError={['password']}
    class="flex flex-col gap-6"
>
    {#snippet children({ errors, processing })}
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">Alamat Email</Label>
                <Input
                    id="email"
                    type="email"
                    name="email"
                    required
                    autocomplete="email"
                    placeholder="email@example.com"
                />
                <InputError message={errors.email} />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">Kata Sandi</Label>
                    {#if canResetPassword}
                        <TextLink href={request()} class="text-sm">
                            Lupa kata sandi Anda?
                        </TextLink>
                    {/if}
                </div>
                <PasswordInput
                    id="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Kata Sandi"
                />
                <InputError message={errors.password} />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" name="remember" />
                    <span>Ingat saya</span>
                </Label>
            </div>

            <div class="grid gap-2">
                <div class="cf-turnstile" data-language="id" data-theme="light" data-size="flexible" data-sitekey={turnstileSiteKey}></div>
                <InputError message={errors['cf-turnstile-response']} />
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                disabled={processing}
                data-test="login-button"
            >
                {#if processing}<Spinner />{/if}
                Masuk
            </Button>
        </div>
    {/snippet}
</Form>
