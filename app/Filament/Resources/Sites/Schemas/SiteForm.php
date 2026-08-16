<?php

namespace App\Filament\Resources\Sites\Schemas;

use Filament\Schemas\Schema;

class SiteForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Textarea::make('url')
                    ->label('URL Situs')
                    ->rows(2)
                    ->helperText('Masukkan link situs (misal: https://detik.com) dan klik di luar kotak ini untuk mengambil data otomatis.')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set) {
                        if (blank($state))
                            return;

                        try {
                            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($state);
                            if ($response->successful()) {
                                $html = $response->body();

                                // Ambil Title
                                if (preg_match('/<title[^>]*>(.*?)<\/title>/is', $html, $matches)) {
                                    $title = trim($matches[1]);
                                    $set('title', $title);
                                    $set('slug', \Illuminate\Support\Str::slug($title));
                                }

                                // Ambil Description
                                if (
                                    preg_match('/<meta[^>]*name=["\']description["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches) ||
                                    preg_match('/<meta[^>]*property=["\']og:description["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)
                                ) {
                                    $set('description', trim($matches[1]));
                                }

                                // Ambil Image
                                if (preg_match('/<meta[^>]*property=["\']og:image["\'][^>]*content=["\'](.*?)["\']/is', $html, $matches)) {
                                    $set('image_url', trim($matches[1]));
                                }
                            }
                        } catch (\Exception $e) {
                            // Abaikan error jika situs tidak bisa discrape
                        }
                    }),

                \Filament\Forms\Components\TextInput::make('title')
                    ->label('Judul Situs')
                    ->helperText('Terisi otomatis dari hasil pencarian URL.')
                    ->required(),

                \Filament\Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->helperText('Digunakan untuk URL rute (misal: /go/slug-situs).')
                    ->required()
                    ->unique(ignoreRecord: true),

                \Filament\Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->helperText('Deskripsi singkat situs (terisi otomatis).')
                    ->columnSpanFull(),

                \Filament\Forms\Components\Textarea::make('image_url')
                    ->label('URL Gambar / Thumbnail')
                    ->rows(2)
                    ->helperText('URL gambar akan terisi otomatis dari OpenGraph image situs tersebut.')
                    ->columnSpanFull(),

                \Filament\Forms\Components\Textarea::make('share_link')
                    ->label('Share Link (Background Tab)')
                    ->rows(3)
                    ->helperText('Link afiliasi atau share link yang akan terbuka di tab latar belakang saat pengunjung mengklik situs ini.')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
