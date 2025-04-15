<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Payment Page')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional custom styles -->
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar-brand {
            font-weight: bold;
        }
        .card {
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        footer {
            margin-top: 50px;
            background-color: #fff;
            padding: 20px 0;
            border-top: 1px solid #dee2e6;
        }
    </style>
</head>

<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
        <div class="container">
            <a class="navbar-brand" href="#">PaymentPortal</a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card p-4">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="text-center">
        <div class="container">
            <p class="mb-0 text-muted">&copy; {{ date('Y') }} PaymentPortal. All rights reserved.</p>
        </div>
    </footer>


</body>

</html>
