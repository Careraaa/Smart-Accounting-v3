@extends('layouts.layout')

@section('content')
<div class="col-md-10 offset-md-1">
    <div class="card">
        <div class="card-header">
            <span class="card-title mb-0">Edit Personal Information</span>
        </div>
        <div class="card-body">

            @if ($errors->any())
                <div class="alert alert-danger mb-4">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('employee.profile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')

                @include('partials.employee.personal', ['readOnly' => false, 'isEdit' => true])

                {{-- Form Actions --}}
                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('employee.profile.show') }}" class="btn btn-secondary">
                        <i class="feather-x me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="feather-save me-1"></i> Save Changes
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    // Initialize spouse name visibility on load
    document.addEventListener('DOMContentLoaded', function () {
        const civilStatusSelect = document.getElementById('civil_status');
        if (civilStatusSelect) {
            civilStatusSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
