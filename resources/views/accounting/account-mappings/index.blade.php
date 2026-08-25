@extends('layouts.layout')

@push('styles')
<style>
@keyframes fadeSlideUp { 0%{opacity:0;transform:translateY(12px)} 100%{opacity:1;transform:translateY(0)} }
.fade-up { animation:fadeSlideUp 0.5s cubic-bezier(0.16,1,0.3,1) both; }
</style>
@endpush

@section('content')
<div class="space-y-5 max-w-3xl">

    @foreach(['success','error'] as $t)
        @if(session($t))
        <div class="flex items-center gap-2.5 px-4 py-3 rounded-xl text-sm font-medium animate-[fadeSlideUp_0.3s_ease] @switch($t) @case('success') bg-green-50 text-green-700 @break @case('error') bg-red-50 text-red-700 @break @endswitch">
            <span>{{ session($t) }}</span>
        </div>
        @endif
    @endforeach

    <div class="fade-up">
        <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Account Mappings</h1>
        <p class="text-sm text-gray-400 mt-0.5">Configure which Chart of Accounts account is used for each accounting category</p>
    </div>

    <div class="fade-up bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <form method="POST" action="{{ route('accounting.account-mappings.store') }}">
            @csrf

            <div class="space-y-4">
                @foreach($mappingDefinitions as $key => $label)
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-gray-900">{{ $label }}</p>
                            <p class="text-[0.6rem] text-gray-400 font-mono mt-0.5">{{ $key }}</p>
                        </div>
                        <div class="min-w-[280px]">
                            <select name="mappings[{{ $loop->index }}][account_id]" required
                                    class="w-full text-xs border border-gray-200 rounded-lg px-3 py-2.5 outline-none transition-all focus:border-gray-400 bg-white">
                                <option value="">Select account…</option>
                                @foreach($accounts as $account)
                                    <option value="{{ $account->id }}" {{ ($existingMappings[$key] ?? null) == $account->id ? 'selected' : '' }}>
                                        {{ $account->account_code }} – {{ $account->account_name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="hidden" name="mappings[{{ $loop->index }}][key]" value="{{ $key }}">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit"
                        class="flex-1 py-2.5 bg-gray-900 text-white rounded-xl text-sm font-semibold transition-all hover:bg-gray-800 active:scale-[0.97] cursor-pointer border-0">
                    Save Mappings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
