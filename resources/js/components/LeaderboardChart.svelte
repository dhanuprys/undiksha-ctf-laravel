<script lang="ts">
    import Chart from 'chart.js/auto';
    import 'chartjs-adapter-date-fns';
    import { id } from 'date-fns/locale';
    import { onMount, onDestroy } from 'svelte';
    import type { LeaderboardGraphData } from '@/types/ctf';

    let { graphData }: { graphData: LeaderboardGraphData[] } = $props();

    let canvas: HTMLCanvasElement;
    let chart: Chart | null = null;

    $effect(() => {
        if (chart && graphData) {
            const rawData = $state.snapshot(graphData);
            chart.data.datasets = rawData.map((team) => ({
                label: team.team_name,
                data: team.data as any[],
                borderColor: team.color,
                backgroundColor: team.color,
                borderWidth: 3,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHitRadius: 10,
                stepped: true,
                tension: 0,
            }));
            chart.update();
        }
    });

    onMount(() => {
        if (!canvas) {
            return;
        }

        const rawData = $state.snapshot(graphData);
        chart = new Chart(canvas, {
            type: 'line',
            data: {
                datasets: rawData.map((team) => ({
                    label: team.team_name,
                    data: team.data as any[],
                    borderColor: team.color,
                    backgroundColor: team.color,
                    borderWidth: 3,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHitRadius: 10,
                    stepped: true,
                    tension: 0,
                })),
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false,
                },
                plugins: {
                    tooltip: {
                        backgroundColor: 'rgba(9, 9, 11, 0.9)', // Tailwind zinc-950 with opacity
                        titleColor: '#fff',
                        bodyColor: '#e4e4e7', // Tailwind zinc-200
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: true,
                        boxPadding: 4,
                    },
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 20,
                            font: {
                                family: 'Inter, ui-sans-serif, system-ui, sans-serif',
                                size: 12,
                            },
                        },
                    },
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            tooltipFormat: 'PPpp',
                            displayFormats: {
                                hour: 'HH:mm',
                                day: 'd MMM',
                            },
                        },
                        adapters: {
                            date: {
                                locale: id,
                            },
                        },
                        title: {
                            display: false,
                        },
                        grid: {
                            display: false, // Cleaner without vertical lines
                        },
                        ticks: {
                            font: {
                                family: 'Inter, ui-sans-serif, system-ui, sans-serif',
                            },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Total Poin',
                            font: {
                                family: 'Inter, ui-sans-serif, system-ui, sans-serif',
                            },
                        },
                        grid: {
                            color: 'rgba(156, 163, 175, 0.15)',
                        },
                        border: { display: false },
                        ticks: {
                            font: {
                                family: 'Inter, ui-sans-serif, system-ui, sans-serif',
                            },
                            padding: 10,
                        },
                    },
                },
            },
        });
    });

    onDestroy(() => {
        if (chart) {
            chart.destroy();
        }
    });
</script>

<div class="h-[400px] w-full">
    <canvas bind:this={canvas}></canvas>
</div>
