@extends('layouts.layout')

@section('content')
    <div class="container">
        <h2>Add Statutory Deduction</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('statutory-deductions.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Min Salary</label>
                <input type="number" name="min_salary" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label>Max Salary</label>
                <input type="number" name="max_salary" class="form-control" step="0.01" required>
            </div>
            <div class="mb-3">
                <label>Employee Share</label>
                <input type="number" name="employee_share" class="form-control" step="0.01">
            </div>
            <div class="mb-3">
                <label>Employer Share</label>
                <input type="number" name="employer_share" class="form-control" step="0.01">
            </div>
            <div class="mb-3">
                <label>Percentage Employee</label>
                <input type="number" name="percentage_employee" class="form-control" step="0.01">
            </div>
            <div class="mb-3">
                <label>Percentage Employer</label>
                <input type="number" name="percentage_employer" class="form-control" step="0.01">
            </div>
            <button type="submit" class="btn btn-success">Save</button>
            <a href="{{ route('statutory-deductions.index') }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
