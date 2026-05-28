@extends('layouts.layout')

@php
$inputClass = 'w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm text-gray-900 bg-gray-50 outline-none transition-all duration-150 focus:border-indigo-400 focus:bg-white focus:ring-2 focus:ring-indigo-200/50';
$labelClass = 'block text-xs font-semibold text-gray-700 mb-1.5';
$reqClass   = 'text-red-500 ml-0.5';
$descClass  = 'text-xs text-gray-400 mt-1 leading-relaxed';
@endphp

@section('content')
<div class="max-w-2xl space-y-6">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 -tracking-[0.02em]">Restore database</h1>
            <p class="text-xs text-gray-400 mt-0.5">Overwrite current data from a backup file</p>
        </div>
        <a href="{{ route('configuration.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Configuration
        </a>
    </div>

    <div class="flex items-start gap-3 px-4 py-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs leading-relaxed">
        <svg class="w-5 h-5 shrink-0 mt-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3.05h16.94a2 2 0 0 0 1.71-3.05L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
            <line x1="12" y1="9" x2="12" y2="13"></line>
            <line x1="12" y1="17" x2="12.01" y2="17"></line>
        </svg>
        <div>
            <strong>Destructive operation.</strong> Restoring replaces all current data with the backup. This cannot be undone. Export anything important first.
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex gap-3 items-start">
            <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
            </div>
            <div>
                <p class="text-sm font-bold text-gray-900">Select backup</p>
                <p class="text-xs text-gray-400">The app will be unavailable during restore</p>
            </div>
        </div>
        <div class="px-5 py-4">
            <form action="{{ route('configuration.restore') }}" method="POST" onsubmit="return confirm('Restore database from this file? All current data will be replaced.');">
                @csrf

                <div class="mb-4">
                    <label for="backup_file" class="{{ $labelClass }}">Backup file <span class="{{ $reqClass }}">*</span></label>
                    <select id="backup_file" name="backup_file" class="{{ $inputClass }}" required>
                        <option value="">&mdash; Choose a backup &mdash;</option>
                        @foreach ($backups as $backup)
                            <option value="{{ $backup['filename'] }}">{{ $backup['filename'] }} ({{ $backup['type'] }})</option>
                        @endforeach
                    </select>
                    <p class="{{ $descClass }}">Pick a file from your backup list</p>
                </div>

                <div class="flex items-start gap-2.5 px-3.5 py-3 rounded-lg bg-sky-50 border border-sky-200 text-sky-800 text-xs leading-relaxed mb-4">
                    <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="16" x2="12" y2="12"></line>
                        <line x1="12" y1="8" x2="12.01" y2="8"></line>
                    </svg>
                    <div>Do not close this page during restoration.</div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-100">
                    <a href="{{ route('configuration.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">Cancel</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-red-500 text-white rounded-xl text-sm font-bold hover:bg-red-600 transition-all duration-150 shadow-md">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M1 4v6h6M23 20v-6h-6"></path>
                            <path d="M20.49 9A9 9 0 0 0 5.64 5.64M3.51 15A9 9 0 0 0 18.36 18.36"></path>
                        </svg>
                        Restore database
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
