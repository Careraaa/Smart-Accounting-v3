@extends('layouts.layout')

@section('content')
<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Create Expense Allocation</h5>
                <a href="{{ route('accountant.expenses.allocations.index') }}" class="btn btn-secondary btn-sm">
                    <i class="feather-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="{{ route('accountant.expenses.allocations.store') }}" method="POST">
                @csrf

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="journal_entry_line_id">Journal Entry Line *</label>
                        <select id="journal_entry_line_id" name="journal_entry_line_id" class="form-select @error('journal_entry_line_id') is-invalid @enderror" required>
                            <option value="">Select Journal Entry Line</option>
                            @foreach(\App\Models\JournalEntryLine::where('account_type', 'Expense')->get() as $line)
                                <option value="{{ $line->id }}" {{ old('journal_entry_line_id') === (string)$line->id ? 'selected' : '' }}>
                                    JE-{{ str_pad($line->journalEntry->id, 6, '0', STR_PAD_LEFT) }} - {{ $line->glAccount->account_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('journal_entry_line_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="cost_center_id">Cost Center *</label>
                        <select id="cost_center_id" name="cost_center_id" class="form-select @error('cost_center_id') is-invalid @enderror" required>
                            <option value="">Select Cost Center</option>
                            @foreach(\App\Models\CostCenter::where('is_active', true)->get() as $center)
                                <option value="{{ $center->id }}" {{ old('cost_center_id') === (string)$center->id ? 'selected' : '' }}>
                                    {{ $center->cost_center_code }} - {{ $center->cost_center_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cost_center_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label" for="allocated_amount">Allocated Amount (₱) *</label>
                        <input
                            type="number"
                            id="allocated_amount"
                            name="allocated_amount"
                            class="form-control @error('allocated_amount') is-invalid @enderror"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('allocated_amount') }}"
                            required
                        >
                        @error('allocated_amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label" for="allocation_percentage">Allocation Percentage (%)</label>
                        <input
                            type="number"
                            id="allocation_percentage"
                            name="allocation_percentage"
                            class="form-control"
                            placeholder="0.00"
                            step="0.01"
                            value="{{ old('allocation_percentage', 0) }}"
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="notes">Notes</label>
                    <textarea
                        id="notes"
                        name="notes"
                        class="form-control"
                        rows="3"
                        placeholder="Allocation notes"
                    >{{ old('notes') }}</textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save"></i> Create Allocation
                    </button>
                    <a href="{{ route('accountant.expenses.allocations.index') }}" class="btn btn-secondary">
                        <i class="feather-x"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
