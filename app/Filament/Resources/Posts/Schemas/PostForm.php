<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Select::make('category_id')
                    ->label('Kategori')
                    ->relationship('category', 'name')
                    ->required(),
                TextInput::make('title')
                    ->label('Judul Postingan')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, callable $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                \Filament\Forms\Components\RichEditor::make('content')
                    ->label('Konten Berita')
                    ->required()
                    ->columnSpanFull(),
                FileUpload::make('image_url')
                    ->label('Gambar Utama (Thumbnail)')
                    ->image()
                    ->disk('public')
                    ->directory('posts'),
                \Filament\Forms\Components\Repeater::make('share_links')
                    ->label('Kumpulan Link Afiliasi (Shopee/TikTok)')
                    ->schema([
                        TextInput::make('url')
                            ->label('URL')
                            ->url()
                            ->required(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
