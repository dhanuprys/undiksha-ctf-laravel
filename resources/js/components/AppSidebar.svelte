<script lang="ts">
    import { Link, page } from '@inertiajs/svelte';
    import Flag from 'lucide-svelte/icons/flag';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import Target from 'lucide-svelte/icons/target';
    import Trophy from 'lucide-svelte/icons/trophy';
    import Users from 'lucide-svelte/icons/users';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import { Button } from '@/components/ui/button';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        SidebarMenuItem,
        SidebarGroup,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import { dashboard } from '@/routes';
    import type { NavItem, Team } from '@/types';

    let {
        children,
    }: {
        children?: Snippet;
    } = $props();

    const mainNavItems: NavItem[] = [
        {
            title: 'Dashboard',
            href: dashboard(),
            icon: LayoutGrid,
        },
    ];

    const competitionNavItems: NavItem[] = [
        {
            title: 'Tantangan',
            href: '/challenges',
            icon: Flag,
        },
        {
            title: 'Leaderboard',
            href: '/leaderboard',
            icon: Trophy,
        },
    ];

    const teamNavItems: NavItem[] = [
        {
            title: 'Tim Saya',
            href: '/team',
            icon: Users,
        },
    ];

    const user = $derived(page.props.auth?.user);
    const team = $derived(user?.current_team as Team | undefined);
</script>

<Sidebar
    collapsible="icon"
    variant="inset"
    class="border-r border-sidebar-border/50"
>
    <SidebarHeader class="border-b border-sidebar-border/50 pb-4 mb-2">
        <SidebarMenu>
            <SidebarMenuItem>
                {#snippet children(props)}
                    <Link
                        {...props}
                        href={toUrl(dashboard())}
                        class={props.class +
                            ' hover:opacity-80 transition-opacity'}
                    >
                        <AppLogo />
                    </Link>
                {/snippet}
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent class="gap-y-4">
        <NavMain title="Utama" items={mainNavItems} />
        <NavMain title="Kompetisi" items={competitionNavItems} />
        <NavMain title="Personal" items={teamNavItems} />

        <!-- Mini Team Summary Widget -->
        <SidebarGroup
            class="mt-auto pt-6 hidden group-data-[collapsible=icon]:hidden md:block"
        >
            <div
                class="rounded-xl border border-border/50 bg-muted/30 p-4 shadow-sm relative overflow-hidden"
            >
                <div class="absolute -right-4 -top-4 text-primary/10">
                    <Target class="h-16 w-16" />
                </div>
                {#if team}
                    <h4 class="font-semibold text-sm mb-1 line-clamp-1">
                        {team.name}
                    </h4>
                    <div class="flex items-end gap-1.5 mt-2">
                        <span
                            class="text-xs text-muted-foreground pb-0.5 font-medium uppercase tracking-wider"
                        >
                            Tim Kompetisi
                        </span>
                    </div>
                {:else}
                    <h4 class="font-semibold text-sm mb-2 text-foreground">
                        Belum ada tim
                    </h4>
                    <p class="text-xs text-muted-foreground mb-3 line-clamp-2">
                        Bergabung dengan tim untuk mengikuti kompetisi.
                    </p>
                    <Button
                        asChild
                        variant="outline"
                        size="sm"
                        class="w-full text-xs h-8 bg-background border-primary/20 hover:bg-primary hover:text-primary-foreground transition-colors"
                    >
                        {#snippet children(props)}
                            <Link {...props} href="/team">
                                Buat / Gabung Tim
                            </Link>
                        {/snippet}
                    </Button>
                {/if}
            </div>
        </SidebarGroup>
    </SidebarContent>

    <SidebarFooter class="border-t border-sidebar-border/50 pt-2">
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
