<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="adminHMD authentication page">
    <title>Login | RentCam</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>

<body class="auth-body">
    <button class="icon-button theme-toggle auth-theme-toggle" type="button" data-theme-toggle
        aria-label="Switch color theme" title="Switch color theme">
        <i class="bi bi-moon-stars" data-theme-icon aria-hidden="true"></i>
    </button>
    <main class="auth-page">
        <section class="auth-card">
            <a class="auth-brand" href="{{ route('login') }}"><span class="brand-icon"><i class="bi bi-grid-1x2-fill"
                        aria-hidden="true"></i></span><span><strong>RentCam</strong><small>Pusat Rental Kamera dan tmn2nya</small></span></a>

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-circle me-1" aria-hidden="true"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login.process') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <h1 class="h3 mb-1">Login</h1>
                    <p class="text-muted mb-0">Sign in to your admin workspace.</p>
                </div>

                <div class="mb-3">
                    <label class="form-label" for="loginEmail">Email</label>
                    <input class="form-control @error('email') is-invalid @enderror" id="loginEmail" name="email"
                        type="email" value="{{ old('email') }}" required autofocus>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label" for="loginPassword">Password</label>
                    <input class="form-control @error('password') is-invalid @enderror" id="loginPassword"
                        name="password" type="password" required>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>


                <button class="btn btn-primary w-100" type="submit"><i class="bi bi-box-arrow-in-right"
                        aria-hidden="true"></i> LOGIN</button>
            </form>
        </section>
    </main>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
</body>

</html>
