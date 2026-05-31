@extends('layouts.layout')

@section('content')
<div class="space-y-6">
    <div class="flex items-start justify-between flex-wrap gap-4">
        <div>
            <h1 class="text-xl font-extrabold text-gray-900 -tracking-[0.02em]">Backup history</h1>
            <p class="text-xs text-gray-400 mt-0.5">Download or remove stored database backups</p>
        </div>
        <a href="{{ route('configuration.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 border border-gray-200 rounded-xl text-sm font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Configuration
        </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        @if (count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">File</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Size</th>
                            <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Created</th>
                            <th class="text-right px-5 py-3 text-xs font-semibold uppercase tracking-wider text-gray-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($backups as $backup)
                            <tr class="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-sm font-semibold text-gray-900">{{ $backup['filename'] }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="font-mono text-xs bg-gray-100 px-2.5 py-1.5 rounded-lg">{{ number_format($backup['size'] / 1024 / 1024, 2) }} MB</span>
                                </td>
                                <td class="px-5 py-3.5 font-mono text-sm text-gray-500">{{ date('M d, Y H:i:s', $backup['created_at']) }}</td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2 justify-end">
                                        <a href="{{ route('configuration.backup-download', ['filename' => $backup['filename']]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 bg-white text-gray-700 border border-gray-200 rounded-lg text-xs font-semibold hover:border-[#c8292a] hover:text-[#c8292a] hover:bg-red-50 transition-all duration-150 no-underline">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                                            Download
                                        </a>
                                        <form action="{{ route('configuration.backup-delete', ['filename' => $backup['filename']]) }}" method="POST" data-sa-confirm="Delete this backup permanently?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-500 text-white rounded-lg text-xs font-semibold hover:bg-red-600 transition-all duration-150">
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
        @else
            <div class="flex flex-col items-center justify-center py-14 text-center">
                <div class="w-14 h-14 bg-gray-100 rounded-2xl flex items-center justify-center mb-3.5 text-gray-300">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20m10-10H2"></path></svg>
                </div>
                <p class="text-sm font-bold text-gray-600 mb-1.5">No backups yet</p>
                <p class="text-xs text-gray-400">Run a backup from configuration to see files here</p>
            </div>
        @endif
    </div>
</div>
@endsection
