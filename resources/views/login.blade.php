<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ATMI Student Portal - Login</title>
    @vite('resources/css/app.css')
</head>

<body>
    <div class="min-h-screen bg-gray-100 text-gray-900 flex justify-center">
        <div class="max-w-screen-xl m-0 sm:m-10 bg-white shadow sm:rounded-lg flex justify-center flex-1">
            <div class="lg:w-1/2 xl:w-5/12 p-6 sm:p-12">
                <div class="text-center">
                    <img src="https://storage.googleapis.com/devitary-image-host.appspot.com/15846435184459982716-LogoMakr_7POjrN.png"
                        class="w-32 mx-auto" />
                    <h2 class="text-xl font-semibold mt-2">ATMI Education Portal</h2>
                </div>
                <div class="mt-8 flex flex-col items-center">
                    <h1 class="text-2xl xl:text-3xl font-extrabold text-center">
                        Sign in to your account
                    </h1>

                    <div class="w-full flex-1 mt-8">
                        <div class="text-center mb-6">
                            <p class="text-gray-600 mb-2">Please use your institutional email</p>
                            <div class="flex justify-center space-x-2">
                                <span
                                    class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">@student.atmi.ac.id</span>
                                <span
                                    class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm">@atmi.ac.id</span>
                            </div>
                        </div>

                        <div class="flex flex-col items-center">
                            <a href="{{ route('login.google') }}"
                                class="w-full max-w-xs font-bold shadow-sm rounded-lg py-3 bg-indigo-600 text-white flex items-center justify-center transition-all duration-300 ease-in-out focus:outline-none hover:shadow focus:shadow-sm focus:shadow-outline hover:bg-indigo-700">
                                <div class="bg-white p-2 rounded-full">
                                    <svg class="w-4" viewBox="0 0 533.5 544.3">
                                        <path
                                            d="M533.5 278.4c0-18.5-1.5-37.1-4.7-55.3H272.1v104.8h147c-6.1 33.8-25.7 63.7-54.4 82.7v68h87.7c51.5-47.4 81.1-117.4 81.1-200.2z"
                                            fill="#ffffff" />
                                        <path
                                            d="M272.1 544.3c73.4 0 135.3-24.1 180.4-65.7l-87.7-68c-24.4 16.6-55.9 26-92.6 26-71 0-131.2-47.9-152.8-112.3H28.9v70.1c46.2 91.9 140.3 149.9 243.2 149.9z"
                                            fill="#ffffff" />
                                        <path
                                            d="M119.3 324.3c-11.4-33.8-11.4-70.4 0-104.2V150H28.9c-38.6 76.9-38.6 167.5 0 244.4l90.4-70.1z"
                                            fill="#ffffff" />
                                        <path
                                            d="M272.1 107.7c38.8-.6 76.3 14 104.4 40.8l77.7-77.7C405 24.6 339.7-.8 272.1 0 169.2 0 75.1 58 28.9 150l90.4 70.1c21.5-64.5 81.8-112.4 152.8-112.4z"
                                            fill="#ffffff" />
                                    </svg>
                                </div>
                                <span class="ml-4">
                                    Sign in with Google
                                </span>
                            </a>
                        </div>

                        <div class="mt-8 text-center">
                            <p class="text-xs text-gray-500">
                                By signing in, you agree to our
                                <a href="#" class="text-indigo-600 hover:underline">Terms of Service</a>
                                and
                                <a href="#" class="text-indigo-600 hover:underline">Privacy Policy</a>
                            </p>
                            <div class="mt-4 border-t pt-4">
                                <p class="text-sm text-gray-600">Need help? <a href="#"
                                        class="text-indigo-600 hover:underline">Contact support</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex-1 bg-indigo-100 text-center hidden lg:flex flex-col justify-center">
                <div class="m-12 xl:m-16 w-full bg-contain bg-center bg-no-repeat"
                    style="background-image: url('https://storage.googleapis.com/devitary-image-host.appspot.com/15848031292911696601-undraw_designer_life_w96d.svg'); height: 60%;">
                </div>
                <div class="px-12 pb-12">
                    <h3 class="text-2xl font-bold text-indigo-900 mb-4">ATMI Education Portal</h3>
                    <p class="text-indigo-800">Access your academic resources, course materials, and institutional
                        services with your ATMI account.</p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
