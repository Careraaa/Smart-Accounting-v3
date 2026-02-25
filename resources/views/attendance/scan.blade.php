<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Attendance QR Scanner" />
    <title>Scan QR • Knights Transport</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.css" />
</head>

<body class="attendance-scanner-page">

    <!-- Top Bar -->
    <header class="top-bar bg-white border-bottom shadow-sm py-2">
        <div class="container-fluid px-4 px-lg-5">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/knights_logo_icon.png') }}" alt="Knights Logo" class="logo">
                    <h4 class="mb-0 fw-semibold fs-6 text-dark">KNIGHTS TRANSPORT SERVICES CORPORATION</h4>
                </div>
                <div class="text-end">
                    <div class="fw-medium text-secondary small" id="current-date"></div>
                    <div class="fw-bold fs-4 text-dark" id="digital-clock"></div>
                </div>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container-fluid px-4 px-lg-5 py-4">
            <div class="row g-4 align-items-stretch">

                <!-- Left – Scanner & Manual Input -->
                <div class="col-12 col-lg-5">
                    <div class="card glass-card h-100 shadow-xl border-0 scanner-panel">
                        <div class="card-body p-3 p-lg-4 d-flex flex-column">
                            <h5 class="fw-semibold mb-3 text-center text-white">SCAN QR CODE</h5>
                            
                            <!-- Camera Control Buttons -->
                            <div class="mb-3" id="camera-controls">
                                <button type="button" class="btn btn-light w-100 mb-2" id="start-camera-btn" style="color: var(--primary); font-weight: 600;">
                                    <i class="fa fa-camera me-2"></i>Start Camera & Scan QR
                                </button>
                                <small class="text-white-75 d-block text-center">Tap to request camera access</small>
                            </div>

                            <!-- Video Stream -->
                            <div class="video-container mb-3" id="video-container" style="position: relative; overflow: hidden; border-radius: 12px; background: #000; width: 100%; max-width: 100%; aspect-ratio: 4/3; display: none; align-items: center; justify-content: center; flex-direction: column; margin: 0; padding: 0;">
                                <video id="camera-stream" playsinline autoplay muted webkit-playsinline style="width: 100%; height: 100%; object-fit: contain; display: block; max-width: 100%; max-height: 100%;"></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <div id="no-camera-message" class="text-white text-center" style="display: none; width: 100%; height: 100%; flex-direction: column; align-items: center; justify-content: center; position: absolute; top: 0; left: 0;">
                                    <i class="fa fa-camera-slash" style="font-size: 48px; margin-bottom: 10px;"></i>
                                    <p><strong>Camera Not Available</strong></p>
                                    <p class="small">Use manual token entry below</p>
                                </div>
                            </div>

                            <!-- Scanner Status -->
                            <div id="scanner-status" class="alert alert-info mb-3">
                                <i class="fa fa-spinner me-2" style="animation: spin 1s linear infinite;"></i>
                                <span id="status-text">Initializing camera...</span>
                            </div>

                            <!-- Scan Result -->
                            <div id="scan-result" style="display: none;" class="alert mb-3">
                                <div id="result-message"></div>
                            </div>

                            <!-- Manual Token Input -->
                            <div class="mt-3">
                                <form id="manual-form">
                                    <label class="form-label small fw-semibold text-white-75">Enter Token Manually:</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="manual-token"
                                            placeholder="8-character token" required maxlength="8" style="font-weight: 500; letter-spacing: 1px;">
                                        <button class="btn btn-light" type="submit" style="color: var(--primary); font-weight: 600;">
                                            <i class="fa fa-arrow-right me-1"></i>Submit
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right – User Info & Instructions -->
                <div class="col-12 col-lg-7">
                    <div class="card glass-card h-100 shadow-xl border-0">
                        <div class="card-body p-3 p-lg-4">
                            <!-- User Info Section -->
                            <div class="alert alert-info mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">Welcome, <strong>{{ auth()->user()->name }}</strong></h6>
                                        <p class="mb-0 text-muted">
                                            <i class="fa fa-clock me-1"></i>
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

                            <!-- Instructions -->
                            <h5 class="fw-semibold mb-4 text-dark">HOW TO LOG ATTENDANCE</h5>
                            
                            <ol class="instruction-steps list-unstyled">
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
                                    <span class="text-muted d-block mt-1 small">align your device camera with scanner on the left</span>
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
                            <div class="mt-4 p-3 bg-light rounded">
                                <p class="mb-2"><strong class="text-dark">For iPhone/iPad Users:</strong></p>
                                <small class="text-muted">
                                    If camera doesn't work, go to <strong>Settings → Privacy → Camera</strong>, enable Safari, reload the page, and try again.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    {{-- QR Scanner Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
    <script>
        let video, canvas, ctx, scanner_running = false;
        let scanFrameId = null;

        // Detect if device is iOS
        function isIOS() {
            return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
        }

        // Clock & Date
        function updateClock() {
            const now = new Date();
            document.getElementById("digital-clock").textContent = now.toLocaleTimeString([], {hour12: true});
            document.getElementById("current-date").textContent = now.toLocaleDateString('en-US', {
                weekday: 'short', month: 'short', day: 'numeric'
            });
            document.getElementById("current-time").textContent = now.toLocaleTimeString();
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Load and initialize
        window.addEventListener('load', async () => {
            console.log('Page loaded');
            localStorage.removeItem('lastAttendanceLog');
            
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                console.error('getUserMedia not supported');
                updateScannerStatus('Browser does not support camera access', 'danger');
                document.getElementById('start-camera-btn').disabled = true;
                return;
            }

            await fetchLastLog();
            
            const startCameraBtn = document.getElementById('start-camera-btn');
            if (startCameraBtn) {
                startCameraBtn.addEventListener('click', async () => {
                    console.log('User clicked "Start Camera"');
                    startCameraBtn.disabled = true;
                    startCameraBtn.innerHTML = '<i class="feather-loader me-2" style="animation: spin 1s linear infinite;"></i>Requesting permission...';
                    updateScannerStatus('Requesting camera permission...', 'info');
                    
                    await initializeCamera();
                    
                    if (!scanner_running) {
                        startCameraBtn.disabled = false;
                        startCameraBtn.innerHTML = '<i class="feather-camera me-2"></i>Start Camera & Scan';
                    }
                });
            }
        });

        async function fetchLastLog() {
            try {
                const response = await fetch("{{ route('attendance.lastlog') }}", {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
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
                console.log('No previous log found');
                document.getElementById('last-log').style.display = 'none';
            }
        }

        function displayLastLog(logData) {
            const lastLogEl = document.getElementById('last-log');
            const badgeEl = document.getElementById('last-log-type');
            const timeEl = document.getElementById('last-log-time');

            lastLogEl.style.display = 'block';
            badgeEl.textContent = logData.type.toUpperCase().replace('_', ' ');
            badgeEl.className = `badge bg-${logData.type === 'time_in' ? 'success' : 'warning'}`;
            timeEl.textContent = logData.time;
        }

        async function initializeCamera() {
            video = document.getElementById('camera-stream');
            canvas = document.getElementById('canvas');
            ctx = canvas.getContext('2d', { willReadFrequently: true });

            try {
                console.log('Requesting camera access');
                
                const constraints = {
                    video: { facingMode: 'environment' },
                    audio: false
                };

                const stream = await navigator.mediaDevices.getUserMedia(constraints);
                
                console.log('✓ Camera access granted');
                video.srcObject = stream;
                
                try {
                    await video.play();
                    console.log('✓ Video playing');
                } catch (e) {
                    console.warn('Auto-play prevented:', e.message);
                }

                video.onloadedmetadata = () => {
                    console.log('✓ Video metadata loaded');
                    
                    if (video.videoWidth === 0 || video.videoHeight === 0) {
                        console.warn('⚠️ Video dimensions are 0, retrying...');
                        setTimeout(() => {
                            if (video.videoWidth > 0) {
                                startScanning();
                            }
                        }, 1000);
                        return;
                    }
                    
                    startScanning();
                };

                setTimeout(() => {
                    if (!scanner_running) {
                        console.warn('Video did not load within 5 seconds');
                        if (video.videoWidth > 0 && video.videoHeight > 0) {
                            startScanning();
                        }
                    }
                }, 5000);

            } catch (error) {
                console.error('Camera error:', error);
                handleCameraError(error);
            }
        }

        function startScanning() {
            console.log('⏹️ Starting QR scan');
            const videoContainer = document.getElementById('video-container');
            const cameraControls = document.getElementById('camera-controls');
            const noCameraMsg = document.getElementById('no-camera-message');
            
            cameraControls.style.display = 'none';
            videoContainer.style.display = 'flex';
            document.getElementById('camera-stream').style.display = 'block';
            noCameraMsg.style.display = 'none';
            
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            scanner_running = true;
            updateScannerStatus('Camera ready - scanning...', 'info');
            scanQRCode();
        }

        function handleCameraError(error) {
            let errorMessage = 'Camera not available.';
            let instructions = 'Use manual token input below.';
            
            if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                if (isIOS()) {
                    errorMessage = 'Camera Permission Denied';
                    instructions = 'Go to Settings → Privacy → Camera and enable access.';
                } else {
                    errorMessage = 'Permission Denied';
                    instructions = 'Allow camera access in your browser settings.';
                }
            } else if (error.name === 'NotFoundError') {
                errorMessage = 'No camera found on this device.';
            } else if (error.name === 'NotReadableError') {
                errorMessage = 'Camera is already in use by another app.';
                instructions = 'Close other apps using the camera and try again.';
            } else if (error.name === 'SecurityError') {
                errorMessage = 'HTTPS is required for camera access.';
            }
            
            const videoContainer = document.getElementById('video-container');
            const cameraControls = document.getElementById('camera-controls');
            const startBtn = document.getElementById('start-camera-btn');
            
            videoContainer.style.display = 'flex';
            cameraControls.style.display = 'block';
            document.getElementById('camera-stream').style.display = 'none';
            
            const noCameraMsg = document.getElementById('no-camera-message');
            noCameraMsg.style.display = 'flex';
            noCameraMsg.innerHTML = `
                <div style="text-align: center;">
                    <i class="feather-camera-off" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                    <p><strong>${errorMessage}</strong></p>
                    <p style="font-size: 12px;">${instructions}</p>
                </div>
            `;
            
            startBtn.disabled = false;
            startBtn.innerHTML = '<i class="feather-camera me-2"></i>Start Camera & Scan';
            updateScannerStatus(errorMessage, 'danger');
        }

        function scanQRCode() {
            if (!scanner_running) {
                return;
            }

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
                    console.log('✓ QR code detected');
                    handleQRCode(code.data);
                    return;
                }
            } catch (error) {
                console.error('QR Scan error:', error);
            }

            scanFrameId = requestAnimationFrame(scanQRCode);
        }

        function handleQRCode(qrData) {
            scanner_running = false;
            if (scanFrameId) {
                cancelAnimationFrame(scanFrameId);
            }

            try {
                const url = new URL(qrData);
                const token = url.searchParams.get('token');

                if (token) {
                    submitAttendance(token);
                } else {
                    if (qrData.length === 20) {
                        submitAttendance(qrData);
                    } else {
                        updateScannerStatus('Invalid QR code format', 'danger');
                        resumeScanning();
                    }
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
                    body: JSON.stringify({ token: token })
                });

                const data = await response.json();

                if (response.ok) {
                    const logTime = new Date().toLocaleTimeString();
                    const logType = data.message.toLowerCase().includes('time in') ? 'time_in' : 'time_out';

                    showResult(data.message, 'success');
                    updateScannerStatus('✓ Attendance recorded successfully!', 'success');
                    displayLastLog({ type: logType, time: logTime });

                    setTimeout(async () => {
                        await fetchLastLog();
                        resumeScanning();
                    }, 3000);
                } else {
                    const errorMsg = data.message || 'Failed to record attendance';
                    showResult(errorMsg, 'danger');
                    updateScannerStatus('Scan failed - try again', 'danger');
                    resumeScanning();
                }
            } catch (error) {
                console.error('Submission error:', error);
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

        function updateScannerStatus(message, type = 'info') {
            const statusEl = document.getElementById('scanner-status');
            statusEl.className = `alert alert-${type} mb-3`;
            document.getElementById('status-text').textContent = message;
        }

        function showResult(message, type) {
            const resultEl = document.getElementById('scan-result');
            resultEl.className = `alert alert-${type} mb-3`;
            resultEl.style.display = 'block';
            resultEl.innerHTML = `
                <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                <strong>${message}</strong>
            `;
            
            if (type === 'danger') {
                setTimeout(() => {
                    resultEl.style.display = 'none';
                }, 5000);
            }
        }

        // Manual form submission
        document.getElementById('manual-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const tokenInput = document.getElementById('manual-token');
            const token = tokenInput.value.trim().toUpperCase();
            
            if (!token) {
                showResult('Please enter a token', 'danger');
                return;
            }
            
            if (token.length !== 8) {
                showResult('Token must be 8 characters', 'danger');
                return;
            }
            
            tokenInput.value = '';
            scanner_running = false;
            if (scanFrameId) {
                cancelAnimationFrame(scanFrameId);
            }
            
            await submitAttendance(token);
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            scanner_running = false;
            if (scanFrameId) {
                cancelAnimationFrame(scanFrameId);
            }
            if (video && video.srcObject) {
                video.srcObject.getTracks().forEach(track => track.stop());
            }
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
        :root {
            --primary: #cc3d38;   
            --primary-dark: #530a0a;
            --bg: #e6e6e6;
        }

        body.attendance-scanner-page {
            min-height: 100vh;
            background: var(--bg);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
            margin: 0;
        }

        .top-bar {
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .logo {
            width: 48px;
            height: 48px;
            object-fit: contain;
            border-radius: 10px;
        }

        .glass-card {
            background: rgb(255, 255, 255);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .scanner-panel {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 20px;
        }

        .scanner-panel .btn-primary {
            background-color: #fff;
            color: var(--primary);
            border-color: #fff;
            font-weight: 600;
        }

        .scanner-panel .btn-primary:hover {
            background-color: #f0f0f0;
            border-color: #f0f0f0;
        }

        .scanner-panel .form-control {
            background-color: rgba(255, 255, 255, 0.95);
            border-color: rgba(255, 255, 255, 0.5);
            color: #333;
        }

        .scanner-panel .form-control::placeholder {
            color: rgba(51, 51, 51, 0.6);
        }

        .scanner-panel .form-label {
            color: rgba(255, 255, 255, 0.9);
        }

        .scanner-panel h5 {
            color: white;
        }

        .text-white-75 {
            color: rgba(255, 255, 255, 0.75);
        }

        .scanner-panel .alert {
            background-color: rgba(255, 255, 255, 0.95);
            border-color: rgba(255, 255, 255, 0.3);
            color: #333;
        }

        .scanner-panel .alert-info {
            background-color: rgba(255, 255, 255, 0.95);
            border-color: rgba(255, 255, 255, 0.3);
            color: #333;
        }

        .scanner-panel .alert-success {
            background-color: rgba(76, 175, 80, 0.95);
            border-color: rgba(76, 175, 80, 0.5);
            color: white;
        }

        .scanner-panel .alert-danger {
            background-color: rgba(244, 67, 54, 0.95);
            border-color: rgba(244, 67, 54, 0.5);
            color: white;
        }

        .video-container {
            aspect-ratio: 4/3;
            max-width: 100%;
            width: 100%;
            box-sizing: border-box;
        }

        .video-container video {
            object-fit: contain;
            width: 100%;
            height: 100%;
            max-width: 100%;
            max-height: 100%;
            display: block;
        }

        .instruction-steps {
            counter-reset: step;
            padding-left: 0;
        }

        .instruction-steps li {
            counter-increment: step;
            position: relative;
            padding: 0.5rem 0.5rem 0.5rem 3.8rem;
            margin-bottom: 1rem;
            border-left: 3px solid var(--primary);
            background: rgb(247, 231, 231);
            border-radius: 0 12px 12px 0;
            transition: all 0.2s ease;
        }

        .instruction-steps li::before {
            content: counter(step);
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            width: 2rem;
            height: 2rem;
            background: var(--primary);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.95rem;
        }

        @media (max-width: 992px) {
            .instruction-steps li {
                padding: 0.9rem 0.9rem 0.9rem 3.4rem;
            }
            .instruction-steps li::before {
                width: 2.2rem;
                height: 2.2rem;
                left: -1.4rem;
                font-size: 0.9rem;
            }
        }
    </style>

</body>
</html>
