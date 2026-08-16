<?php

namespace App\Filament\Resources\Sites\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Table;

class SitesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                \Filament\Tables\Columns\ImageColumn::make('image_url')
                    ->label('Gambar')
                    ->square(),
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                \Filament\Tables\Columns\TextColumn::make('url')
                    ->label('URL Situs')
                    ->searchable()
                    ->limit(30),
                \Filament\Tables\Columns\TextColumn::make('share_link')
                    ->label('Share Link')
                    ->searchable()
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('buka_link')
                    ->label('Buka Link')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (\App\Models\Site $record): string => url('/' . $record->slug))
                    ->openUrlInNewTab(),
                \Filament\Actions\EditAction::make(),
            ])
            ->toolbarActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
