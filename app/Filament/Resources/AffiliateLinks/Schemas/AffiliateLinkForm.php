<?php

namespace App\Filament\Resources\AffiliateLinks\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class AffiliateLinkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([

            Section::make('URL & Scraping Otomatis')
                ->description('Isi URL sumber untuk mengambil data (judul, gambar, deskripsi) secara otomatis.')
                ->schema([
                    Textarea::make('source_url')
                        ->label('URL Sumber (untuk Scraping Meta Tag)')
                        ->rows(2)
                        ->helperText('Masukkan URL halaman produk/berita lalu klik di luar kotak ini untuk mengisi data otomatis. Contoh: https://shopee.co.id/produk-xyz')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (?string $state, \Filament\Schemas\Components\Utilities\Set $set) {
                            if (blank($state)) return;

                            try {
                                $response = \Illuminate\Support\Facades\Http::timeout(10)
                                    ->withOptions(['verify' => false])
                                    ->withHeaders([
                                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                                        'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
                                    ])
                                    ->get($state);

                                if ($response->successful()) {
                                    $html = $response->body();

                                    // Ambil Title dari og:title atau <title>
                                    if (
                                        preg_match('/\<meta[^\>]*property=["\']og:title["\'][^\>]*content=["\'](.*?)["\']/is', $html, $m) ||
                                        preg_match('/\<title[^\>]*\>(.*?)\<\/title\>/is', $html, $m)
                                    ) {
                                        $title = trim(html_entity_decode($m[1]));
                                        $set('title', $title);
                                        $set('slug', Str::slug($title));
                                    }

                                    // Ambil Description
                                    if (
                                        preg_match('/\<meta[^\>]*property=["\']og:description["\'][^\>]*content=["\'](.*?)["\']/is', $html, $m) ||
                                        preg_match('/\<meta[^\>]*name=["\']description["\'][^\>]*content=["\'](.*?)["\']/is', $html, $m)
                                    ) {
                                        $set('description', trim(html_entity_decode($m[1])));
                                    }

                                    // Ambil Image dari og:image
                                    if (preg_match('/\<meta[^\>]*property=["\']og:image["\'][^\>]*content=["\'](.*?)["\']/is', $html, $m)) {
                                        $set('image_url', trim($m[1]));
                                    }
                                }
                            } catch (\Exception $e) {
                                // Abaikan error scraping
                            }
                        }),

                    Textarea::make('affiliate_url')
                        ->label('URL Afiliasi (Link Produk/Shopee)')
                        ->rows(3)
                        ->helperText('Link afiliasi yang akan dibuka saat pengunjung mengklik iklan ini. Bisa berbeda dengan URL sumber di atas.')
                        ->required()
                        ->columnSpanFull(),
                ]),

            Section::make('Informasi Iklan')
                ->columns(2)
                ->schema([
                    TextInput::make('title')
                        ->label('Judul Iklan')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('slug')
                        ->label('Slug (URL)')
                        ->helperText('Terisi otomatis. Digunakan untuk /go/{slug}')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Textarea::make('image_url')
                        ->label('URL Gambar Thumbnail')
                        ->rows(2)
                        ->helperText('Terisi otomatis dari meta og:image. Bisa diedit manual.')
                        ->columnSpanFull(),

                    Textarea::make('description')
                        ->label('Deskripsi Singkat')
                        ->rows(2)
                        ->helperText('Deskripsi produk/penawaran. Terisi otomatis dari meta description.')
                        ->columnSpanFull(),

                    TextInput::make('badge')
                        ->label('Label Promosi (Opsional)')
                        ->helperText('Contoh: HOT DEAL, Diskon 50%, Terlaris')
                        ->maxLength(30)
                        ->placeholder('HOT DEAL'),

                    TextInput::make('cta_text')
                        ->label('Teks Tombol CTA')
                        ->helperText('Teks yang tampil di tombol iklan.')
                        ->default('Lihat Penawaran')
                        ->required()
                        ->maxLength(50),
                ]),

            Section::make('Pengaturan Tampilan')
                ->columns(2)
                ->schema([
                    Toggle::make('is_active')
                        ->label('Aktifkan Iklan')
                        ->helperText('Nonaktifkan untuk menyembunyikan iklan dari homepage tanpa menghapusnya.')
                        ->default(true),

                    TextInput::make('display_order')
                        ->label('Urutan Tampil')
                        ->numeric()
                        ->default(0)
                        ->helperText('Angka lebih kecil = tampil lebih dulu. 0 = urutan default (acak).'),
                ]),
        ]);
    }
}
