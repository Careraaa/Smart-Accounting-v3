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

                    <!-- Camera Control Buttons -->
                    <div class="mb-4" id="camera-controls">
                        <button type="button" class="btn btn-primary w-100 mb-2" id="start-camera-btn">
                            <i class="feather-camera me-2"></i>Start Camera & Scan QR
                        </button>
                        <small class="text-muted d-block">Tap to request camera access</small>
                    </div>

                    <!-- Video Stream -->
                    <div class="video-container mb-4" id="video-container" style="position: relative; overflow: hidden; border-radius: 8px; background: #000; min-height: 300px; display: none; align-items: center; justify-content: center;">
                        <video id="camera-stream" playsinline autoplay muted webkit-playsinline style="width: 100%; height: auto; max-height: 400px;"></video>
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

                    <!-- Debug Info -->
                    <div class="alert alert-secondary mb-3" role="alert" style="font-size: 12px; display: none;" id="debug-info">
                        <strong>Debug Info:</strong>
                        <br><span id="debug-text"></span>
                        <br><button class="btn btn-sm btn-outline-secondary mt-2" type="button" id="test-camera-btn">Test Camera Access</button>
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
                    <strong>General Steps:</strong>
                    <ol class="mb-3">
                        <li>Allow camera access when prompted</li>
                        <li>Position the QR code in the camera view</li>
                        <li>Wait for automatic scan and confirmation</li>
                        <li>Your attendance will be recorded (Time IN or OUT)</li>
                    </ol>

                    <strong>For iPhone/iPad Users:</strong>
                    <ol class="mb-3">
                        <li>If camera doesn't work, go to <strong>Settings → Privacy → Camera</strong></li>
                        <li>Make sure <strong>Safari</strong> (or your browser) has camera permission enabled</li>
                        <li>Reload this page after enabling permission</li>
                        <li>Allow camera access when prompted</li>
                        <li>If still not working, try entering the QR token manually</li>
                    </ol>

                    <strong>If Camera Still Doesn't Work:</strong>
                    <ul class="mb-0">
                        <li>Click "Test Camera Access" button (debug section) to check permission status</li>
                        <li>Use "Or enter token manually" option to submit attendance</li>
                        <li>Ask your administrator for a QR token</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- QR Scanner Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/jsqr/dist/jsQR.js"></script>
<script>
    let video, canvas, ctx, scanner_running = false;
    let scanFrameId = null;

    // Detect if device is iOS
    function isIOS() {
        return /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    }

    // Load last attendance log and initialize
    window.addEventListener('load', async () => {
        console.log('Page loaded');
        console.log('Device is iOS:', isIOS());
        console.log('User Agent:', navigator.userAgent);
        
        // Check if jsQR is available
        if (typeof jsQR === 'undefined') {
            console.error('jsQR library not loaded');
            showDebugInfo('ERROR: QR scanning library not loaded');
        } else {
            console.log('✓ jsQR library available');
        }
        
        // Check if mediaDevices API is available
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.error('getUserMedia not supported');
            showDebugInfo('ERROR: Camera API not supported in this browser');
            updateScannerStatus('Browser does not support camera access', 'danger');
            document.getElementById('start-camera-btn').disabled = true;
            return;
        }
        
        console.log('Camera API available');
        showDebugInfo('Device: ' + (isIOS() ? 'iPhone/iPad' : 'Other') + ' | Camera API: Available | jsQR: Ready');

        // Update current time
        updateTime();
        setInterval(updateTime, 1000);

        // Fetch last log from server
        await fetchLastLog();

        // Add click handler to Start Camera button
        const startCameraBtn = document.getElementById('start-camera-btn');
        if (startCameraBtn) {
            startCameraBtn.addEventListener('click', async () => {
                console.log('User clicked "Start Camera"');
                startCameraBtn.disabled = true;
                startCameraBtn.innerHTML = '<i class="feather-loader me-2" style="animation: spin 1s linear infinite;"></i>Requesting permission...';
                updateScannerStatus('Requesting camera permission...', 'info');
                
                await initializeCamera();
                
                // Re-enable button if camera initialization fails
                if (!scanner_running) {
                    startCameraBtn.disabled = false;
                    startCameraBtn.innerHTML = '<i class="feather-camera me-2"></i>Start Camera & Scan QR';
                }
            });
        }
    });

    function updateTime() {
        const now = new Date();
        document.getElementById('current-time').textContent = now.toLocaleTimeString();
    }

    async function fetchLastLog() {
        try {
            const response = await fetch("{{ route('attendance.lastlog') }}", {
                method: 'GET',
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

            // Fallback to localStorage
            const lastLog = localStorage.getItem('lastAttendanceLog');
            if (lastLog) {
                try {
                    const logData = JSON.parse(lastLog);
                    displayLastLog(logData);
                } catch (e) {
                    console.log('Failed to parse localStorage lastAttendanceLog');
                }
            }
        } catch (e) {
            console.log('No previous log found');
            // Fallback to localStorage
            const lastLog = localStorage.getItem('lastAttendanceLog');
            if (lastLog) {
                try {
                    const logData = JSON.parse(lastLog);
                    displayLastLog(logData);
                } catch (e) {
                    console.log('Failed to parse localStorage');
                }
            }
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

    function showDebugInfo(message) {
        const debugEl = document.getElementById('debug-info');
        const txtEl = document.getElementById('debug-text');
        debugEl.style.display = 'block';
        txtEl.textContent = message;
        console.log('[DEBUG]', message);
    }

    async function initializeCamera() {
        video = document.getElementById('camera-stream');
        canvas = document.getElementById('canvas');
        ctx = canvas.getContext('2d', { willReadFrequently: true });

        try {
            showDebugInfo('Requesting camera permission...');
            console.log('Requesting camera access');
            
            // Ultra-permissive constraints for maximum compatibility
            const constraints = {
                video: {
                    facingMode: 'environment'
                },
                audio: false
            };

            console.log('Camera constraints:', JSON.stringify(constraints));
            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            
            console.log('✓ Camera access granted, stream active:', stream.active);
            showDebugInfo('✓ Camera access granted, setting up video...');
            video.srcObject = stream;
            
            // Force play for iOS
            try {
                await video.play();
                console.log('✓ Video playing');
            } catch (e) {
                console.warn('Auto-play prevented:', e.message);
            }

            // Check if video element is ready
            video.onloadedmetadata = () => {
                console.log('✓ Video metadata loaded', {
                    width: video.videoWidth,
                    height: video.videoHeight,
                    readyState: video.readyState
                });
                
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

            // Set timeout in case video doesn't load
            setTimeout(() => {
                if (!scanner_running) {
                    console.warn('Video did not load within 5 seconds, checking stream...');
                    if (video.videoWidth > 0 && video.videoHeight > 0) {
                        console.log('Video has dimensions now, starting scan');
                        startScanning();
                    } else {
                        showDebugInfo('⚠️ Video stream not responding. Check browser console.');
                    }
                }
            }, 5000);

        } catch (error) {
            console.error('❌ Camera error:', {
                name: error.name,
                message: error.message,
                code: error.code
            });
            
            handleCameraError(error);
        }
    }

    function startScanning() {
        console.log('⏹️ Starting QR scan');
        const videoContainer = document.getElementById('video-container');
        const cameraControls = document.getElementById('camera-controls');
        const startBtn = document.getElementById('start-camera-btn');
        
        // Hide button, show video container
        cameraControls.style.display = 'none';
        videoContainer.style.display = 'flex';
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        scanner_running = true;
        updateScannerStatus('Camera ready - scanning...', 'info');
        scanQRCode();
    }

    function handleCameraError(error) {
        showDebugInfo(`❌ Error: ${error.name} - ${error.message}`);
        
        // Show detailed error message
        let errorMessage = 'Camera not available.';
        let instructions = 'Use manual token input below.';
        
        if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
            if (isIOS()) {
                errorMessage = '❌ Camera Permission Denied';
                instructions = '<strong>iOS Fix:</strong><br>1. Go to iPhone Settings<br>2. Scroll down and find Safari<br>3. Tap Camera → Toggle ON<br>4. Reload page and try again';
            } else {
                errorMessage = '❌ Permission Denied';
                instructions = '1. Tap the camera icon in your address bar<br>2. Select "Allow" for camera access<br>3. Reload the page and try again';
            }
        } else if (error.name === 'NotFoundError') {
            errorMessage = '❌ No camera found on this device.';
        } else if (error.name === 'NotReadableError') {
            errorMessage = '❌ Camera is already in use by another app.';
            instructions = 'Close other apps using the camera and try again.';
        } else if (error.name === 'SecurityError') {
            errorMessage = '❌ HTTPS is required for camera access.';
            instructions = 'Use a secure connection (HTTPS) to access the camera.';
        } else if (error.name === 'TypeError') {
            errorMessage = '❌ Camera API error. Browser may not support getUserMedia.';
        }
        
        // Show error in UI, allow user to try again
        const videoContainer = document.getElementById('video-container');
        const cameraControls = document.getElementById('camera-controls');
        const startBtn = document.getElementById('start-camera-btn');
        
        videoContainer.style.display = 'flex';
        cameraControls.style.display = 'block';
        document.getElementById('camera-stream').style.display = 'none';
        
        const noCameraMsg = document.getElementById('no-camera-message');
        noCameraMsg.style.display = 'flex';
        noCameraMsg.innerHTML = `
            <div style="text-align: center; padding: 20px;">
                <i class="feather-camera-off" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                <p><strong>${errorMessage}</strong></p>
                <p class="small">${instructions}</p>
            </div>
        `;
        
        startBtn.disabled = false;
        startBtn.innerHTML = '<i class="feather-camera me-2"></i>Start Camera & Scan QR';
        updateScannerStatus(errorMessage, 'danger');
    }

    function scanQRCode() {
        if (!scanner_running) {
            return;
        }

        // Check if video is ready
        if (video.readyState !== video.HAVE_ENOUGH_DATA) {
            scanFrameId = requestAnimationFrame(scanQRCode);
            return;
        }

        try {
            // Ensure canvas dimensions match video
            if (canvas.width !== video.videoWidth || canvas.height !== video.videoHeight) {
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
            }
            
            // Draw current video frame to canvas
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            
            // Scan for QR code
            const code = jsQR(imageData.data, imageData.width, imageData.height);

            if (code) {
                console.log('✓ QR code detected:', code.data.substring(0, 50) + '...');
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
            // Try to parse as URL
            const url = new URL(qrData);
            const token = url.searchParams.get('token');

            if (token) {
                submitAttendance(token);
            } else {
                // If it's not a URL, maybe it's just the token
                if (qrData.length === 20) {
                    submitAttendance(qrData);
                } else {
                    updateScannerStatus('Invalid QR code format', 'danger');
                    resumeScanning();
                }
            }
        } catch (e) {
            // Not a URL, try as direct token
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

                // Store in localStorage
                localStorage.setItem('lastAttendanceLog', JSON.stringify({
                    type: logType,
                    time: logTime
                }));

                showResult(data.message, 'success');
                updateScannerStatus('✓ Attendance recorded successfully!', 'success');
                displayLastLog({ type: logType, time: logTime });

                // Resume scanning after delay
                setTimeout(() => {
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
        
        // Auto-hide error messages after 5 seconds
        if (type === 'danger') {
            setTimeout(() => {
                resultEl.style.display = 'none';
            }, 5000);
        }
    }

    // Test camera button
    const testCameraBtn = document.getElementById('test-camera-btn');
    if (testCameraBtn) {
        testCameraBtn.addEventListener('click', async () => {
            showDebugInfo('Testing camera access...');
            testCameraBtn.disabled = true;
            testCameraBtn.textContent = 'Testing...';

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: true, 
                    audio: false 
                });
                
                showDebugInfo('✓ Test successful! Camera is accessible.');
                // Stop the test stream
                stream.getTracks().forEach(track => track.stop());
                testCameraBtn.textContent = '✓ Camera Test Passed';
                testCameraBtn.className = 'btn btn-sm btn-success mt-2';
            } catch (err) {
                showDebugInfo(`✗ Test failed: ${err.name} - ${err.message}`);
                testCameraBtn.textContent = '✗ Camera Test Failed';
                testCameraBtn.className = 'btn btn-sm btn-danger mt-2';
            }
        });
    }

    // Manual form submission
    document.getElementById('manual-form').addEventListener('submit', async (e) => {
        e.preventDefault();
        const tokenInput = document.getElementById('manual-token');
        const token = tokenInput.value.trim();
        
        if (!token) {
            showResult('Please enter a token', 'danger');
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
