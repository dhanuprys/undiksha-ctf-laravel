<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\SystemMonitor\ServerMonitorWidget;
use BackedEnum;
use Filament\Pages\Page;
use UnitEnum;

class SystemMonitor extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string|UnitEnum|null $navigationGroup = 'Monitoring';

    protected static ?string $navigationLabel = 'Kinerja Server';

    protected static ?string $title = 'Kinerja Server';

    protected string $view = 'filament.pages.system-monitor';

    protected function getHeaderWidgets(): array
    {
        return [
            ServerMonitorWidget::class,
        ];
    }
}
