<?php

namespace App\Filament\Exports;

use App\Models\Event;
use App\Models\Submission;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class SubmissionExporter extends Exporter
{
    protected static ?string $model = Submission::class;

    public static function getColumns(): array
    {
        $sanitize = fn (?string $state): ?string => $state && str($state)->startsWith(['=', '+', '-', '@']) ? "'".$state : $state;

        return [
            ExportColumn::make('created_at')
                ->label('Waktu Submit')
                ->formatStateUsing(fn ($state) => $state ? Carbon::parse($state)->format('d M Y, H:i:s') : null),
            ExportColumn::make('user.name')
                ->label('Nama Pengguna')
                ->formatStateUsing($sanitize),
            ExportColumn::make('team.name')
                ->label('Tim')
                ->formatStateUsing($sanitize),
            ExportColumn::make('challenge.title')
                ->label('Tantangan')
                ->formatStateUsing($sanitize),
            ExportColumn::make('submitted_flag')
                ->label('Bendera (Flag)')
                ->formatStateUsing($sanitize),
            ExportColumn::make('is_correct')
                ->label('Status Benar')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Benar' : 'Salah'),
            ExportColumn::make('points_awarded')
                ->label('Poin Diberikan'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Ekspor data Log Jawaban Soal telah selesai. '.Number::format($export->successful_rows).' baris berhasil diekspor.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' baris gagal diekspor.';
        }

        return $body;
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['user', 'team', 'challenge']);
    }

    public static function getOptionsFormComponents(): array
    {
        return [
            Select::make('event_id')
                ->label('Acara (Event)')
                ->options(Event::pluck('name', 'id'))
                ->required()
                ->searchable(),
        ];
    }

    public function getJobConnection(): ?string
    {
        return 'sync';
    }

    public function getFileName(Export $export): string
    {
        $eventName = 'All Events';

        if (isset($this->options['event_id'])) {
            $event = Event::find($this->options['event_id']);
            if ($event) {
                $eventName = $event->name;
            }
        }

        $timestamp = now()->format('Y-m-d-H-i-s');

        return (string) str("Log Jawaban Soal Export - {$eventName} - {$timestamp}")->slug();
    }
}
