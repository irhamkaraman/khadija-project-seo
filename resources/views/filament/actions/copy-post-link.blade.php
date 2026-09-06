@php
    $appUrl = config('app.url');
    $host = request()->getHttpHost();
    $parsedApp = parse_url($appUrl);
    if (!empty($parsedApp['host']) && !in_array($parsedApp['host'], ['localhost', '127.0.0.1'])) {
        $host = $parsedApp['host'] . (isset($parsedApp['port']) ? ':' . $parsedApp['port'] : '');
    }

    $wwwHost = str_starts_with($host, 'www.') ? $host : 'www.' . $host;
    $path = route('blog.show', $record->slug, false);

    $urlWithoutHttps = $wwwHost . $path;
    $urlWithHttps = 'https://' . $wwwHost . $path;
@endphp

<div x-data="{
    copied1: false,
    copied2: false,
    copyText(text, slot) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            let textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            textArea.remove();
        }
        if (slot === 1) {
            this.copied1 = true;
            setTimeout(() => this.copied1 = false, 2500);
        } else {
            this.copied2 = true;
            setTimeout(() => this.copied2 = false, 2500);
        }
    }
}" class="copy-modal-wrapper">

    <style>
        .copy-modal-wrapper {
            display: flex;
            flex-direction: column;
            gap: 16px;
            padding: 8px 0;
            font-family: inherit;
        }
        .copy-article-note {
            font-size: 13px;
            color: #9ca3af;
            line-height: 1.5;
            margin-bottom: 4px;
        }
        .copy-article-title {
            color: #f3f4f6;
            font-weight: 600;
        }
        .copy-format-card {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            transition: border-color 0.2s, background 0.2s;
        }
        .copy-format-card:hover {
            border-color: rgba(245, 158, 11, 0.35);
            background: rgba(255, 255, 255, 0.06);
        }
        .copy-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .copy-badge-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 600;
            color: #f9fafb;
        }
        .copy-dot-indicator {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }
        .dot-amber { background-color: #f59e0b; }
        .dot-emerald { background-color: #10b981; }
        .copy-sub-desc {
            font-size: 11px;
            color: #6b7280;
        }
        .copy-input-row {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .copy-url-input {
            flex: 1;
            width: 100%;
            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 12px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            color: #f3f4f6;
            outline: none;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .copy-url-input:focus {
            border-color: #f59e0b;
        }
        .copy-action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s ease;
            white-space: nowrap;
            min-width: 90px;
        }
        .btn-amber-theme {
            background-color: #d97706;
            color: #ffffff;
        }
        .btn-amber-theme:hover {
            background-color: #b45309;
        }
        .btn-emerald-theme {
            background-color: #059669;
            color: #ffffff;
        }
        .btn-emerald-theme:hover {
            background-color: #047857;
        }
        .btn-success-active {
            background-color: #16a34a !important;
            color: #ffffff !important;
        }
        .copy-icon-svg {
            width: 14px;
            height: 14px;
            display: inline-block;
        }

        /* Light Mode Styling */
        html:not(.dark) .copy-article-note {
            color: #6b7280;
        }
        html:not(.dark) .copy-article-title {
            color: #111827;
        }
        html:not(.dark) .copy-format-card {
            background: #f9fafb;
            border: 1px solid #e5e7eb;
        }
        html:not(.dark) .copy-badge-label {
            color: #1f2937;
        }
        html:not(.dark) .copy-sub-desc {
            color: #9ca3af;
        }
        html:not(.dark) .copy-url-input {
            background: #ffffff;
            border: 1px solid #d1d5db;
            color: #111827;
        }
    </style>

    <div class="copy-article-note">
        Pilih salah satu format tautan untuk artikel: <span class="copy-article-title">&ldquo;{{ $record->title }}&rdquo;</span>
    </div>

    {{-- Opsi 1: Tanpa HTTPS (Hanya www) --}}
    <div class="copy-format-card">
        <div class="copy-card-header">
            <div class="copy-badge-label">
                <span class="copy-dot-indicator dot-amber"></span>
                <span>Format WWW (Tanpa HTTPS)</span>
            </div>
            <span class="copy-sub-desc">Tidak perlu https://</span>
        </div>
        <div class="copy-input-row">
            <input type="text"
                   readonly
                   value="{{ $urlWithoutHttps }}"
                   @click="$event.target.select()"
                   class="copy-url-input">
            <button type="button"
                    @click="copyText('{{ $urlWithoutHttps }}', 1)"
                    :class="copied1 ? 'btn-success-active' : 'btn-amber-theme'"
                    class="copy-action-btn">
                <template x-if="!copied1">
                    <span style="display: flex; align-items: center; gap: 4px;">
                        <svg class="copy-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Salin
                    </span>
                </template>
                <template x-if="copied1">
                    <span style="display: flex; align-items: center; gap: 4px;">
                        <svg class="copy-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tersalin!
                    </span>
                </template>
            </button>
        </div>
    </div>

    {{-- Opsi 2: Dengan HTTPS (https://www) --}}
    <div class="copy-format-card">
        <div class="copy-card-header">
            <div class="copy-badge-label">
                <span class="copy-dot-indicator dot-emerald"></span>
                <span>Format Lengkap HTTPS (https://www...)</span>
            </div>
            <span class="copy-sub-desc">Dengan protokol https://</span>
        </div>
        <div class="copy-input-row">
            <input type="text"
                   readonly
                   value="{{ $urlWithHttps }}"
                   @click="$event.target.select()"
                   class="copy-url-input">
            <button type="button"
                    @click="copyText('{{ $urlWithHttps }}', 2)"
                    :class="copied2 ? 'btn-success-active' : 'btn-emerald-theme'"
                    class="copy-action-btn">
                <template x-if="!copied2">
                    <span style="display: flex; align-items: center; gap: 4px;">
                        <svg class="copy-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                        Salin
                    </span>
                </template>
                <template x-if="copied2">
                    <span style="display: flex; align-items: center; gap: 4px;">
                        <svg class="copy-icon-svg" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Tersalin!
                    </span>
                </template>
            </button>
        </div>
    </div>

</div>
