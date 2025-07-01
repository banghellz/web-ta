<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Google Sign-In -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .google-signin-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        /* Override Google Sign-In button styles */
        .g_id_signin {
            display: flex !important;
            justify-content: center !important;
            width: 100% !important;
            max-width: 320px !important;
            margin: 0 auto !important;
        }

        .g_id_signin iframe {
            width: 100% !important;
            max-width: 320px !important;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        .fade-out {
            animation: fadeOut 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateY(0);
            }

            to {
                opacity: 0;
                transform: translateY(-10px);
            }
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
                    <div class="flex-grow-1 d-flex justify-content-center">
                        <a href="." aria-label="Cabinex" class="navbar-brand m-0">
                            <img src="/logo/darkmode_logo.png" alt="Cabinex Logo" class="navbar-brand-image"
                                style="width: 120px; height: auto;">
                        </a>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">Cabinex Portal</h1>
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

                    <!-- Initial Loading Spinner -->
                    <div id="initial_loading" class="flex flex-col items-center justify-center py-8">
                        <div class="spinner mb-4"></div>
                        <p class="text-sm text-gray-600">Loading Google Sign-In...</p>
                    </div>

                    <!-- Google Sign-In Button -->
                    <div id="sign_in_container" class="hidden">
                        <div id="g_id_onload"
                            data-client_id="325198821446-fnj1ouur8bqgmlvjnt6of77lmp1es5do.apps.googleusercontent.com"
                            data-context="signin" data-ux_mode="popup" data-callback="handleCredentialResponse"
                            data-auto_prompt="false" data-itp_support="true">
                        </div>

                        <div class="flex justify-center">
                            <div class="g_id_signin" data-type="standard" data-shape="rectangular" data-theme="outline"
                                data-text="signin_with" data-size="large" data-logo_alignment="center" data-width="320">
                            </div>
                        </div>
                    </div>

                    <!-- Login Processing Spinner -->
                    <div id="login_processing" class="hidden flex flex-col items-center justify-center py-8">
                        <div class="spinner mb-4"></div>
                        <p class="text-sm text-gray-600 mb-2">Signing you in...</p>
                        <p class="text-xs text-gray-500">Please wait while we verify your account</p>
                    </div>

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
            <img src="/img/background_login.jpg" alt="Education Background"
                class="absolute inset-0 w-full h-full object-cover" />

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

    <script>
        // Elements
        const initialLoading = document.getElementById('initial_loading');
        const signInContainer = document.getElementById('sign_in_container');
        const loginProcessing = document.getElementById('login_processing');

        // Show/hide functions with animations
        function showElement(element, hideElements = []) {
            hideElements.forEach(el => {
                if (el && !el.classList.contains('hidden')) {
                    el.classList.add('fade-out');
                    setTimeout(() => {
                        el.classList.add('hidden');
                        el.classList.remove('fade-out');
                    }, 300);
                }
            });

            setTimeout(() => {
                if (element) {
                    element.classList.remove('hidden');
                    element.classList.add('fade-in');
                    setTimeout(() => element.classList.remove('fade-in'), 500);
                }
            }, hideElements.length > 0 ? 300 : 0);
        }

        // Handle credential response from Google
        function handleCredentialResponse(response) {
            console.log('Google Sign-In successful, processing...');

            // Show processing spinner and hide sign-in button
            showElement(loginProcessing, [signInContainer]);

            // Create form and submit traditionally (avoid CORS issues)
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/auth/google/callback';
            form.style.display = 'none';

            // Add CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            // Add credential
            const credentialInput = document.createElement('input');
            credentialInput.type = 'hidden';
            credentialInput.name = 'credential';
            credentialInput.value = response.credential;
            form.appendChild(credentialInput);

            // Submit form
            document.body.appendChild(form);

            // Add a small delay to show the spinner
            setTimeout(() => {
                form.submit();
            }, 500);
        }

        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            // Force HTTPS redirect if needed
            const loc = window.location.href;
            if (loc.indexOf('http://') === 0 && loc.indexOf('localhost') === -1) {
                window.location.href = loc.replace('http://', 'https://');
                return;
            }

            // Simulate loading time for Google Sign-In initialization
            setTimeout(() => {
                showElement(signInContainer, [initialLoading]);
            }, 1000);
        });

        // Handle errors from Google Sign-In
        window.addEventListener('error', function(e) {
            console.error('Google Sign-In error:', e);
            if (e.message && e.message.includes('Cross-Origin-Opener-Policy')) {
                console.log('COOP error detected, but continuing...');
                // This error can be safely ignored in most cases
            }
        });

        // Prevent multiple form submissions
        let isSubmitting = false;

        // Override the default Google Sign-In click handler to add loading state
        document.addEventListener('click', function(e) {
            if (e.target.closest('.g_id_signin') && !isSubmitting) {
                isSubmitting = true;
                console.log('Google Sign-In button clicked');
                // Reset after a timeout in case sign-in fails
                setTimeout(() => {
                    if (isSubmitting) {
                        isSubmitting = false;
                        console.log('Reset submission flag after timeout');
                    }
                }, 30000); // 30 seconds timeout
            }
        });
    </script>
</body>

</html>
