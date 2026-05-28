@extends('layouts.layout')
@push('styles')
<style>
@keyframes fadeUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
@keyframes scaleIn { 0%{opacity:0;transform:scale(0.93)} 100%{opacity:1;transform:scale(1)} }
.fade-up { animation:fadeUp 0.4s cubic-bezier(0.16,1,0.3,1) both; }
.scale-in { animation:scaleIn 0.35s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush
@section('content')
{{-- Header --}}
    <div class="flex items-start justify-between flex-wrap gap-4 mb-5 fade-up">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">PAOs / Conductors</h1>
            <p class="text-sm text-gray-500 mt-0.5">Manage Passenger Assistant Officers for remittance operations</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('reports.print.pao-report') }}" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold border border-gray-200 text-gray-600 bg-white hover:bg-gray-50 transition-all no-underline">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Print
            </a>
            <a href="{{ route('paos.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-bold text-white bg-gray-900 hover:bg-gray-800 shadow-sm transition-all no-underline">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                Add PAO
            </a>
        </div>
    </div>

    {{-- Flash messages --}}
    @foreach(['success','error','info'] as $t)
        @if(session($t))
        <div class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-semibold mb-4 fade-up {{ $t === 'success' ? 'bg-emerald-500/15 border border-emerald-500/25 text-emerald-700' : 'bg-red-500/15 border border-red-500/25 text-red-700' }}">
            @if($t==='success')<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            @else<svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 16v-4m0-4h.01"/></svg>@endif
            {{ session($t) }}
        </div>
        @endif
    @endforeach

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6 fade-up">
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total PAOs</p>
                <p class="text-lg font-bold text-gray-900">{{ $totalPAOs }}</p>
                <p class="text-[10px] text-gray-400">all PAOs</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Active</p>
                <p class="text-lg font-bold text-gray-900">{{ $activePAOs }}</p>
                <p class="text-[10px] text-gray-400">currently active</p>
            </div>
        </div>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 flex items-center gap-3.5">
            <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Inactive</p>
                <p class="text-lg font-bold text-gray-900">{{ $inactivePAOs }}</p>
                <p class="text-[10px] text-gray-400">currently inactive</p>
            </div>
        </div>
    </div>

    {{-- Filter bar --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-3 mb-4 flex items-center gap-3 flex-wrap fade-up">
        <div class="relative flex-1 min-w-[180px]">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="paoSearch" placeholder="Search PAO…" value="{{ request('search') }}" class="w-full border border-gray-200 rounded-lg pl-9 pr-3 py-2 text-xs text-gray-700 placeholder:text-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all">
        </div>
        <select id="statusFilter" class="border border-gray-200 rounded-lg px-3 py-2 text-xs text-gray-700 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-300 transition-all">
            <option value="">All Statuses</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden fade-up">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-50 bg-gray-50/50">
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">
                            <a href="{{ route('paos.index', ['sort_by' => 'name', 'sort_order' => ($sortBy === 'name' && $sortOrder === 'asc') ? 'desc' : 'asc']) }}" class="text-gray-400 no-underline hover:text-gray-600 inline-flex items-center gap-1">
                                Name
                                @if($sortBy === 'name')<svg width="10" height="10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $sortOrder === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"/></svg>@endif
                            </a>
                        </th>
                        <th class="text-left px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Contact</th>
                        <th class="text-center px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Status</th>
                        <th class="text-right px-5 py-3 text-[0.55rem] font-bold uppercase tracking-wider text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody id="paoTbody" class="divide-y divide-gray-50">
                    @forelse($paos as $pao)
                    @php $sc = match($pao->status) { 'active' => 'bg-emerald-500/10 text-emerald-600 border-emerald-200/30', 'pending' => 'bg-amber-500/10 text-amber-600 border-amber-200/30', default => 'bg-red-500/10 text-red-600 border-red-200/30' }; @endphp
                    <tr class="transition-colors hover:bg-gray-50/50 cursor-pointer"
                        data-name="{{ strtolower($pao->name) }}"
                        data-status="{{ $pao->status }}"
                        onclick="window.location='{{ route('paos.show', $pao) }}'">
                        <td class="px-5 py-3.5">
                            <span class="text-xs font-bold text-gray-900">{{ $pao->name }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-xs text-gray-500 font-mono">{{ $pao->contact_number }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-semibold border {{ $sc }}">{{ ucfirst($pao->status) }}</span>
                        </td>
                        <td class="px-5 py-3.5 text-right" onclick="event.stopPropagation()">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('paos.show', $pao) }}" title="View" class="w-7 h-7 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <a href="{{ route('paos.edit', $pao) }}" title="Edit" class="w-7 h-7 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white flex items-center justify-center transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <button type="button" title="Delete" onclick="openDeleteModal({{ $pao->id }}, '{{ $pao->name }}')" class="w-7 h-7 rounded-lg bg-red-50 text-red-600 hover:bg-red-500 hover:text-white flex items-center justify-center transition-all border-0 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                                <form id="delete-form-{{ $pao->id }}" action="{{ route('paos.destroy', $pao) }}" method="POST" style="display:none;">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-12">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-xs text-gray-400">No PAOs found.</p>
                                <p class="text-[10px] text-gray-300">Add a PAO to get started.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="paoNoResults" style="display:none;">
            <div class="py-12 text-center">
                <p class="text-xs text-gray-400">No results</p>
                <p class="text-[10px] text-gray-300">Try adjusting your filters.</p>
            </div>
        </div>
    </div>

{{-- Delete Modal --}}
<div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center hidden" id="deleteModal">
    <div class="bg-white rounded-2xl shadow-xl p-6 max-w-sm w-full mx-4 scale-in">
        <div class="flex flex-col items-center text-center gap-3">
            <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900">Delete PAO?</h3>
            <p class="text-xs text-gray-500" id="deleteModalBody">This action cannot be undone.</p>
            <div class="flex gap-2 w-full pt-2">
                <button type="button" onclick="closeModal()" class="flex-1 px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-100 text-gray-600 transition-all hover:bg-gray-200 cursor-pointer border-0">Cancel</button>
                <button type="button" id="deleteConfirmBtn" class="flex-1 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-red-500 transition-all hover:bg-red-600 cursor-pointer border-0">Yes, Delete</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const search = document.getElementById('paoSearch');
    const statusF = document.getElementById('statusFilter');
    const tbody = document.getElementById('paoTbody');
    const noRes = document.getElementById('paoNoResults');
    function run() {
        const q = search.value.toLowerCase().trim();
        const s = statusF.value;
        const rows = Array.from(tbody.querySelectorAll('tr[data-name]'));
        const vis = rows.filter(r => (!q || r.dataset.name.includes(q)) && (!s || r.dataset.status === s));
        rows.forEach(r => r.style.display = 'none');
        vis.forEach(r => r.style.display = '');
        noRes.style.display = vis.length === 0 && rows.length > 0 ? 'block' : 'none';
    }
    search.addEventListener('input', run);
    statusF.addEventListener('change', run);
})();

let _deleteId = null;
function openDeleteModal(id, name) {
    _deleteId = id;
    document.getElementById('deleteModalBody').textContent = 'Delete PAO "' + name + '"? This cannot be undone.';
    document.getElementById('deleteModal').classList.remove('hidden');
}
function closeModal() {
    document.getElementById('deleteModal').classList.add('hidden');
}
document.getElementById('deleteConfirmBtn').addEventListener('click', function () {
    if (_deleteId) document.getElementById('delete-form-' + _deleteId).submit();
});
document.getElementById('deleteModal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
});
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });
</script>
@endpush
