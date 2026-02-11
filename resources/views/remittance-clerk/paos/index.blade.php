@extends('layouts.layout')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">PAO / Conductors List</h5>
                        <a href="{{ route('paos.create') }}" class="btn btn-outline-primary border-1 rounded">Add PAO</a>
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

                                    @forelse($paos as $pao)
                                        <tr>
                                            <td style="text-align: left;">{{ $pao->name }}</td>
                                            <td style="text-align: left;">{{ $pao->contact_number }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge px-3 {{ $statusStyles[$pao->status] ?? 'bg-secondary text-white' }}">
                                                    {{ ucfirst($pao->status) }}
                                                </span>
                                            </td>
                                            <td class="text-center align-middle">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="{{ route('paos.show', $pao) }}"
                                                        class="btn btn-outline-info btn-sm border-1 rounded" title="View">
                                                        <i class="feather-eye"></i>
                                                    </a>
                                                    <a href="{{ route('paos.edit', $pao) }}"
                                                        class="btn btn-outline-warning btn-sm border-1 rounded"
                                                        title="Edit">
                                                        <i class="feather-edit"></i>
                                                    </a>
                                                    <form action="{{ route('paos.destroy', $pao) }}" method="POST"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-outline-danger btn-sm border-1 rounded"
                                                            onclick="return confirm('Are you sure you want to delete this PAO?')"
                                                            title="Delete">
                                                            <i class="feather-trash-2"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted">No PAOs found</td>
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
