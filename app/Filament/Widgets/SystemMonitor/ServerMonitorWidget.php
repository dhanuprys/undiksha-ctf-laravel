<?php

namespace App\Filament\Widgets\SystemMonitor;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServerMonitorWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $cpu = $this->getCpuInfo();
        $ram = $this->getRamInfo();
        $disk = $this->getDiskInfo();

        return [
            Stat::make('CPU Load', $cpu['load_1m'])
                ->description("5m: {$cpu['load_5m']} | 15m: {$cpu['load_15m']} ({$cpu['cores']} Cores)")
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color($this->getHealthColor($cpu['percent'])),

            Stat::make('RAM Usage', "{$ram['used']} / {$ram['total']}")
                ->description("{$ram['percent']}% Terpakai")
                ->descriptionIcon('heroicon-m-server')
                ->color($this->getHealthColor($ram['percent'])),

            Stat::make('Disk Usage', "{$disk['used']} / {$disk['total']}")
                ->description("{$disk['percent']}% Terpakai pada {$disk['path']}")
                ->descriptionIcon('heroicon-m-circle-stack')
                ->color($this->getHealthColor($disk['percent'])),

            Stat::make('Uptime', $this->getUptime())
                ->description('Waktu Server Berjalan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('success'),

            Stat::make('PHP Memory', $this->getPhpMemory())
                ->description('Limit: '.ini_get('memory_limit'))
                ->descriptionIcon('heroicon-m-code-bracket')
                ->color('primary'),
        ];
    }

    private function getHealthColor($percentage): string
    {
        if ($percentage >= 90) {
            return 'danger';
        }
        if ($percentage >= 75) {
            return 'warning';
        }

        return 'success';
    }

    private function getCpuInfo(): array
    {
        $cores = 1;
        if (PHP_OS_FAMILY === 'Linux' && is_readable('/proc/cpuinfo')) {
            $cpuinfo = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $cpuinfo, $matches);
            $cores = count($matches[0]) ?: 1;
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            $cores = (int) shell_exec('sysctl -n hw.ncpu') ?: 1;
        }

        $load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];
        $percent = min(100, round(($load[0] / $cores) * 100));

        return [
            'load_1m' => round($load[0], 2),
            'load_5m' => round($load[1], 2),
            'load_15m' => round($load[2], 2),
            'cores' => $cores,
            'percent' => $percent,
        ];
    }

    private function getRamInfo(): array
    {
        $total = 0;
        $used = 0;

        if (PHP_OS_FAMILY === 'Linux' && is_readable('/proc/meminfo')) {
            $meminfo = file_get_contents('/proc/meminfo');
            preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $totalMatches);
            preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $availMatches);

            if (isset($totalMatches[1])) {
                $totalBytes = $totalMatches[1] * 1024;
                $availBytes = isset($availMatches[1]) ? $availMatches[1] * 1024 : 0;
                $usedBytes = $totalBytes - $availBytes;

                $total = $this->formatBytes($totalBytes);
                $used = $this->formatBytes($usedBytes);
                $percent = round(($usedBytes / $totalBytes) * 100, 1);

                return ['total' => $total, 'used' => $used, 'percent' => $percent];
            }
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            $totalBytes = (float) shell_exec('sysctl -n hw.memsize');
            $vmstat = shell_exec('vm_stat');

            preg_match('/Pages active:\s+(\d+)/', $vmstat, $activeMatches);
            preg_match('/Pages wired down:\s+(\d+)/', $vmstat, $wiredMatches);

            $pageSize = 4096;
            $usedBytes = ((int) ($activeMatches[1] ?? 0) + (int) ($wiredMatches[1] ?? 0)) * $pageSize;

            if ($totalBytes > 0) {
                $total = $this->formatBytes($totalBytes);
                $used = $this->formatBytes($usedBytes);
                $percent = round(($usedBytes / $totalBytes) * 100, 1);

                return ['total' => $total, 'used' => $used, 'percent' => $percent];
            }
        }

        return ['total' => 'N/A', 'used' => 'N/A', 'percent' => 0];
    }

    private function getDiskInfo(): array
    {
        $path = base_path();
        $totalBytes = disk_total_space($path);
        $freeBytes = disk_free_space($path);

        if ($totalBytes > 0) {
            $usedBytes = $totalBytes - $freeBytes;
            $percent = round(($usedBytes / $totalBytes) * 100, 1);

            return [
                'total' => $this->formatBytes($totalBytes),
                'used' => $this->formatBytes($usedBytes),
                'percent' => $percent,
                'path' => $path,
            ];
        }

        return ['total' => 'N/A', 'used' => 'N/A', 'percent' => 0, 'path' => $path];
    }

    private function getUptime(): string
    {
        if (PHP_OS_FAMILY === 'Linux' && is_readable('/proc/uptime')) {
            $uptime = (float) explode(' ', file_get_contents('/proc/uptime'))[0];

            return $this->formatDuration($uptime);
        } elseif (PHP_OS_FAMILY === 'Darwin') {
            $bootTime = (int) shell_exec('sysctl -n kern.boottime | awk \'{print $4}\' | tr -d \',\'');
            if ($bootTime > 0) {
                return $this->formatDuration(time() - $bootTime);
            }
        }

        return 'N/A';
    }

    private function getPhpMemory(): string
    {
        return $this->formatBytes(memory_get_usage());
    }

    private function formatBytes($bytes, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision).' '.$units[$pow];
    }

    private function formatDuration($seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        $parts = [];
        if ($days > 0) {
            $parts[] = "{$days}d";
        }
        if ($hours > 0) {
            $parts[] = "{$hours}h";
        }
        if ($minutes > 0) {
            $parts[] = "{$minutes}m";
        }

        return count($parts) > 0 ? implode(' ', $parts) : '< 1m';
    }
}
