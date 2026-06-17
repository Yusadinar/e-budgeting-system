<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'E-Budgeting System') }} — @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&family=DM+Serif+Display&display=swap" rel="stylesheet" />

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans:    ['"DM Sans"', 'sans-serif'],
                        display: ['"DM Serif Display"', 'serif'],
                    },
                }
            }
        }
    </script>

    <style>
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up { animation: fadeUp 0.5s ease both; }
        .animate-fade-up-delay { animation: fadeUp 0.5s ease 0.1s both; }

        /* Dot grid background */
        .dot-grid {
            background-image: radial-gradient(circle, #e2e8f0 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="h-full bg-slate-50 font-sans antialiased">
    <div class="min-h-screen dot-grid flex items-center justify-center px-4 py-12">



        <div class="w-full max-w-sm animate-fade-up-delay">
            @yield('content')
        </div>
    </div>
    <script>
        function togglePassword(inputId, btnId) {
            const passwordInput = document.getElementById(inputId);
            const toggleBtn = document.getElementById(btnId);
            const eyeIcon = toggleBtn.querySelector('.eye-icon');
            const eyeOffIcon = toggleBtn.querySelector('.eye-off-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.add('hidden');
                eyeOffIcon.classList.remove('hidden');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('hidden');
                eyeOffIcon.classList.add('hidden');
            }
        }
    </script>
</body>
</html>