@extends('layouts.layout')

@section('content')
    <div class="container-fluid">

        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-semibold mb-1">Scan QR Attendance</h4>
                        <div class="fs-12 text-muted" id="current-date"></div>
                    </div>
                    <div class="fw-bold fs-4 text-dark" id="digital-clock"></div>
                </div>
            </div>
        </div>

        <div class="row g-4 align-items-stretch">

            <!-- Left - Scanner & Manual Input -->
            <div class="col-12 col-lg-5">
                <div class="card stretch stretch-full scanner-panel">
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="fw-semibold mb-3 text-center text-white">SCAN QR CODE</h5>

                        <!-- Camera Button -->
                        <div class="mb-3" id="camera-controls">
                            <button type="button" class="btn btn-light w-100 mb-2 fw-semibold" id="start-camera-btn"
                                style="color: #cc3d38;">
                                <i class="fa fa-camera me-2"></i>Start Camera & Scan QR
                            </button>
                            <small class="text-white-75 d-block text-center">Tap to request camera access</small>
                        </div>

                        <!-- Video Stream -->
                        <div id="video-container"
                            style="display:none; position:relative; overflow:hidden; border-radius:12px; background:#000; width:100%; aspect-ratio:4/3; align-items:center; justify-content:center; flex-direction:column;"
                            class="mb-3">
                            <video id="camera-stream" playsinline autoplay muted webkit-playsinline
                                style="width:100%; height:100%; object-fit:contain; display:block;"></video>
                            <canvas id="canvas" style="display:none;"></canvas>
                            <div id="no-camera-message" class="text-white text-center"
                                style="display:none; width:100%; height:100%; flex-direction:column; align-items:center; justify-content:center; position:absolute; top:0; left:0;">
                                <i class="fa fa-camera-slash" style="font-size:48px; margin-bottom:10px;"></i>
                                <p><strong>Camera Not Available</strong></p>
                                <p class="small">Use manual token entry below</p>
                            </div>
                        </div>

                        <!-- Scanner Status -->
                        <div id="scanner-status" class="scanner-alert scanner-alert-info mb-3">
                            <i class="fa fa-spinner me-2" style="animation:spin 1s linear infinite;"></i>
                            <span id="status-text">Initializing camera...</span>
                        </div>

                        <!-- Scan Result -->
                        <div id="scan-result" style="display:none;" class="scanner-alert mb-3"></div>

                        <!-- Manual Token Input -->
                        <div class="mt-auto">
                            <form id="manual-form">
                                <label class="form-label small fw-semibold text-white-75">Enter Token Manually:</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="manual-token"
                                        placeholder="8-character token" required maxlength="8"
                                        style="font-weight:500; letter-spacing:1px;">
                                    <button class="btn btn-light fw-semibold" type="submit" style="color:#cc3d38;">
                                        <i class="fa fa-arrow-right me-1"></i>Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right - User Info & Instructions -->
            <div class="col-12 col-lg-7">
                <div class="card stretch stretch-full">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Welcome, <strong>{{ auth()->user()->name }}</strong></h5>
                    </div>
                    <div class="card-body">

                        <!-- Current Time -->
                        <div class="alert alert-info mb-4">
                            <i class="feather-clock me-2"></i>
                            <span id="current-time"></span>
                        </div>

                        <!-- Last Attendance Log -->
                        <div id="last-log" style="display:none;" class="mb-4">
                            <p class="text-muted small mb-2">Last Log:</p>
                            <div class="alert alert-light border">
                                <span id="last-log-type" class="badge"></span>
                                <span class="text-muted ms-2" id="last-log-time"></span>
                            </div>
                        </div>

                        <!-- Instructions -->
                        <h6 class="fw-semibold mb-3 text-dark">HOW TO LOG ATTENDANCE</h6>
                        <ol class="instruction-steps list-unstyled mb-4">
                            <li>
                                <strong>Open Smart Accounting</strong>
                                <span class="text-muted d-block mt-1 small">on your mobile device</span>
                            </li>
                            <li>
                                <strong>Allow camera access</strong>
                                <span class="text-muted d-block mt-1 small">when prompted by the browser</span>
                            </li>
                            <li>
                                <strong>Position QR code</strong>
                                <span class="text-muted d-block mt-1 small">align your device camera with the scanner on the
                                    left</span>
                            </li>
                            <li>
                                <strong>Wait for scan</strong>
                                <span class="text-muted d-block mt-1 small">automatic detection and confirmation</span>
                            </li>
                            <li>
                                <strong>Confirmation appears</strong>
                                <span class="text-muted d-block mt-1 small">"Attendance logged" with time in/out</span>
                            </li>
                        </ol>

                        <!-- iPhone Help -->
                        <div class="p-3 bg-light rounded">
                            <p class="mb-1"><strong class="text-dark">For iPhone/iPad Users:</strong></p>
                            <small class="text-muted">
                                If camera does not work, go to <strong>Settings - Privacy - Camera</strong>, enable Safari,
                                reload the page, and try again.
                            </small>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>

    <script>
        let video, canvas, ctx, scanner_running = false;
        let scanFrameId = null;

        function isIOS() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        }

        function updateClock() {
            const now = new Date();
            document.getElementById("digital-clock").textContent = now.toLocaleTimeString([], {
                hour12: true
            });
            document.getElementById("current-date").textContent = now.toLocaleDateString('en-US', {
                weekday: 'long',
                month: 'long',
                day: 'numeric',
                year: 'numeric'
            });
            document.getElementById("current-time").textContent = now.toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();

        window.addEventListener('load', async () => {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                updateScannerStatus('Browser does not support camera access', 'danger');
                document.getElementById('start-camera-btn').disabled = true;
                return;
            }

            await fetchLastLog();

            document.getElementById('start-camera-btn').addEventListener('click', async () => {
                const btn = document.getElementById('start-camera-btn');
                btn.disabled = true;
                btn.innerHTML =
                    '<i class="fa fa-spinner me-2" style="animation:spin 1s linear infinite;"></i>Requesting permission...';
                updateScannerStatus('Requesting camera permission...', 'info');
                await initializeCamera();
                if (!scanner_running) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fa fa-camera me-2"></i>Start Camera & Scan';
                }
            });
        });

        async function fetchLastLog() {
            try {
                const response = await fetch("{{ route('attendance.lastlog') }}", {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    const data = await response.json();
                    if (data && data.type) {
                        displayLastLog(data);
                        return;
                    }
                }
                document.getElementById('last-log').style.display = 'none';
            } catch (e) {
                document.getElementById('last-log').style.display = 'none';
            }
        }

        function displayLastLog(logData) {
            document.getElementById('last-log').style.display = 'block';
            const badge = document.getElementById('last-log-type');
            badge.textContent = logData.type.toUpperCase().replace('_', ' ');
            badge.className = 'badge bg-' + (logData.type === 'time_in' ? 'success' : 'warning');
            document.getElementById('last-log-time').textContent = logData.time;
        }

        async function initializeCamera() {
            video = document.getElementById('camera-stream');
            canvas = document.getElementById('canvas');
            ctx = canvas.getContext('2d', {
                willReadFrequently: true
            });

            try {
                const stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: 'environment'
                    },
                    audio: false
                });
                video.srcObject = stream;
                try {
                    await video.play();
                } catch (e) {}

                video.onloadedmetadata = () => {
                    if (video.videoWidth === 0 || video.videoHeight === 0) {
                        setTimeout(() => {
                            if (video.videoWidth > 0) startScanning();
                        }, 1000);
                        return;
                    }
                    startScanning();
                };

                setTimeout(() => {
                    if (!scanner_running && video.videoWidth > 0) startScanning();
                }, 5000);

            } catch (error) {
                handleCameraError(error);
            }
        }

        function startScanning() {
            document.getElementById('camera-controls').style.display = 'none';
            const vc = document.getElementById('video-container');
            vc.style.display = 'flex';
            document.getElementById('camera-stream').style.display = 'block';
            document.getElementById('no-camera-message').style.display = 'none';
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            scanner_running = true;
            updateScannerStatus('Camera ready - scanning...', 'info');
            scanQRCode();
        }

        function handleCameraError(error) {
            let msg = 'Camera not available.',
                hint = 'Use manual token input below.';
            if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                msg = isIOS() ? 'Camera Permission Denied' : 'Permission Denied';
                hint = isIOS() ? 'Go to Settings - Privacy - Camera and enable access.' :
                    'Allow camera access in your browser settings.';
            } else if (error.name === 'NotFoundError') {
                msg = 'No camera found on this device.';
            } else if (error.name === 'NotReadableError') {
                msg = 'Camera is already in use by another app.';
                hint = 'Close other apps using the camera and try again.';
            } else if (error.name === 'SecurityError') {
                msg = 'HTTPS is required for camera access.';
            }

            document.getElementById('video-container').style.display = 'flex';
            document.getElementById('camera-controls').style.display = 'block';
            document.getElementById('camera-stream').style.display = 'none';
            const noCam = document.getElementById('no-camera-message');
            noCam.style.display = 'flex';
            noCam.innerHTML =
                '<div style="text-align:center;"><i class="fa fa-camera-slash" style="font-size:48px; margin-bottom:10px; display:block;"></i><p><strong>' +
                msg + '</strong></p><p style="font-size:12px;">' + hint + '</p></div>';
            const btn = document.getElementById('start-camera-btn');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-camera me-2"></i>Start Camera & Scan';
            updateScannerStatus(msg, 'danger');
        }

        function scanQRCode() {
            if (!scanner_running) return;
            if (video.readyState !== video.HAVE_ENOUGH_DATA) {
                scanFrameId = requestAnimationFrame(scanQRCode);
                return;
            }
            try {
                if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                }
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                const code = jsQR(imageData.data, imageData.width, imageData.height);
                if (code) {
                    handleQRCode(code.data);
                    return;
                }
            } catch (e) {}
            scanFrameId = requestAnimationFrame(scanQRCode);
        }

        function handleQRCode(qrData) {
            scanner_running = false;
            if (scanFrameId) cancelAnimationFrame(scanFrameId);
            try {
                const url = new URL(qrData);
                const token = url.searchParams.get('token');
                if (token) {
                    submitAttendance(token);
                } else if (qrData.length === 20) {
                    submitAttendance(qrData);
                } else {
                    updateScannerStatus('Invalid QR code format', 'danger');
                    resumeScanning();
                }
            } catch (e) {
                if (qrData && qrData.length > 0) {
                    submitAttendance(qrData);
                } else {
                    updateScannerStatus('Invalid QR code', 'danger');
                    resumeScanning();
                }
            }
        }

        async function submitAttendance(token) {
            try {
                updateScannerStatus('Submitting attendance...', 'info');
                const response = await fetch("{{ route('hr.qr.submit') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        token
                    })
                });
                const data = await response.json();
                if (response.ok) {
                    const logType = data.message.toLowerCase().includes('time in') ? 'time_in' : 'time_out';
                    showResult(data.message, 'success');
                    updateScannerStatus('Attendance recorded successfully!', 'success');
                    displayLastLog({
                        type: logType,
                        time: new Date().toLocaleTimeString()
                    });
                    setTimeout(async () => {
                        await fetchLastLog();
                        resumeScanning();
                    }, 3000);
                } else {
                    showResult(data.message || 'Failed to record attendance', 'danger');
                    updateScannerStatus('Scan failed - try again', 'danger');
                    resumeScanning();
                }
            } catch (error) {
                showResult('Network error. Please check your connection.', 'danger');
                updateScannerStatus('Error - try again', 'danger');
                resumeScanning();
            }
        }

        function resumeScanning() {
            scanner_running = true;
            updateScannerStatus('Camera ready - scanning...', 'info');
            scanQRCode();
        }

        function updateScannerStatus(message, type) {
            type = type || 'info';
            const el = document.getElementById('scanner-status');
            el.className = 'scanner-alert scanner-alert-' + type + ' mb-3';
            document.getElementById('status-text').textContent = message;
        }

        function showResult(message, type) {
            const el = document.getElementById('scan-result');
            el.className = 'scanner-alert scanner-alert-' + type + ' mb-3';
            el.style.display = 'block';
            el.innerHTML = '<i class="fa fa-' + (type === 'success' ? 'check-circle' : 'exclamation-circle') +
                ' me-2"></i><strong>' + message + '</strong>';
            if (type === 'danger') setTimeout(function() {
                el.style.display = 'none';
            }, 5000);
        }

        document.getElementById('manual-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            const input = document.getElementById('manual-token');
            const token = input.value.trim().toUpperCase();
            if (!token) {
                showResult('Please enter a token', 'danger');
                return;
            }
            if (token.length !== 8) {
                showResult('Token must be 8 characters', 'danger');
                return;
            }
            input.value = '';
            scanner_running = false;
            if (scanFrameId) cancelAnimationFrame(scanFrameId);
            await submitAttendance(token);
        });

        window.addEventListener('beforeunload', function() {
            scanner_running = false;
            if (scanFrameId) cancelAnimationFrame(scanFrameId);
            if (video && video.srcObject) video.srcObject.getTracks().forEach(function(t) {
                t.stop();
            });
        });
    </script>

    <style>
        .scanner-panel {
            background: linear-gradient(135deg, #cc3d38 0%, #530a0a 100%);
            border: none !important;
        }

        .scanner-panel .form-control {
            background: rgba(255, 255, 255, 0.95);
            border-color: rgba(255, 255, 255, 0.5);
            color: #333;
        }

        .scanner-panel .form-control::placeholder {
            color: rgba(51, 51, 51, 0.6);
        }

        .scanner-panel .form-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .text-white-75 {
            color: rgba(255, 255, 255, 0.75);
        }

        .scanner-alert {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .scanner-alert-info {
            background: rgba(255, 255, 255, 0.95);
            color: #333;
        }

        .scanner-alert-success {
            background: rgba(76, 175, 80, 0.95);
            color: #fff;
        }

        .scanner-alert-danger {
            background: rgba(244, 67, 54, 0.95);
            color: #fff;
        }

        .instruction-steps {
            counter-reset: step;
            padding-left: 0;
        }

        .instruction-steps li {
            counter-increment: step;
            position: relative;
            padding: 0.6rem 0.75rem 0.6rem 3.8rem;
            margin-bottom: 0.85rem;
            border-left: 3px solid #cc3d38;
            background: #fdf2f2;
            border-radius: 0 12px 12px 0;
        }

        .instruction-steps li::before {
            content: counter(step);
            position: absolute;
            left: 0.85rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            background: #cc3d38;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.9rem;
        }

        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection
