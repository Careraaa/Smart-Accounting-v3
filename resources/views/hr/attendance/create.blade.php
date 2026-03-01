@extends('layouts.layout')

@section('content')
<div class="col-md-8 offset-md-2">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Record Attendance</span>
        </div>
        <div class="card-body">
            <form action="{{ route('attendance.store') }}" method="POST">
                @csrf

                {{-- Employee --}}
                <div class="mb-4">
                    <label for="user_id" class="form-label">Employee <span class="text-danger">*</span></label>
                    <select name="user_id" id="user_id" class="form-control @error('user_id') is-invalid @enderror" required>
                        <option value="">Select an employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('user_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Date --}}
                <div class="mb-4">
                    <label for="date" class="form-label">Date <span class="text-danger">*</span></label>
                    <input type="date" name="date" id="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', today()->toDateString()) }}" required>
                    @error('date')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Time In/Out --}}
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="time_in" class="form-label">Time In</label>
                        <input type="time" name="time_in" id="time_in" class="form-control @error('time_in') is-invalid @enderror" value="{{ old('time_in') }}">
                        @error('time_in')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                    <div class="col-md-6 mb-4">
                        <label for="time_out" class="form-label">Time Out</label>
                        <input type="time" name="time_out" id="time_out" class="form-control @error('time_out') is-invalid @enderror" value="{{ old('time_out') }}">
                        @error('time_out')<span class="invalid-feedback">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Status --}}
                <div class="mb-4">
                    <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                        <option value="">— Select Status —</option>
                        <option value="present" @selected(old('status') == 'present')>Present</option>
                        <option value="absent" @selected(old('status') == 'absent')>Absent</option>
                        <option value="late" @selected(old('status') == 'late')>Late</option>
                        <option value="early_leave" @selected(old('status') == 'early_leave')>Early Leave</option>
                    </select>
                    @error('status')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 pt-3 border-top">
                    <button type="submit" class="btn btn-primary btn-sm">Save Attendance</button>
                    <a href="{{ route('attendance.index') }}" class="btn btn-secondary btn-sm">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.prl-hint { font-size: 0.8rem; color: #9898a8; }
</style>
@endsection
