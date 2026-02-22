@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card text-center">
                <div class="card-header">
                    <h5 class="card-title mb-0">Scan to Record Attendance</h5>
                </div>

                <div class="card-body">
                    <p class="text-muted">
                        QR code refreshes every <strong>50 seconds</strong>
                    </p>

                    <div id="qrcode" class="d-flex justify-content-center my-4"></div>

                    <span class="badge bg-success">ACTIVE</span>
                </div>

                <div class="card-footer text-muted">
                    Display this screen for employees to scan
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR SCRIPT --}}
<script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>
<script>
function loadQR() {
    fetch("{{ route('hr.qr.generate') }}", {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        const qrDiv = document.getElementById('qrcode');
        qrDiv.innerHTML = '';
        new QRCode(qrDiv, {
            text: data.token,
            width: 220,
            height: 220
        });
    })
    .catch(() => {
        document.getElementById('qrcode').innerHTML =
            '<p class="text-danger">Failed to load QR</p>';
    });
}

loadQR();
setInterval(loadQR, 50000);
</script>
@endsection
