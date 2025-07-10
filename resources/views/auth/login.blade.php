<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProList-APP | Company Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .bg-gradient-custom {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }
    </style>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden">
            <!-- Header with gradient -->
            <div class="bg-gradient-custom py-6 px-8 text-center">
                <div class="flex justify-center mb-4">
                    <div class="bg-white p-3 rounded-full shadow-md">
                        <i class="fas fa-tasks text-blue-600 text-2xl"></i>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-white">ProList-APP</h1>
                <p class="text-blue-100 mt-1">Internal Task Management System</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="px-8 pt-4 text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <!-- Login Form -->
            <form class="px-8 py-6" action="{{ route('login') }}" method="POST">
                @csrf

                <div class="mb-6">
                    <label for="email" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-envelope mr-2 text-blue-500"></i> Company Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 input-focus transition duration-200"
                        placeholder="your.name@company.com" required autofocus>
                    @error('email')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-lock mr-2 text-blue-500"></i> Password
                    </label>
                    <div class="relative">
                        <input type="password" id="password" name="password"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-blue-500 input-focus transition duration-200 pr-12"
                            placeholder="••••••••" required>
                        <button type="button" id="togglePassword"
                            class="absolute right-3 top-3 text-gray-500 hover:text-blue-600">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-4">
                    <input id="remember_me" type="checkbox" name="remember"
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                    <label for="remember_me" class="ml-2 block text-sm text-gray-900">
                        {{ __('Remember me') }}
                    </label>
                </div>

                <div class="mb-6">
                    <button type="submit"
                        class="w-full bg-gradient-custom text-white py-3 px-4 rounded-lg font-medium hover:opacity-90 transition duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                        <i class="fas fa-sign-in-alt mr-2"></i> Login to Dashboard
                    </button>
                </div>

                <div class="text-center text-sm text-gray-500">
                    <p>For authorized personnel only</p>
                    <p class="mt-1">© {{ date('Y') }} ProList-APP. All rights reserved.</p>
                </div>
            </form>
        </div>

    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (!email.includes('@')) {
                alert('Please enter a valid company email address');
                e.preventDefault();
            }

            if (password.length < 6) {
                alert('Password must be at least 6 characters');
                e.preventDefault();
            }
        });

        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordField = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);

            this.innerHTML = type === 'password'
                ? '<i class="fas fa-eye"></i>'
                : '<i class="fas fa-eye-slash"></i>';
        });
    </script>



</body>

</html>