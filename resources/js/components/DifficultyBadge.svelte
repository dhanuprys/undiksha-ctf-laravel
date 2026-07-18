<script lang="ts">
    import { Badge } from '@/components/ui/badge';

    let { difficulty }: { difficulty: 'easy' | 'medium' | 'hard' | 'very_hard' } = $props();

    let variant: 'default' | 'secondary' | 'destructive' | 'outline' = $derived.by(() => {
        switch (difficulty) {
            case 'easy':
                return 'outline';
            case 'medium':
                return 'secondary';
            case 'hard':
                return 'destructive';
            case 'very_hard':
                return 'destructive';
            default:
                return 'secondary';
        }
    });

    let label = $derived.by(() => {
        switch (difficulty) {
            case 'easy': return 'Easy';
            case 'medium': return 'Medium';
            case 'hard': return 'Hard';
            case 'very_hard': return 'Very Hard';
            default: return 'Unknown';
        }
    });
</script>

<Badge {variant} class={difficulty === 'very_hard' ? 'bg-red-900 hover:bg-red-900/80 text-white border-transparent' : ''}>
    {label}
</Badge>
