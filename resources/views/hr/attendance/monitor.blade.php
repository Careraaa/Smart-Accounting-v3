<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Attendance Monitoring" />
    <meta name="keyword" content="" />
    <meta name="author" content="flexilecode" />
    <title>Attendance Monitoring - Smart Accounting</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/bootstrap.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('vendors/css/vendors.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/theme.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/overrides.css') }}">
</head>

<body class="attendance-monitor-page">

    <!-- Top Bar with Logo, Title, and Time -->
    <div class="attendance-topbar bg-white shadow-sm p-3 mb-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/knights_logo_icon.png') }}" alt="Logo" class="attendance-logo">
                <h4 class="mb-0 fw-bold text-dark">KNIGHTS TRANSPORT SERVICES CORPORATION</h4>
            </div>
            <div class="text-end">
                <div class="fw-bold text-dark" id="current-date"></div>
                <div class="fw-bold fs-5" id="digital-clock"></div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-5 px-lg-5 py-4 attendance-bg">
        <div class="row align-items-stretch g-4 attendance-row">

            <!-- LEFT COLUMN: Instructions -->
            <div class="col-12 col-lg-8 d-flex">
                <div class="card shadow-lg attendance-card d-flex flex-column flex-fill">

                    <!-- Main Content -->
                    <div class="card-body d-flex flex-column justify-content-center flex-grow-1 p-5">
                        <div class="instructions-container text-start px-4 py-3">
                            <h5 class="mb-4 fw-bold text-dark">Scan this code in the website on your phone</h5>
                            <ol class="instruction-list">
                                <li class="mb-3">
                                    <strong>Open the Smart Accounting website</strong>
                                    <small class="d-block text-muted mt-1">on your mobile device</small>
                                </li>
                                <li class="mb-3">
                                    <strong>Sign in with your registered email</strong>
                                    <small class="d-block text-muted mt-1">If prompted, enter the OTP sent to your email</small>
                                </li>
                                <li class="mb-3">
                                    <strong>Navigate to the Scan QR section</strong>
                                    <small class="d-block text-muted mt-1">from the app's main menu</small>
                                </li>
                                <li class="mb-3">
                                    <strong>Point your camera at the QR code</strong>
                                    <small class="d-block text-muted mt-1">on this screen</small>
                                </li>
                                <li class="mb-3">
                                    <strong>Wait for the scan to complete</strong>
                                    <small class="d-block text-muted mt-1">You'll see the "Attendance logged" confirmation with your time in/out</small>
                                </li>
                            </ol>
                        </div>
                    </div>

                </div>
            </div>

            <!-- RIGHT COLUMN: QR Code Display -->
            <div class="col-12 col-lg-4 d-flex">
                <div class="card shadow-lg attendance-card d-flex flex-column flex-fill qr-card">

                    <!-- Main Content -->
                    <div class="card-body d-flex flex-column bg-primary align-items-center justify-content-center text-center flex-grow-1 p-5 position-relative">
                        <h5 class="text-white mb-4 fw-bold">SCAN ME</h5>
                        <div class="qr-border p-3 mb-4 d-flex flex-column align-items-center justify-content-center">
                            <div id="qrcode"></div>
                            <div id="scan-success" class="alert alert-success d-none success-flash w-100" style="margin: 0;">
                                <i class="feather-check-circle me-2"></i>
                                Attendance Recorded Successfully!
                            </div>
                        </div>
                        <small class="text-white-50" id="qr-timer">30 SECONDS<br>BEFORE CHANGING QR</small>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- QR Code Library -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs/qrcode.min.js"></script>

    <!-- Scripts -->
    <script>
        let currentToken       = null;
        let tokenCheckInterval = null;
        let qrRefreshInterval  = null;
        let qrCountdown        = 30;

        // Digital Clock & Date
        function updateClock() {
            const now = new Date();
            document.getElementById("digital-clock").innerText = now.toLocaleTimeString();
            
            const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById("current-date").innerText = now.toLocaleDateString('en-US', options);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // QR Code Countdown 
        function updateQRTimer() {
            const timerEl = document.getElementById("qr-timer");
            if (qrCountdown > 0) {
                qrCountdown--;
                timerEl.innerText = qrCountdown + ' SECONDS\nBEFORE CHANGING QR';
            }
        }

        // QR Code Generation
        function loadQR() {
            fetch("{{ route('hr.qr.generate') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => {
                currentToken = data.token;
                const qrDiv = document.getElementById('qrcode');
                qrDiv.innerHTML = '';

                new QRCode(qrDiv, {
                    text: data.token,
                    width: 320,
                    height: 320,
                    correctLevel: QRCode.CorrectLevel.H
                });

                qrCountdown = 30;
                startTokenStatusCheck();
                startQRRefresh();
            });
        }

        function startQRRefresh() {
            if (qrRefreshInterval) clearInterval(qrRefreshInterval);
            qrRefreshInterval = setInterval(() => {
                updateQRTimer();
                if (qrCountdown <= 0) {
                    clearInterval(qrRefreshInterval);
                    loadQR();
                }
            }, 1000);
        }

        // Token Status Checking
        function checkTokenStatus() {
            fetch("{{ route('api.qr.token-status') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.used) {
                        triggerSuccess();
                        stopTokenStatusCheck();
                        loadQR();
                    }
                });
        }

        function startTokenStatusCheck() {
            if (tokenCheckInterval) clearInterval(tokenCheckInterval);
            tokenCheckInterval = setInterval(checkTokenStatus, 2000);
        }

        function stopTokenStatusCheck() {
            if (tokenCheckInterval) clearInterval(tokenCheckInterval);
        }

        // Success Feedback
        function triggerSuccess() {
            const successBox = document.getElementById("scan-success");
            const qrBorder = document.querySelector(".qr-border");

            qrBorder.classList.add("success-active");
            successBox.classList.remove("d-none");
            successBox.classList.add("animate-success");

            setTimeout(() => {
                successBox.classList.add("d-none");
                successBox.classList.remove("animate-success");
                qrBorder.classList.remove("success-active");
            }, 3000);
        }

        // Initialization
        window.addEventListener('load', () => {
            loadQR();
        });
    </script>

    <!-- Styles -->
    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .attendance-row {
            min-height: calc(100vh - 140px); 
        }

        .attendance-topbar {
            border-bottom: 3px solid #007bff;
        }

        .attendance-logo {
            width: 45px;
            height: 45px;
            object-fit: contain;
        }

        .attendance-bg {
            background: linear-gradient(135deg, #f0f4ff, #e0f7fa);
            min-height: calc(100vh - 120px);
        }

        .attendance-card {
            border-radius: 15px;
            overflow: hidden;
        }

        .qr-border {
            background: white;
            border: 8px solid white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .qr-border.success-active #qrcode {
        opacity: 0.2;
        }

        #qr-timer {
            text-transform: uppercase;
        }

        #qrcode {
            min-height: 320px;
            min-width: 320px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .instructions-container h5 {
            color: #000000;
            font-size: 1.25rem;
        }

        .instruction-list {
            list-style: none;
            padding: 0;
            counter-reset: step-counter;
            font-size: 1.05rem;
            line-height: 1.8;
        }

        .instruction-list li {
            counter-increment: step-counter;
            padding: 0.2rem 0;
            padding-left: 4rem;
            position: relative;
            border-left: 3px solid #007bff;
            margin-left: 0.5rem;
        }

        .instruction-list li::before {
            content: counter(step-counter);
            position: absolute;
            left: -1.5rem;
            top: 0;
            background: #007bff;
            color: white;
            width: 2.5rem;
            height: 2.5rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
        }

        .instruction-list li:last-child {
            border-left: none;
        }

        .instruction-list li strong {
            color: #333;
            font-weight: 600;
        }

        .pulse-badge {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%   { transform: scale(1); }
            50%  { transform: scale(1.08); }
            100% { transform: scale(1); }
        }

        .animate-success {
            animation: pop 0.5s ease;
        }

        @keyframes pop {
            from { transform: scale(0.8); opacity: 0; }
            to   { transform: scale(1);   opacity: 1; }
        }

        @media (max-width: 992px) {
            .attendance-bg {
                height: auto !important;
            }

            #qrcode {
                min-height: 250px;
                min-width: 250px;
            }

            .instruction-list {
                font-size: 0.95rem;
            }

            .instruction-list li {
                padding-left: 3.5rem;
            }

            .instruction-list li::before {
                width: 2rem;
                height: 2rem;
                font-size: 0.95rem;
                left: -1.2rem;
            }
        }
    </style>

    <!--! BEGIN: Vendors JS !-->
    <script src="{{ asset('vendors/js/vendors.min.js') }}"></script>
    <!--! END: Vendors JS !-->
    <!--! BEGIN: Apps Init  !-->
    <script src="{{ asset('js/common-init.min.js') }}"></script>
    <!--! END: Apps Init !-->

</body>

</html>