<script module lang="ts">
    export const layout = {
        title: 'Masuk ke akun Anda',
        description:
            'Masukkan email dan kata sandi Anda di bawah ini untuk masuk',
    };
</script>

<script lang="ts">
    import { Form, page } from '@inertiajs/svelte';
    import type { Action } from 'svelte/action';
    import AppHead from '@/components/AppHead.svelte';
    import InputError from '@/components/InputError.svelte';
    import PasswordInput from '@/components/PasswordInput.svelte';
    import { Button } from '@/components/ui/button';
    import { Checkbox } from '@/components/ui/checkbox';
    import { Input } from '@/components/ui/input';
    import { Label } from '@/components/ui/label';
    import { Spinner } from '@/components/ui/spinner';
    import { store } from '@/routes/login';

    let {
        status = '',
        turnstileSiteKey,
    }: {
        status?: string;
        turnstileSiteKey: string;
    } = $props();

    let turnstileToken = $state('');
    let turnstileWidgetId = $state<string | undefined>(undefined);

    const turnstileAction: Action<HTMLDivElement> = (node) => {
        let checkInterval: ReturnType<typeof setInterval>;

        function renderTurnstile() {
            if (typeof window !== 'undefined' && (window as any).turnstile) {
                turnstileWidgetId = (window as any).turnstile.render(node, {
                    sitekey: turnstileSiteKey,
                    size: 'flexible',
                    language: 'id',
                    theme: 'light',
                    callback: (token: string) => {
                        turnstileToken = token;
                    },
                    'expired-callback': () => {
                        turnstileToken = '';
                    },
                    'error-callback': () => {
                        turnstileToken = '';
                    },
                });
            }
        }

        if (typeof window !== 'undefined' && (window as any).turnstile) {
            renderTurnstile();
        } else if (typeof window !== 'undefined') {
            checkInterval = setInterval(() => {
                if ((window as any).turnstile) {
                    clearInterval(checkInterval);
                    renderTurnstile();
                }
            }, 100);
        }

        return {
            destroy() {
                if (checkInterval) {
                    clearInterval(checkInterval);
                }

                if (
                    turnstileWidgetId !== undefined &&
                    typeof window !== 'undefined' &&
                    (window as any).turnstile
                ) {
                    (window as any).turnstile.remove(turnstileWidgetId);
                }
            },
        };
    };

    $effect(() => {
        if (Object.keys(page.props.errors || {}).length > 0) {
            if (
                typeof window !== 'undefined' &&
                (window as any).turnstile &&
                turnstileWidgetId !== undefined
            ) {
                (window as any).turnstile.reset(turnstileWidgetId);
                turnstileToken = '';
            }
        }
    });
</script>

<AppHead title="Masuk" />
<svelte:head>
    <script
        src="https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit"
        async
        defer
    ></script>
</svelte:head>

{#if status}
    <div class="mb-4 text-center text-sm font-medium text-green-600">
        {status}
    </div>
{/if}

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
                <div use:turnstileAction></div>
                <InputError message={errors['cf-turnstile-response']} />
            </div>

            <Button
                type="submit"
                class="mt-4 w-full"
                disabled={processing || !turnstileToken}
                data-test="login-button"
            >
                {#if processing}<Spinner />{/if}
                Masuk
            </Button>
        </div>
    {/snippet}
</Form>
