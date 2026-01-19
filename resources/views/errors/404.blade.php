<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 Halaman Tidak Ditemukan</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            color: #334155;
            text-align: center;
        }
        .error-container {
            background: #ffffff;
            padding: 60px 40px;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            max-width: 480px;
            width: 90%;
            position: relative;
            overflow: hidden;
        }
        .error-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 6px;
            background: linear-gradient(90deg, #ef4444, #f59e0b);
        }
        .icon-wrapper {
            font-size: 5rem;
            color: #ef4444;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }
        h1 {
            font-size: 4rem;
            font-weight: 800;
            color: #1e293b;
            margin: 0;
            line-height: 1;
        }
        h2 {
            font-size: 1.5rem;
            margin: 15px 0 25px;
            font-weight: 600;
            color: #334155;
        }
        p {
            font-size: 1.1rem;
            color: #64748b;
            margin-bottom: 30px;
            line-height: 1.6;
            background: #fef2f2;
            padding: 15px;
            border-radius: 12px;
            border: 1px dashed #fcd34d;
            font-weight: 500;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: #0f172a;
            color: white;
            padding: 14px 28px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .btn:hover {
            background: #1e293b;
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon-wrapper">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <h1>404</h1>
        <h2>Halaman Hilang!</h2>
        <p>
            <i class="fas fa-bullhorn" style="color: #ef4444; margin-right: 5px;"></i>
            Halaman hilang segera laporkan ke posko terdekat
        </p>
        <a href="/" class="btn">
            <i class="fas fa-home"></i> Kembali ke Beranda
        </a>
    </div>
</body>
</html>
