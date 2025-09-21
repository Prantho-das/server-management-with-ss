<x-layouts.guest>
    <div class="relative flex items-center justify-center w-full h-screen bg-gray-900 overflow-hidden">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-50"></div>
            <div class="absolute inset-0 bg-grid-pattern opacity-20"></div>
        </div>
        <div class="relative text-center text-white p-8">
            <h1 class="text-6xl font-bold tracking-tight sm:text-7xl">
                <span class="block">ServerMan</span>
            </h1>
            <p class="mt-4 text-xl max-w-2xl mx-auto">
                The ultimate control panel for developers and DevOps.
                Streamline your server management, automate your deployments, and monitor your applications with ease.
            </p>
            <div class="mt-10">
                <a href="/admin" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition">
                    Launch Panel
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>

    <!-- Features Section -->
    <section class="py-20 bg-gray-800 text-white">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold text-center mb-12">Features Designed for You</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <div class="text-center p-6 bg-gray-700 rounded-lg shadow-lg">
                    <div class="text-indigo-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-1.25-3M15 10V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v5m-4 6h16a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v7a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4">Automated Deployments</h3>
                    <p class="text-gray-300">Deploy your applications with ease using pre-defined templates and custom scripts.</p>
                </div>
                <div class="text-center p-6 bg-gray-700 rounded-lg shadow-lg">
                    <div class="text-indigo-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4">Comprehensive Monitoring</h3>
                    <p class="text-gray-300">Keep an eye on your server's health, processes, and services with detailed metrics.</p>
                </div>
                <div class="text-center p-6 bg-gray-700 rounded-lg shadow-lg">
                    <div class="text-indigo-400 mb-4">
                        <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-2xl font-semibold mb-4">Package Management</h3>
                    <p class="text-gray-300">Install and manage software packages on your servers directly from the panel.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gray-900 text-white text-center">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-4xl font-extrabold mb-6">Ready to Take Control of Your Servers?</h2>
            <p class="text-xl mb-8">Join ServerMan today and experience effortless server management.</p>
            <a href="/admin" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-8 rounded-lg text-lg transition">
                Launch Panel
            </a>
        </div>
    </section>

    <!-- Footer Section -->
    <footer class="bg-gray-900 text-gray-400 py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} ServerMan. All rights reserved.</p>
            <div class="mt-4">
                <a href="#" class="text-gray-400 hover:text-white mx-2">Privacy Policy</a>
                <span class="text-gray-600">|</span>
                <a href="#" class="text-gray-400 hover:text-white mx-2">Terms of Service</a>
            </div>
        </div>
    </footer>

<style>
    .bg-grid-pattern {
        background-image: linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
        background-size: 2rem 2rem;
    }
</style>
