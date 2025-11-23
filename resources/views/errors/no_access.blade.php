<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tidak Punya Akses</title>
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <!-- Auto redirect after 3 seconds -->
    <meta http-equiv="refresh" content="3;url={{ $redirectUrl }}">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {background:#f8f9fc;font-family:'Nunito',sans-serif;}
        .access-wrapper {min-height:100vh;display:flex;align-items:center;justify-content:center;}
        .access-card {max-width:520px;width:100%;box-shadow:0 0.15rem 1.75rem 0 rgba(58,59,69,.15);border-radius:.5rem;}
        .pulse {animation:pulse 1.4s infinite;}@keyframes pulse {0%{opacity:.4}50%{opacity:1}100%{opacity:.4}}
    </style>
</head>
<body>
<div class="access-wrapper">
    <div class="card access-card border-left-danger">
        <div class="card-body text-center py-4">
            <h4 class="text-danger font-weight-bold mb-3">Maaf, kamu tidak punya akses halaman ini</h4>
            <p class="mb-2 text-muted">Sesi login kamu sudah berakhir atau hak akses tidak valid.</p>
            <p class="small text-muted">Mengalihkan ke halaman login dalam 3 detik...</p>
            <div class="pulse"><i class="fas fa-lock fa-3x text-danger"></i></div>
            <a href="{{ $redirectUrl }}" class="btn btn-danger btn-sm mt-4">Ke Halaman Login Sekarang</a>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js" defer></script>
</body>
</html>