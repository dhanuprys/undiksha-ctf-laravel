<script lang="ts">
    import { Link } from '@inertiajs/svelte';
    import Flag from 'lucide-svelte/icons/flag';
    import LayoutGrid from 'lucide-svelte/icons/layout-grid';
    import Trophy from 'lucide-svelte/icons/trophy';
    import Users from 'lucide-svelte/icons/users';
    import type { Snippet } from 'svelte';
    import AppLogo from '@/components/AppLogo.svelte';
    import NavMain from '@/components/NavMain.svelte';
    import NavUser from '@/components/NavUser.svelte';
    import {
        Sidebar,
        SidebarContent,
        SidebarFooter,
        SidebarHeader,
        SidebarMenu,
        // SidebarMenuButton,
        SidebarMenuItem,
    } from '@/components/ui/sidebar';
    import { toUrl } from '@/lib/utils';
    import { dashboard } from '@/routes';
    import type { NavItem } from '@/types';

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
        {
            title: 'Tim Saya',
            href: '/team',
            icon: Users,
        },
    ];
</script>

<Sidebar collapsible="icon" variant="inset">
    <SidebarHeader>
        <SidebarMenu>
            <SidebarMenuItem>
                {#snippet children(props)}
                    <Link
                        {...props}
                        href={toUrl(dashboard())}
                        class={props.class}
                    >
                        <AppLogo />
                    </Link>
                {/snippet}
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarHeader>

    <SidebarContent>
        <NavMain items={mainNavItems} />
    </SidebarContent>

    <SidebarFooter>
        <NavUser />
    </SidebarFooter>
</Sidebar>
{@render children?.()}
