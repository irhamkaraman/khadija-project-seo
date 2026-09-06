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
                                // 1. Attempt with Facebook Bot User Agent (usually whitelisted by Shopee/TikTok for link previews)
                                $response = \Illuminate\Support\Facades\Http::timeout(15)
                                    ->withOptions([
                                        'verify' => false,
                                        'allow_redirects' => true
                                    ])
                                    ->withHeaders([
                                        'User-Agent' => 'facebookexternalhit/1.1 (+http://www.facebook.com/externalhit_uatext.php)',
                                        'Accept'     => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                                        'Accept-Language' => 'en-US,en;q=0.5',
                                    ])
                                    ->get($state);
                                
                                $html = $response->body();

                                // If empty or not successful, try fallback Googlebot
                                if (!$response->successful() || strlen($html) < 500) {
                                    $response = \Illuminate\Support\Facades\Http::timeout(15)
                                        ->withOptions(['verify' => false, 'allow_redirects' => true])
                                        ->withHeaders([
                                            'User-Agent' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
                                        ])
                                        ->get($state);
                                    $html = $response->body();
                                }

                                if ($html) {
                                    $dom = new \DOMDocument();
                                    @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
                                    
                                    $scrapedTitle = '';
                                    $scrapedDesc = '';
                                    $scrapedImage = '';

                                    $metas = $dom->getElementsByTagName('meta');
                                    foreach ($metas as $meta) {
                                        $property = strtolower($meta->getAttribute('property') ?: $meta->getAttribute('name'));
                                        $content = $meta->getAttribute('content');
                                        
                                        if (in_array($property, ['og:title', 'twitter:title']) && !$scrapedTitle) {
                                            $scrapedTitle = $content;
                                        }
                                        if (in_array($property, ['og:description', 'twitter:description', 'description']) && !$scrapedDesc) {
                                            $scrapedDesc = $content;
                                        }
                                        if (in_array($property, ['og:image', 'twitter:image', 'image']) && !$scrapedImage) {
                                            $scrapedImage = $content;
                                        }
                                    }

                                    if (!$scrapedTitle) {
                                        $titles = $dom->getElementsByTagName('title');
                                        if ($titles->length > 0) {
                                            $scrapedTitle = $titles->item(0)->textContent;
                                        }
                                    }

                                    if ($scrapedTitle) {
                                        $set('title', trim($scrapedTitle));
                                        $set('slug', Str::slug(trim($scrapedTitle)));
                                    }
                                    if ($scrapedDesc) {
                                        $set('description', trim($scrapedDesc));
                                    }
                                    if ($scrapedImage) {
                                        $set('image_url', $scrapedImage);
                                    }

                                    if ($scrapedTitle || $scrapedImage) {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Berhasil Menarik Data')
                                            ->body('Data SEO berhasil diambil dari sumber.')
                                            ->success()
                                            ->send();
                                    } else {
                                        \Filament\Notifications\Notification::make()
                                            ->title('Data tidak lengkap')
                                            ->body('Web tujuan mungkin memblokir scraper atau tidak memiliki tag meta.')
                                            ->warning()
                                            ->send();
                                    }
                                }
                            } catch (\Exception $e) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Gagal menarik data')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
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
