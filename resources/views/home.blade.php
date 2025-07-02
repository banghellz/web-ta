<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website ATMI - CABINEX</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .carousel-container {
            position: relative;
            overflow: hidden;
        }

        .carousel-slide {
            transition: transform 0.5s ease-in-out;
        }

        .carousel-indicators {
            bottom: 1rem;
        }

        .indicator {
            transition: all 0.3s ease;
        }

        .indicator.active {
            background-color: white;
            transform: scale(1.2);
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">
    <!-- Navbar -->
    <header class="bg-white shadow-lg" x-data="{ isMobileOpen: false }">
        <nav class="mx-auto flex max-w-7xl items-center justify-between p-6 lg:px-8" aria-label="Global">
            <div class="flex lg:flex-1">
                <a href="#" class="-m-1.5 p-1.5">
                    <span class="sr-only">ATMI</span>
                    <img class="h-8 w-auto" src="/logo/logo_new_mid.png" alt="ATMI Logo">
                </a>
            </div>
            <div class="flex lg:hidden">
                <button type="button" @click="isMobileOpen=!isMobileOpen"
                    class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700">
                    <span class="sr-only">Open main menu</span>
                    <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                        aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
            <div class="hidden lg:flex lg:gap-x-12">
                <a href="#hero" class="text-sm/6 font-semibold text-gray-900 hover:text-indigo-600">Home</a>
                <a href="#specs" class="text-sm/6 font-semibold text-gray-900 hover:text-indigo-600">Specifications</a>
                <a href="#features" class="text-sm/6 font-semibold text-gray-900 hover:text-indigo-600">Features</a>
                <a href="#manual" class="text-sm/6 font-semibold text-gray-900 hover:text-indigo-600">Manual</a>
                <a href="#team" class="text-sm/6 font-semibold text-gray-900 hover:text-indigo-600">Team</a>
                <a href="#contact" class="text-sm/6 font-semibold text-gray-900 hover:text-indigo-600">Contact</a>
            </div>
            <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                <a href="/login" class="text-sm/6 font-semibold text-gray-900 hover:text-indigo-600">Log in <span
                        aria-hidden="true">&rarr;</span></a>
            </div>
        </nav>

        <!-- Mobile menu -->
        <div class="lg:hidden" role="dialog" aria-modal="true" x-show="isMobileOpen"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">
            <div class="fixed inset-0 z-10"></div>
            <div
                class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">ATMI</span>
                        <img class="h-8 w-auto" src="/logo/darkmode_logo.png" alt="ATMI Logo">
                    </a>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="isMobileOpen=false">
                        <span class="sr-only">Close menu</span>
                        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <a href="#hero"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Home</a>
                            <a href="#specs"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Specifications</a>
                            <a href="#features"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Features</a>
                            <a href="#manual"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Manual</a>
                            <a href="#team"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Team</a>
                            <a href="#contact"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Contact</a>
                        </div>
                        <div class="py-6">
                            <a href="/login"
                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Log
                                in</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Hero Section with Carousel -->
    <section id="hero" class="relative bg-gradient-to-br from-indigo-900 via-indigo-800 to-blue-900 text-white">
        <div class="absolute inset-0 bg-black/20"></div>

        <!-- Hero Carousel -->
        <div class="carousel-container relative h-96 lg:h-[500px]" x-data="{
            currentSlide: 0,
            slides: [{
                    image: 'https://images.unsplash.com/photo-1558618047-3c8c76ca7d13?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                    title: 'Smart RFID Access Control',
                    description: 'Secure tool access with RFID card authentication'
                },
                {
                    image: 'https://images.unsplash.com/photo-1581091226825-a6a2a5aee158?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                    title: 'Digital Coin System',
                    description: 'Modern digital token-based borrowing system'
                },
                {
                    image: 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                    title: 'Real-Time Dashboard',
                    description: 'Monitor and manage tools in real-time'
                },
                {
                    image: 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                    title: 'Automated Notifications',
                    description: 'Smart alerts and complete history tracking'
                }
            ],
            nextSlide() {
                this.currentSlide = (this.currentSlide + 1) % this.slides.length;
            },
            prevSlide() {
                this.currentSlide = this.currentSlide === 0 ? this.slides.length - 1 : this.currentSlide - 1;
            },
            goToSlide(index) {
                this.currentSlide = index;
            }
        }" x-init="setInterval(() => nextSlide(), 5000)">

            <!-- Carousel Images -->
            <div class="absolute inset-0">
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="currentSlide === index" x-transition:enter="transition ease-out duration-500"
                        x-transition:enter-start="opacity-0 transform translate-x-full"
                        x-transition:enter-end="opacity-100 transform translate-x-0"
                        x-transition:leave="transition ease-in duration-500"
                        x-transition:leave-start="opacity-100 transform translate-x-0"
                        x-transition:leave-end="opacity-0 transform -translate-x-full" class="absolute inset-0">
                        <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-black/40"></div>
                    </div>
                </template>
            </div>

            <!-- Carousel Content -->
            <div class="relative z-10 flex items-center justify-center h-full">
                <div class="text-center px-6 max-w-4xl">
                    <template x-for="(slide, index) in slides" :key="index">
                        <div x-show="currentSlide === index"
                            x-transition:enter="transition ease-out duration-700 delay-300"
                            x-transition:enter-start="opacity-0 transform translate-y-8"
                            x-transition:enter-end="opacity-100 transform translate-y-0">
                            <h2 class="text-2xl md:text-4xl font-bold mb-4" x-text="slide.title"></h2>
                            <p class="text-lg md:text-xl text-gray-200" x-text="slide.description"></p>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Carousel Navigation -->
            <button @click="prevSlide()"
                class="absolute left-4 top-1/2 transform -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 rounded-full p-2 transition-colors">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>

            <button @click="nextSlide()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 z-20 bg-white/20 hover:bg-white/30 rounded-full p-2 transition-colors">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>

            <!-- Carousel Indicators -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 z-20 flex space-x-2">
                <template x-for="(slide, index) in slides" :key="index">
                    <button @click="goToSlide(index)" :class="{ 'active': currentSlide === index }"
                        class="indicator w-3 h-3 rounded-full bg-white/50 hover:bg-white/75 transition-all duration-300"></button>
                </template>
            </div>
        </div>

        <!-- Main Hero Content -->
        <div class="relative mx-auto max-w-7xl px-6 py-24 sm:py-32 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h1 class="text-4xl font-bold tracking-tight sm:text-6xl">
                    Welcome to <span class="text-indigo-300">CABINEX</span>
                </h1>
                <p class="mt-6 text-lg leading-8 text-gray-300">
                    Advanced Technology Manufacturing Innovation - Smart tool management system with RFID integration,
                    digital coin system, and real-time monitoring for modern workshops.
                </p>
                <div class="mt-10 flex items-center justify-center gap-x-6">
                    <a href="#features"
                        class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors">
                        Explore Features
                    </a>
                    <a href="#contact"
                        class="text-sm font-semibold leading-6 text-white hover:text-indigo-300 transition-colors">
                        Contact Us <span aria-hidden="true">→</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute bottom-0 left-1/2 transform -translate-x-1/2">
            <svg class="w-6 h-6 text-white animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3">
                </path>
            </svg>
        </div>
    </section>

    <!-- Specifications Section -->
    <section id="specs" class="py-24 sm:py-32 bg-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Hardware Specifications</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    CABINEX is built with high-quality components for reliable performance and seamless integration.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-5xl">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-indigo-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Main Controller</h3>
                        <p class="text-2xl font-bold text-indigo-600 mb-2">Raspberry Pi 4</p>
                        <p class="text-sm text-gray-600">High-performance ARM processor with 4GB/8GB RAM options</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-green-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">RFID Reader</h3>
                        <p class="text-2xl font-bold text-green-600 mb-2">R20DC</p>
                        <p class="text-sm text-gray-600">Long-range RFID reader for seamless card detection</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Display</h3>
                        <p class="text-2xl font-bold text-purple-600 mb-2">Waveshare</p>
                        <p class="text-sm text-gray-600">7-inch capacitive touchscreen for user interface</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-yellow-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Relay Module</h3>
                        <p class="text-2xl font-bold text-yellow-600 mb-2">8-Channel</p>
                        <p class="text-sm text-gray-600">For cabinet lock control and device automation</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-red-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Power Supply</h3>
                        <p class="text-2xl font-bold text-red-600 mb-2">5V 3A</p>
                        <p class="text-sm text-gray-600">Stable power for Raspberry Pi and all accessories</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-8 text-center hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Connectivity</h3>
                        <p class="text-2xl font-bold text-blue-600 mb-2">Wi-Fi / LAN</p>
                        <p class="text-sm text-gray-600">Dual connectivity options for flexible deployment</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-24 sm:py-32 bg-gray-50">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Key Features</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    CABINEX revolutionizes tool management with smart features designed for modern workshops.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-7xl">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">💳 Smart Access with RFID Integration
                            </h3>
                            <p class="text-gray-600">Users can only borrow tools by logging in with a registered
                                account linked to an RFID card, ensuring secure and authorized access.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">💰 Digital Coin System</h3>
                            <p class="text-gray-600">Physical coins are replaced with digital tokens. Each borrowing
                                action deducts coins automatically, reducing the risk of loss and simplifying the
                                lending process.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">📊 Real-Time Monitoring Dashboard</h3>
                            <p class="text-gray-600">Admins can track tool status live, view borrowing history, and
                                manage users and tools through an intuitive, web-based dashboard.</p>
                        </div>
                    </div>
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-orange-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-5 5v-5zM4 6h16M4 12h16m-7 6H4"></path>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">🔔 Automated Notifications & History
                                Logs</h3>
                            <p class="text-gray-600">Automated notifications keep users informed about their borrowing
                                activities, while comprehensive history logs provide complete audit trails for all
                                transactions.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Manual Book Section -->
    <section id="manual" class="py-24 sm:py-32 bg-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Documentation & Manuals</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    Comprehensive guides and documentation to help you get the most out of CABINEX.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-5xl">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-xl p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-blue-600 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Quick Start Guide</h3>
                        <p class="text-gray-600 mb-4">Get up and running in minutes with our step-by-step installation
                            and setup guide for CABINEX.</p>
                        <a href="#"
                            class="inline-flex items-center text-blue-600 hover:text-blue-800 font-medium">
                            Download PDF <span class="ml-1">→</span>
                        </a>
                    </div>
                    <div
                        class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-xl p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-green-600 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">User Manual</h3>
                        <p class="text-gray-600 mb-4">Comprehensive documentation covering all CABINEX features, user
                            interface, and best practices.</p>
                        <a href="#"
                            class="inline-flex items-center text-green-600 hover:text-green-800 font-medium">
                            Download PDF <span class="ml-1">→</span>
                        </a>
                    </div>
                    <div
                        class="bg-gradient-to-br from-purple-50 to-violet-100 rounded-xl p-8 hover:shadow-lg transition-shadow">
                        <div class="w-12 h-12 bg-purple-600 rounded-lg mb-4 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">Technical Manual</h3>
                        <p class="text-gray-600 mb-4">Advanced configuration, hardware setup, troubleshooting, and
                            maintenance procedures.</p>
                        <a href="#"
                            class="inline-flex items-center text-purple-600 hover:text-purple-800 font-medium">
                            Download PDF <span class="ml-1">→</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="team" class="py-24 sm:py-32 bg-gray-50">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Meet Our Team</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    The innovative minds behind CABINEX - Smart Tool Management System.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-4xl">
                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 justify-center">
                    <div class="bg-white rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-indigo-400 to-indigo-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <span class="text-2xl font-bold text-white">CD</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-1">Christopher Davin</h3>
                        <p class="text-indigo-600 font-medium mb-3">Lead Developer & System Architect</p>
                        <p class="text-gray-600 text-sm">Designing and implementing the core CABINEX system
                            architecture, from RFID integration to web dashboard development.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-8 text-center shadow-lg hover:shadow-xl transition-shadow">
                        <div
                            class="w-20 h-20 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full mx-auto mb-4 flex items-center justify-center">
                            <span class="text-2xl font-bold text-white">DN</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-1">Dipa Nusantara</h3>
                        <p class="text-blue-600 font-medium mb-3">Hardware Engineer & Integration Specialist</p>
                        <p class="text-gray-600 text-sm">Specializing in hardware integration, Raspberry Pi
                            configuration, and ensuring seamless communication between all system components.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Us Section -->
    <section id="contact" class="py-24 sm:py-32 bg-white">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl text-center">
                <h2 class="text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl">Get In Touch</h2>
                <p class="mt-4 text-lg leading-8 text-gray-600">
                    Interested in implementing CABINEX in your workshop? Contact our team for more information.
                </p>
            </div>
            <div class="mx-auto mt-16 max-w-6xl">
                <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
                    <!-- Contact Information -->
                    <div class="space-y-8">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-6">Contact Information</h3>
                            <div class="space-y-6">
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-indigo-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Office Address</h4>
                                        <p class="text-gray-600">ATMI Polytechnic<br>Jl. Mojo No.1,
                                            Karangasem<br>Surakarta, Central Java<br>Indonesia</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-green-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Phone Number</h4>
                                        <p class="text-gray-600">+62 271 714466<br>+62 271 714390</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4">
                                    <div
                                        class="w-12 h-12 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-lg font-semibold text-gray-900">Email Address</h4>
                                        <p class="text-gray-600">info@atmi.ac.id<br>cabinex@atmi.ac.id</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-100 rounded-2xl p-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-3">Office Hours</h4>
                            <div class="space-y-2 text-sm text-gray-600">
                                <div class="flex justify-between">
                                    <span>Monday - Friday</span>
                                    <span>7:00 AM - 4:00 PM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Saturday</span>
                                    <span>7:00 AM - 12:00 PM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>Sunday</span>
                                    <span>Closed</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Form -->
                    <div class="bg-gray-50 rounded-2xl p-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h3>
                        <form class="space-y-6" x-data="{ formData: { name: '', email: '', subject: '', message: '' } }">
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full
                                        Name</label>
                                    <input type="text" id="name" x-model="formData.name"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="Your Name">
                                </div>
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                        Address</label>
                                    <input type="email" id="email" x-model="formData.email"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                        placeholder="your@email.com">
                                </div>
                            </div>
                            <div>
                                <label for="subject"
                                    class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                <input type="text" id="subject" x-model="formData.subject"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"
                                    placeholder="How can we help you?">
                            </div>
                            <div>
                                <label for="message"
                                    class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                <textarea id="message" rows="5" x-model="formData.message"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none"
                                    placeholder="Tell us about your workshop and requirements..."></textarea>
                            </div>
                            <button type="submit"
                                class="w-full bg-indigo-600 text-white py-3 px-6 rounded-lg font-semibold hover:bg-indigo-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white">
        <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <img class="h-8 w-auto" src="/logo/logo_new_mid.png" alt="ATMI Logo">
                        <span class="text-xl font-bold">CABINEX</span>
                    </div>
                    <p class="text-gray-300 mb-6 max-w-md">
                        Revolutionary smart tool management system with RFID integration, digital coin system, and
                        real-time monitoring for modern workshops.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M22.46 6c-.77.35-1.6.58-2.46.69.88-.53 1.56-1.37 1.88-2.38-.83.5-1.75.85-2.72 1.05C18.37 4.5 17.26 4 16 4c-2.35 0-4.27 1.92-4.27 4.29 0 .34.04.67.11.98C8.28 9.09 5.11 7.38 3 4.79c-.37.63-.58 1.37-.58 2.15 0 1.49.75 2.81 1.91 3.56-.71 0-1.37-.2-1.95-.5v.03c0 2.08 1.48 3.82 3.44 4.21a4.22 4.22 0 0 1-1.93.07 4.28 4.28 0 0 0 4 2.98 8.521 8.521 0 0 1-5.33 1.84c-.34 0-.68-.02-1.02-.06C3.44 20.29 5.7 21 8.12 21 16 21 20.33 14.46 20.33 8.79c0-.19 0-.37-.01-.56.84-.6 1.56-1.36 2.14-2.23z" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center hover:bg-gray-700 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                        </a>
                    </div>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Quick Links</h3>
                    <ul class="space-y-3">
                        <li><a href="#hero" class="text-gray-300 hover:text-white transition-colors">Home</a></li>
                        <li><a href="#specs"
                                class="text-gray-300 hover:text-white transition-colors">Specifications</a></li>
                        <li><a href="#features" class="text-gray-300 hover:text-white transition-colors">Features</a>
                        </li>
                        <li><a href="#manual"
                                class="text-gray-300 hover:text-white transition-colors">Documentation</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-3">
                        <li><a href="#team" class="text-gray-300 hover:text-white transition-colors">Our Team</a>
                        </li>
                        <li><a href="#contact" class="text-gray-300 hover:text-white transition-colors">Contact Us</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Help Center</a>
                        </li>
                        <li><a href="#" class="text-gray-300 hover:text-white transition-colors">Privacy
                                Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-12 pt-8 text-center">
                <p class="text-gray-400">&copy; 2025 ATMI Polytechnic - CABINEX. All rights reserved. Advanced
                    Technology Manufacturing Innovation.</p>
            </div>
        </div>
    </footer>

    <!-- Smooth Scrolling Script -->
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add scroll effect to navbar
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 100) {
                header.classList.add('backdrop-blur-sm', 'bg-white/95');
            } else {
                header.classList.remove('backdrop-blur-sm', 'bg-white/95');
            }
        });
    </script>
</body>

</html>
