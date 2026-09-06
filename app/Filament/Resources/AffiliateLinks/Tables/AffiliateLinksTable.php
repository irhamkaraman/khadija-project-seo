<?php

namespace App\Filament\Resources\AffiliateLinks\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class AffiliateLinksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Gambar')
                    ->height(56)
                    ->width(80)
                    ->defaultImageUrl(url('/favicon.ico'))
                    ->extraImgAttributes(['style' => 'object-fit:cover; border-radius:6px;']),

                TextColumn::make('title')
                    ->label('Judul Iklan')
                    ->searchable()
                    ->sortable()
                    ->limit(50)
                    ->weight('bold'),

                TextColumn::make('badge')
                    ->label('Label')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('click_count')
                    ->label('Klik')
                    ->sortable()
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state >= 1000 => 'success',
                        $state >= 100  => 'warning',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => number_format($state)),

                ToggleColumn::make('is_active')
                    ->label('Aktif')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                \Filament\Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ]);
    }
}
