<?php

namespace App\Filament\Resources\Challenges\Schemas;

use App\Enums\ChallengeLevel;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class ChallengeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('challenge_tabs')
                    ->columnSpan('full')
                    ->tabs([
                        Tabs\Tab::make('Informasi Umum')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('event_id')
                                        ->label('Acara')
                                        ->relationship('event', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->helperText('Pilih acara/event untuk tantangan ini.'),
                                    Select::make('category_id')
                                        ->label('Kategori')
                                        ->relationship('category', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->helperText('Kategori soal (Web, Forensic, Crypto, dll).'),
                                ]),
                                TextInput::make('title')
                                    ->label('Judul Tantangan')
                                    ->required()
                                    ->maxLength(255)
                                    ->placeholder('Contoh: SQL Injection 101')
                                    ->columnSpanFull(),
                                RichEditor::make('description')
                                    ->label('Deskripsi Tantangan')
                                    ->helperText('Berikan petunjuk dan konteks soal untuk peserta.')
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('challenge-images')
                                    ->fileAttachmentsVisibility('public')
                                    ->columnSpanFull(),
                            ]),

                        Tabs\Tab::make('Skor & Tingkat')
                            ->icon('heroicon-o-chart-bar')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('base_score')
                                        ->label('Skor Dasar')
                                        ->required()
                                        ->numeric()
                                        ->minValue(1)
                                        ->placeholder('100')
                                        ->helperText('Poin maksimal yang diperoleh saat tantangan diselesaikan.'),
                                    Select::make('difficulty')
                                        ->label('Tingkat Kesulitan')
                                        ->options(ChallengeLevel::class)
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->helperText('Menentukan badge warna pada tabel tantangan.'),
                                ]),
                                Section::make('Bendera (Flag)')
                                    ->description('Kunci jawaban yang harus disubmit peserta. Pastikan format flag konsisten (misal: CTF{...}).')
                                    ->icon('heroicon-o-key')
                                    ->schema([
                                        TextInput::make('flag')
                                            ->label('Bendera (Flag)')
                                            ->required()
                                            ->password()
                                            ->revealable()
                                            ->maxLength(255)
                                            ->placeholder('CTF{contoh_flag_disini}')
                                            ->helperText('Klik ikon mata untuk melihat/menyembunyikan flag.'),
                                    ]),
                                Toggle::make('is_active')
                                    ->label('Aktifkan Tantangan')
                                    ->helperText('Tantangan yang tidak aktif tidak akan terlihat oleh peserta.')
                                    ->default(true),
                            ]),

                        Tabs\Tab::make('Lampiran')
                            ->icon('heroicon-o-paper-clip')
                            ->badge(fn (\Filament\Schemas\Components\Utilities\Get $get): ?string => count($get('attachments') ?? []) ?: null)
                            ->schema([
                                Placeholder::make('attachments_info')
                                    ->content('Tambahkan file pendukung seperti binary, pcap, gambar, atau file lain yang diperlukan peserta untuk menyelesaikan tantangan.')
                                    ->columnSpanFull(),
                                Repeater::make('attachments')
                                    ->label('')
                                    ->relationship()
                                    ->schema([
                                        TextInput::make('file_name')
                                            ->label('Nama File')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('challenge.zip'),
                                        FileUpload::make('file_path')
                                            ->label('File')
                                            ->disk('local')
                                            ->directory('challenge-attachments')
                                            ->visibility('private')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->defaultItems(0)
                                    ->addActionLabel('+ Tambah Lampiran')
                                    ->reorderable()
                                    ->collapsible()
                                    ->itemLabel(fn (array $state): ?string => $state['file_name'] ?? 'Lampiran Baru')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }
}
