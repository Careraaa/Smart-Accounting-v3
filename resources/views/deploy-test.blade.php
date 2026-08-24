<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deployment Test — Smart Accounting</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: linear-gradient(135deg, #0f172a, #1e293b);
            color: #e2e8f0;
        }
        .card {
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 20px;
            padding: 48px 56px;
            text-align: center;
            max-width: 560px;
            box-shadow: 0 25px 60px rgba(0,0,0,.45);
        }
        h1 { font-size: 2.2rem; margin-bottom: 8px; }
        .badge {
            display: inline-block;
            background: #22c55e22;
            color: #4ade80;
            border: 1px solid #4ade8055;
            padding: 6px 16px;
            border-radius: 999px;
            font-size: .85rem;
            letter-spacing: .5px;
            margin-bottom: 24px;
        }
        p { color: #94a3b8; line-height: 1.7; margin-bottom: 8px; }
        .meta {
            margin-top: 28px;
            padding-top: 24px;
            border-top: 1px dashed rgba(255,255,255,.15);
            font-size: .85rem;
            color: #64748b;
        }
        .meta b { color: #cbd5e1; }
        a.back {
            display: inline-block;
            margin-top: 28px;
            color: #60a5fa;
            text-decoration: none;
            font-weight: 600;
        }
        a.back:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <div class="badge">✅ PIPELINE TEST SUCCESSFUL</div>
        <h1>It Works!</h1>
        <p>If you can read this page, the full deployment pipeline is alive:</p>
        <p><b>git push → GitHub Actions → build → FTP → live site</b></p>
        <div class="meta">
            Rendered at <b>{{ now('Asia/Manila')->format('M d, Y — g:i:s A') }}</b><br>
            Server: {{ request()->getHost() }}
        </div>
        <a class="back" href="{{ url('/') }}">← Back to login</a>
    </div>
</body>
</html>
