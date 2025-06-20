<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATMI Student Portal - Login</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tambahkan di head section -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Left Panel - Login Form -->
        <div class="flex-1 flex items-center justify-center px-4 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <!-- Logo and Title -->
                <div class="text-center mb-8">
                    <div
                        class="mx-auto w-20 h-20 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl flex items-center justify-center mb-6 shadow-lg">
                        <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" />
                        </svg>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">ATMI Education Portal</h1>
                    <p class="text-gray-600">Welcome back! Please sign in to your account</p>
                </div>

                <!-- Login Form -->
                <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                    <!-- Email Domain Info -->
                    <div class="mb-6">
                        <p class="text-sm text-gray-600 mb-3 text-center">Please use your institutional email</p>
                        <div class="flex flex-wrap justify-center gap-2">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                @student.atmi.ac.id
                            </span>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">
                                @atmi.ac.id
                            </span>
                        </div>
                    </div>
                    <div id="g_id_onload"
                        data-client_id="325198821446-fnj1ouur8bqgmlvjnt6of77lmp1es5do.apps.googleusercontent.com"
                        data-context="signin" data-ux_mode="redirect"
                        data-login_uri="@php echo $_ENV['GOOGLE_REDIRECT_URI'] @endphp" data-auto_prompt="false">
                    </div>

                    <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline"
                        data-text="signin_with" data-size="large" data-logo_alignment="left">
                    </div>
                    <!-- Google Sign In Button -->
                    <button onclick="window.location.href='{{ route('login.google') }}'"
                        class="w-full flex items-center justify-center px-6 py-3 border border-gray-300 rounded-xl shadow-sm bg-white text-gray-700 font-medium hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 group">
                        <svg class="w-5 h-5 mr-3" viewBox="0 0 24 24">
                            <path fill="#4285F4"
                                d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
                            <path fill="#34A853"
                                d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
                            <path fill="#FBBC05"
                                d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
                            <path fill="#EA4335"
                                d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
                        </svg>
                        <span class="group-hover:text-gray-900 transition-colors">Continue with Google</span>
                    </button>

                    <!-- Footer Links -->
                    <div class="mt-6 text-center space-y-3">
                        <p class="text-xs text-gray-500">
                            By signing in, you agree to our
                            <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Terms of
                                Service</a>
                            and
                            <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Privacy
                                Policy</a>
                        </p>
                        <div class="pt-3 border-t border-gray-100">
                            <p class="text-sm text-gray-600">
                                Need help?
                                <a href="#" class="text-indigo-600 hover:text-indigo-700 font-medium">Contact
                                    Support</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Image -->
        <div class="hidden lg:flex lg:flex-1 relative overflow-hidden">
            <!-- Background Image -->
            <img src="https://images.unsplash.com/photo-1523240795612-9a054b0db644?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80"
                alt="Education Background" class="absolute inset-0 w-full h-full object-cover" />

            <!-- Overlay -->
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 via-indigo-800/70 to-purple-900/80"></div>

            <!-- Content -->
            <div class="relative flex flex-col justify-center items-center text-white p-12 z-10">
                <div class="text-center max-w-md">
                    <h2 class="text-3xl font-bold mb-4">ATMI Education Portal</h2>
                    <p class="text-xl text-indigo-100 leading-relaxed">
                        Access your academic resources, course materials, and institutional services with your ATMI
                        account.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <script src="https://accounts.google.com/gsi/client" async></script>

</body>

</html>
