<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_url')
                    ->label('Gambar')
                    ->square()
                    ->disk('public')
                    ->url(fn (Post $record) => $record->image_url ? asset(Storage::url($record->image_url)) : null),
                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->color('gray'),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                \Filament\Actions\Action::make('visit')
                    ->label('Kunjungi')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('info')
                    ->url(fn (Post $record): string => route('blog.show', $record->slug))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
