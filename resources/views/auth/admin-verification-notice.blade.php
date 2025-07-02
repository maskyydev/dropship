@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-6">
        {{-- Popup Modal Success --}}
        @if (session('success'))
            <div id="successModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
                <div class="bg-white p-6 rounded-lg shadow-lg text-center max-w-sm">
                    <div class="flex justify-center mb-4">
                        <svg class="w-16 h-16 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4 -4m-7 7a9 9 0 1 0 -9 -9a9 9 0 0 0 9 9z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-semibold text-gray-700 mb-2">Berhasil!</h2>
                    <p class="text-sm text-gray-600 mb-4">{{ session('success') }}</p>
                    <button onclick="redirectToDashboard()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                        OK
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-white p-10 rounded-lg shadow-md space-y-6">
            <div class="text-center">
                <h2 class="text-3xl font-extrabold text-gray-900">Verifikasi Email</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Kami telah mengirim kode verifikasi ke email <span class="font-medium">{{ $email }}</span>. Silakan masukkan kode tersebut di bawah ini.
                </p>
            </div>

            <form method="POST" action="{{ route('verify.code') }}" class="space-y-6">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">

                <div>
                    <label for="verification_code" class="sr-only">Kode Verifikasi</label>
                    <input id="verification_code" name="verification_code" type="text" required
                        class="appearance-none rounded-md relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Masukkan kode verifikasi">
                </div>

                <button type="submit"
                    class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Verifikasi
                </button>
            </form>

            <div class="text-center text-sm">
                <form method="POST" action="{{ route('verification.resend') }}">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Kirim ulang kode verifikasi
                    </button>
                </form>
            </div>

            <div class="flex items-center justify-between text-sm">
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">
                    Kembali ke halaman login
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="font-medium text-indigo-600 hover:text-indigo-500">
                        Login sebagai user lain
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- JS untuk redirect ke dashboard --}}
<script>
    function redirectToDashboard() {
        window.location.href = '/dashboard';
    }
</script>
@endsection
