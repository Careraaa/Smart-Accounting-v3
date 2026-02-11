@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Drivers List</h5>
                        <a href="{{ route('drivers.create') }}" class="btn btn-outline-primary border-1 rounded">Add
                            Driver</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover w-100" style="table-layout: fixed;">
                                <thead>
                                    <tr>
                                        <th style="width: 30%; text-align: left;">Name</th>
                                        <th style="width: 30%; text-align: left;">Contact</th>
                                        <th style="width: 20%; text-align: center;">Status</th>
                                        <th style="width: 20%; text-align: center;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $statusStyles = [
                                            'active' => 'bg-soft-success text-success',
                                            'inactive' => 'bg-soft-danger text-danger',
                                            'pending' => 'bg-soft-warning text-warning',
                                        ];
                                    @endphp

                                    @forelse($drivers as $driver)
                                        <tr>
                                            <td style="text-align: left;">{{ $driver->name }}</td>
                                            <td style="text-align: left;">{{ $driver->contact_number }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge px-3 {{ $statusStyles[$driver->status] ?? 'bg-secondary text-white' }}">
                                                    {{ ucfirst($driver->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('drivers.show', $driver) }}"
                                                        class="btn btn-outline-info btn-sm border-1 rounded" title="View">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('drivers.edit', $driver) }}"
                                                        class="btn btn-outline-warning btn-sm border-1 rounded"
                                                        title="Edit">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('drivers.destroy', $driver) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm border-1 rounded"
                                                            onclick="return confirm('Are you sure you want to delete this driver?')"
                                                            title="Delete">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No drivers found</td>
                                        </tr>
                                    @endforelse

                                    <!-- Invisible spacer row to fully show bottom button outlines -->
                                    <tr style="height: 8px;">
                                        <td colspan="4"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
