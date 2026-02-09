@extends('layouts.layout')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-12">
            <div class="card shadow-lg">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h4 class="mb-0">
                        <i class="feather-camera me-2"></i>Attendance QR Scanner
                    </h4>
                </div>

                <div class="card-body p-4">
                    <!-- User Info Section -->
                    <div class="alert alert-info mb-4" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">Welcome, <strong>{{ auth()->user()->name }}</strong></h6>
                                <p class="mb-0 text-muted">
                                    <i class="feather-clock me-1"></i>
                                    <span id="current-time"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Last Attendance Log -->
                    <div class="mb-4" id="last-log" style="display: none;">
                        <p class="text-muted small mb-2">Last Log:</p>
                        <div class="alert alert-light border" role="alert">
                            <span id="last-log-type" class="badge"></span>
                            <span class="text-muted ms-2" id="last-log-time"></span>
                        </div>
                    </div>

                    <!-- Video Stream -->
                    <div class="video-container mb-4" style="position: relative; overflow: hidden; border-radius: 8px; background: #000; min-height: 300px; display: flex; align-items: center; justify-content: center;">
                        <video id="camera-stream" style="width: 100%; height: auto; max-height: 400px;" playsinline></video>
                        <canvas id="canvas" style="display: none;"></canvas>
                        <div id="no-camera-message" class="text-white text-center" style="display: none;">
                            <i class="feather-camera-off" style="font-size: 48px; margin-bottom: 10px;"></i>
                            <p>Camera Not Available</p>
                            <p class="small">Allow camera access or use a device with a camera</p>
                        </div>
                    </div>

                    <!-- Scanner Status -->
                    <div id="scanner-status" class="alert alert-info mb-4">
                        <i class="feather-loader me-2" style="animation: spin 1s linear infinite;"></i>
                        <span id="status-text">Initializing camera...</span>
                    </div>

                    <!-- Scan Result -->
                    <div id="scan-result" style="display: none;" class="alert mb-4">
                        <div id="result-message"></div>
                    </div>

                    <!-- Manual Token Input (Fallback) -->
                    <div class="mt-4">
                        <button class="btn btn-link btn-sm w-100" type="button" data-bs-toggle="collapse"
                            data-bs-target="#manual-input" aria-expanded="false" aria-controls="manual-input">
                            <i class="feather-edit-2 me-1"></i>Or enter token manually
                        </button>
                        <div class="collapse mt-3" id="manual-input">
                            <form id="manual-form">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="manual-token"
                                        placeholder="Paste or enter QR token here..." required>
                                    <button class="btn btn-primary" type="submit">
                                        <i class="feather-send me-1"></i>Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light text-muted text-center py-3">
                    <small>Position the QR code in front of your camera</small>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="feather-help-circle me-2"></i>How to Use
                    </h6>
                </div>
                <div class="card-body small text-muted">
                    <ol class="mb-0">
                        <li>Allow camera access when prompted</li>
                        <li>Position the QR code in the camera view</li>
                        <li>Wait for automatic scan and confirmation</li>
                        <li>Your attendance will be recorded (Time IN or OUT)</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Scanner Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script>
    // Load last attendance log
    window.addEventListener('load', async () => {
        const user = {
            id: {{ auth()->user()->id }},
            name: '{{ auth()->user()->name }}'
        };

        // Update current time
        function updateTime() {
            const now = new Date();
            document.getElementById('current-time').textContent = now.toLocaleTimeString();
        }
        updateTime();
        setInterval(updateTime, 1000);

        // Fetch last log
        await fetchLastLog();

        // Initialize camera scanner
        initializeCamera();
    });

    async function fetchLastLog() {
        try {
            // This would need an endpoint to fetch last log - for now we'll show it from storage
            const lastLog = localStorage.getItem('lastAttendanceLog');
            if (lastLog) {
                const logData = JSON.parse(lastLog);
                document.getElementById('last-log').style.display = 'block';
                document.getElementById('last-log-type').textContent = logData.type.toUpperCase().replace('_', ' ');
                document.getElementById('last-log-type').className = `badge bg-${logData.type === 'time_in' ? 'success' : 'warning'}`;
                document.getElementById('last-log-time').textContent = logData.time;
            }
        } catch (e) {
            console.log('No previous log');
        }
    }

    let video, canvas, ctx, scanner_running;

    async function initializeCamera() {
        video = document.getElementById('camera-stream');
        canvas = document.getElementById('canvas');
        ctx = canvas.getContext('2d', { willReadFrequently: true });

        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' }
            });

            video.srcObject = stream;
            video.addEventListener('loadedmetadata', () => {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                scanner_running = true;
                updateScannerStatus('Camera ready - scanning...', 'info');
                scanQRCode();
            });
        } catch (error) {
            console.error('Camera access denied:', error);
            document.getElementById('camera-stream').style.display = 'none';
            document.getElementById('no-camera-message').style.display = 'flex';
            updateScannerStatus('Camera access denied. Use manual input below.', 'danger');
        }
    }

    function scanQRCode() {
        if (!scanner_running) return;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
        const code = jsQR(imageData.data, imageData.width, imageData.height, {
            inversionAttempts: 'dontInvert',
        });

        if (code) {
            handleQRCode(code.data);
            return; // Stop scanning after successful read
        }

        requestAnimationFrame(scanQRCode);
    }

    function handleQRCode(qrData) {
        scanner_running = false;

        // Extract token from URL
        const url = new URL(qrData);
        const token = url.searchParams.get('token');

        if (token) {
            submitAttendance(token);
        } else {
            updateScannerStatus('Invalid QR code format', 'danger');
            scanner_running = true;
            scanQRCode();
        }
    }

    async function submitAttendance(token) {
        try {
            updateScannerStatus('Submitting attendance...', 'info');

            const response = await fetch("{{ route('hr.qr.submit') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ token: token })
            });

            const data = await response.json();

            if (response.ok) {
                // Store log in localStorage
                const logTime = new Date().toLocaleTimeString();
                const logType = data.message.includes('Time IN') ? 'time_in' : 'time_out';
                localStorage.setItem('lastAttendanceLog', JSON.stringify({
                    type: logType,
                    time: logTime
                }));

                showResult(data.message, 'success');
                updateScannerStatus('Scan successful! Refreshing...', 'success');

                // Refresh last log display
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showResult(data.message || 'Failed to record attendance', 'danger');
                updateScannerStatus('Scan failed. Try again.', 'danger');
                scanner_running = true;
                scanQRCode();
            }
        } catch (error) {
            console.error('Error:', error);
            showResult('Network error. Please try again.', 'danger');
            updateScannerStatus('Error occurred', 'danger');
            scanner_running = true;
            scanQRCode();
        }
    }

    function updateScannerStatus(message, type = 'info') {
        const statusEl = document.getElementById('scanner-status');
        statusEl.className = `alert alert-${type} mb-4`;
        document.getElementById('status-text').textContent = message;
    }

    function showResult(message, type) {
        const resultEl = document.getElementById('scan-result');
        resultEl.className = `alert alert-${type} mb-4`;
        resultEl.style.display = 'block';
        resultEl.innerHTML = `
            <i class="feather-${type === 'success' ? 'check-circle' : 'alert-circle'} me-2"></i>
            <strong>${message}</strong>
        `;
    }

    // Manual form submission
    document.getElementById('manual-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const token = document.getElementById('manual-token').value;
        document.getElementById('manual-token').value = '';
        await submitAttendance(token);
    });

    // Add spinning animation
    const style = document.createElement('style');
    style.textContent = `
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
    `;
    document.head.appendChild(style);
</script>

<style>
    .video-container {
        aspect-ratio: 4/3;
    }

    @media (max-width: 576px) {
        .video-container {
            min-height: 250px;
        }
    }

    #camera-stream {
        display: block;
    }
</style>
@endsection
