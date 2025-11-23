<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Invoice Gaji</title>
    <style>
        body{font-family: Arial, Helvetica, sans-serif; color:#333;}
        .container{max-width:900px;margin:0 auto;}
        .card{border:1px solid #ddd;border-radius:6px;}
        .card-body{padding:24px;}
        table{width:100%;border-collapse:collapse;}
        th,td{padding:8px;border:1px solid #e5e5e5;}
        thead th{background:#f8f9fc;}
        .text-right{text-align:right}
        .text-success{color:#108a00}
        .text-danger{color:#b30000}
        @media print{
            body{-webkit-print-color-adjust:exact; print-color-adjust:exact}
            thead th{background:#f0f3ff !important}
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container">
        @include('owner.invoice-salary.template',['payment'=>$payment])
    </div>
</body>
</html>