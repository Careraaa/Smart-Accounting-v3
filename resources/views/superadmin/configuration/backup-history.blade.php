@extends('layouts.layout')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">

    <div id="download-overlay" class="fixed inset-0 z-[99999] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm" style="display:none;">
        <div class="bg-white rounded-2xl px-10 py-8 shadow-2xl text-center max-w-sm border border-gray-200 scale-in">
            <div class="w-9 h-9 border-[3px] border-gray-100 border-t-gray-900 rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-sm font-bold text-gray-900">Preparing download...</p>
            <p class="text-xs text-gray-400 mt-1.5">Your backup is being packaged</p>
        </div>
    </div>

    <div class="fade-in flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Backup history</h1>
            <p class="text-sm text-gray-400 mt-1">Download or remove stored database backups</p>
        </div>
        <a href="{{ route('configuration.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-gray-900 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 no-underline active:scale-[0.97]">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Configuration
        </a>
    </div>

    @if (session('success'))
    <div class="slide-in flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-emerald-50 border border-emerald-200 text-emerald-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if (session('error'))
    <div class="slide-in flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium bg-red-50 border border-red-200 text-red-700">
        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 9l-6 6M9 9l6 6"/></svg>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    @if (count($backups) > 0)
    @php
        $totalSize = array_sum(array_column($backups, 'size'));
    @endphp
    <div class="fade-in flex items-center gap-4 text-xs text-gray-400">
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
            {{ count($backups) }} {{ Str::plural('file', count($backups)) }}
        </span>
        <span class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            {{ number_format($totalSize / 1024 / 1024, 2) }} MB total
        </span>
    </div>

    <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="text-left px-5 py-3.5 text-[0.6rem] font-bold uppercase tracking-widest text-gray-400">File</th>
                        <th class="text-left px-5 py-3.5 text-[0.6rem] font-bold uppercase tracking-widest text-gray-400">Size</th>
                        <th class="text-left px-5 py-3.5 text-[0.6rem] font-bold uppercase tracking-widest text-gray-400">Created</th>
                        <th class="text-right px-5 py-3.5 text-[0.6rem] font-bold uppercase tracking-widest text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($backups as $i => $backup)
                        <tr class="row-in border-b border-gray-50 hover:bg-gray-50/60 transition-colors duration-150" style="--i: {{ $i }};">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gray-50 border border-gray-100 rounded-lg flex items-center justify-center text-gray-300 shrink-0">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    </div>
                                    <span class="font-mono text-sm font-semibold text-gray-900 truncate max-w-[280px]">{{ $backup['filename'] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="font-mono text-xs bg-gray-100/80 px-2.5 py-1.5 rounded-lg text-gray-600 font-medium">{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</span>
                            </td>
                            <td class="px-5 py-4 font-mono text-sm text-gray-400 whitespace-nowrap">{{ date('M d, Y H:i:s', $backup['created_at']) }}</td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 justify-end">
                                    <a href="{{ route('configuration.backup-download', ['filename' => $backup['filename']]) }}" class="download-link inline-flex items-center gap-1.5 px-3 py-2 bg-white text-gray-600 border border-gray-200 rounded-lg text-xs font-semibold hover:border-gray-900 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 no-underline active:scale-[0.95]">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                        Download
                                    </a>
                                    <form action="{{ route('configuration.backup-delete', ['filename' => $backup['filename']]) }}" method="POST" data-sa-confirm="Delete this backup permanently?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 transition-all duration-200 active:scale-[0.95]">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="scale-in bg-white rounded-xl border border-gray-100 shadow-sm">
        <div class="flex flex-col items-center justify-center py-16 text-center px-6">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 text-gray-200 border border-gray-100">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
            </div>
            <p class="text-sm font-bold text-gray-700 mb-1.5">No backups yet</p>
            <p class="text-xs text-gray-400 max-w-[240px]">Run a backup from the configuration page to see files here</p>
        </div>
    </div>
    @endif
</div>

<style>
@keyframes fadeIn { 0%{opacity:0} 100%{opacity:1} }
@keyframes slideIn { 0%{opacity:0;transform:translateY(-8px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.97)} 100%{opacity:1;transform:scale(1)} }
@keyframes rowIn { 0%{opacity:0;transform:translateX(-6px)} 100%{opacity:1;transform:translateX(0)} }
.fade-in { animation:fadeIn 0.4s ease-out both; }
.slide-in { animation:slideIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
.row-in { animation:rowIn 0.3s cubic-bezier(0.16,1,0.3,1) both; animation-delay: calc(0.03s * var(--i)); }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var overlay = document.getElementById('download-overlay');

    document.querySelectorAll('.download-link').forEach(function (link) {
        link.addEventListener('click', async function (e) {
            e.preventDefault();
            var url = this.href;
            overlay.style.display = 'flex';

            try {
                var resp = await fetch(url, { redirect: 'manual' });

                if (resp.type === 'opaqueredirect' || (resp.status >= 300 && resp.status < 400)) {
                    window.location.reload();
                    return;
                }

                if (!resp.ok) {
                    var msg = 'Server returned ' + resp.status;
                    try {
                        var text = await resp.text();
                        if (text && text.length < 500) msg = text;
                    } catch (_) {}
                    throw new Error(msg);
                }

                var ct = resp.headers.get('Content-Type') || '';
                if (ct.includes('text/html')) {
                    window.location.reload();
                    return;
                }

                var blob = await resp.blob();
                var filename = 'backup.zip';
                var cd = resp.headers.get('Content-Disposition');
                if (cd) {
                    var m = cd.match(/filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/);
                    if (m && m[1]) filename = m[1].replace(/['"]/g, '');
                }

                var blobUrl = URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = blobUrl;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                URL.revokeObjectURL(blobUrl);
            } catch (err) {
                if (window.saAlert) {
                    await window.saAlert({ title: 'Download Error', message: err.message || 'Download failed' });
                } else {
                    alert('Download failed: ' + (err.message || ''));
                }
            } finally {
                overlay.style.display = 'none';
            }
        });
    });

    var flashes = document.querySelectorAll('.slide-in');
    flashes.forEach(function (el) {
        setTimeout(function () {
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            el.style.transition = 'all 0.3s ease';
            setTimeout(function () { el.remove(); }, 300);
        }, 5000);
    });
});
</script>
@endsection