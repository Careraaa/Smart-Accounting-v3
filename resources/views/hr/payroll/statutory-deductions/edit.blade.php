@extends('layouts.layout')

@section('content')
    <div class="container">
        <h2>Edit Statutory Deduction</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('statutory-deductions.update', $deduction->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" value="{{ $deduction->name }}" required>
            </div>
            <div class="mb-3">
                <label>Min Salary</label>
                <input type="number" name="min_salary" class="form-control" step="0.01"
                    value="{{ $deduction->min_salary }}" required>
            </div>
            <div class="mb-3">
                <label>Max Salary</label>
                <input type="number" name="max_salary" class="form-control" step="0.01"
                    value="{{ $deduction->max_salary }}" required>
            </div>
            <div class="mb-3">
                <label>Employee Share</label>
                <input type="number" name="employee_share" class="form-control" step="0.01"
                    value="{{ $deduction->employee_share }}">
            </div>
            <div class="mb-3">
                <label>Employer Share</label>
                <input type="number" name="employer_share" class="form-control" step="0.01"
                    value="{{ $deduction->employer_share }}">
            </div>
            <div class="mb-3">
                <label>Percentage Employee</label>
                <input type="number" name="percentage_employee" class="form-control" step="0.01"
                    value="{{ $deduction->percentage_employee }}">
            </div>
            <div class="mb-3">
                <label>Percentage Employer</label>
                <input type="number" name="percentage_employer" class="form-control" step="0.01"
                    value="{{ $deduction->percentage_employer }}">
            </div>
            <button type="submit" class="btn btn-success">Update</button>
            <a href="{{ route('statutory-deductions.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
