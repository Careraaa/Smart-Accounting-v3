<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="x-ua-compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="description" content="Attendance Monitoring" />
    <title>Attendance • Knights Transport</title>
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('images/knights_logo_icon.png') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- Keep your existing vendors/theme if needed, but we're minimizing external dependency here -->
</head>

<body class="attendance-page">

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

                <!-- Left – Instructions -->
                <div class="col-12 col-lg-7">
                    <div class="card glass-card h-100 shadow-xl border-0">
                        <div class="card-body p-3 p-lg-4">
                            <h5 class="fw-semibold mb-4 text-dark text-center">HOW TO LOG ATTENDANCE</h5>
                            
                            <ol class="instruction-steps list-unstyled">
                                <li>
                                    <strong>Open Smart Accounting</strong>
                                    <span class="text-muted d-block mt-1 small">on your mobile device</span>
                                </li>
                                <li>
                                    <strong>Sign in</strong>
                                    <span class="text-muted d-block mt-1 small">using your registered email (complete OTP if required)</span>
                                </li>
                                <li>
                                    <strong>Go to Scan QR</strong>
                                    <span class="text-muted d-block mt-1 small">from the main menu</span>
                                </li>
                                <li>
                                    <strong>Scan the code below</strong>
                                    <span class="text-muted d-block mt-1 small">align your camera with this screen</span>
                                </li>
                                <li>
                                    <strong>Confirmation appears</strong>
                                    <span class="text-muted d-block mt-1 small">“Attendance logged” + time in/out</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>

                <!-- Right – QR Code -->
                <div class="col-12 col-lg-5">
                    <div class="card glass-card h-100 shadow-xl border-0 qr-panel">
                        <div class="card-body p-4 p-lg-5 d-flex flex-column align-items-center justify-content-center text-center position-relative">
                            <h5 class="fw-semibold mb-4 text-white">SCAN HERE</h5>
                            
                            <div class="qr-container">
                                <div id="qrcode" class="qr-code"></div>
                                <div id="scan-success" class="alert alert-success d-none success-flash">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Attendance Recorded!
                                </div>
                            </div>

                            <div class="mt-4 text-white-75 small fw-medium" id="qr-timer">30 SECONDS <br> QR REFRESHING SOON</div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>

    <script>
        // ────────────────────────────────────────────────
        // Clock & Date
        function updateClock() {
            const now = new Date();
            document.getElementById("digital-clock").textContent = now.toLocaleTimeString([], {hour12: true});
            document.getElementById("current-date").textContent = now.toLocaleDateString('en-US', {
                weekday: 'long', month: 'long', day: 'numeric', year: 'numeric'
            });
        }
        setInterval(updateClock, 1000);
        updateClock();

        // ────────────────────────────────────────────────
        let currentToken = null;
        let tokenCheckInterval = null;
        let qrRefreshInterval  = null;
        let qrCountdown = 30;

        function updateQRTimer() {
            const el = document.getElementById("qr-timer");
            if (qrCountdown > 0) {
                qrCountdown--;
                el.innerHTML = `${qrCountdown} SECONDS<br>QR REFRESHING SOON`;
            }
        }

        function loadQR() {
            fetch("{{ route('hr.qr.generate') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(r => r.json())
            .then(data => {
                currentToken = data.token;
                const qrDiv = document.getElementById('qrcode');
                qrDiv.innerHTML = '';

                new QRCode(qrDiv, {
                    text: data.token,
                    width: 260,
                    height: 260,
                    colorDark: "#1a1a1a",
                    colorLight: "#ffffff",
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

        function checkTokenStatus() {
            fetch("{{ route('api.qr.token-status') }}")
                .then(r => r.json())
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

        function triggerSuccess() {
            const success = document.getElementById("scan-success");
            const container = document.querySelector(".qr-container");

            container.classList.add("success-active");
            success.classList.remove("d-none");
            success.classList.add("animate__animated", "animate__fadeInUp");

            setTimeout(() => {
                success.classList.add("d-none");
                success.classList.remove("animate__animated", "animate__fadeInUp");
                container.classList.remove("success-active");
            }, 3200);
        }

        window.addEventListener('load', loadQR);
    </script>

    <style>
        :root {
            --primary: #cc3d38;   
            --primary-dark: #530a0a;
            --bg: #e6e6e6;
        }

        body {
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

        .qr-panel {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border-radius: 20px;
        }

        .qr-container {
            background: white;
            border-radius: 16px;
            padding: 1.5rem;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            transition: all 0.4s ease;
        }

        .qr-container.success-active {
            box-shadow: 0 0 0 6px rgba(34,197,94,0.4);
            transform: scale(1.04);
        }

        .qr-code {
            min-width: 260px;
            min-height: 260px;
            margin: 0 auto;
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

        .success-flash {
            position: absolute;
            inset: 0;
            margin: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 600;
            background: rgba(34,197,94,0.95);
            color: white;
            border-radius: 16px;
            z-index: 10;
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

    <!-- Optional: animate.css for smoother success animation -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>

</body>
</html>