<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Acara')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Acara')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('year')
                                ->label('Tahun')
                                ->required()
                                ->numeric(),
                            DateTimePicker::make('start_time')
                                ->label('Waktu Mulai'),
                            DateTimePicker::make('end_time')
                                ->label('Waktu Berakhir')
                                ->after('start_time'),
                            Toggle::make('is_active')
                                ->label('Aktif')
                                ->default(false)
                                ->columnSpanFull(),
                        ]),
                    ]),
                Section::make('Pengaturan Skor')
                    ->description('Pengaturan untuk skor dinamis dan penalti pada event ini.')
                    ->columnSpan('full')
                    ->schema([
                        Grid::make(3)->schema([
                            TextInput::make('degradation_rate')
                                ->label('Pengurangan Solver ke-2+')
                                ->helperText('Persentase pengurangan skor untuk solver ke-2 dan seterusnya. Solver pertama selalu mendapat skor penuh. Contoh: 0.10 = pengurangan 10%.')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(1)
                                ->step(0.01)
                                ->default(0.10)
                                ->required(),
                            TextInput::make('penalty_deduction')
                                ->label('Persentase Penalti Jawaban Salah')
                                ->helperText('Persentase skor dasar yang dikurangi per jawaban salah. Contoh: 0.05 = 5% dari base score.')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(1)
                                ->step(0.01)
                                ->default(0.05)
                                ->required(),
                            TextInput::make('max_team_size')
                                ->label('Maks Anggota Tim')
                                ->helperText('Batas maksimal anggota dalam satu tim.')
                                ->numeric()
                                ->minValue(1)
                                ->maxValue(100)
                                ->default(3)
                                ->required(),
                            Toggle::make('show_solver_count')
                                ->label('Tampilkan Jumlah Solver')
                                ->helperText('Tampilkan ke peserta berapa banyak tim yang telah menyelesaikan challenge tertentu.')
                                ->default(true)
                                ->columnSpanFull(),
                        ]),
                    ]),
            ]);
    }
}
