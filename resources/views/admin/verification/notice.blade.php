@extends('layouts.admin')

@section('content')
<div class="bg-white shadow rounded-lg p-6 max-w-2xl mx-auto text-center">
    <div class="text-5xl mb-6">🛑</div>
    <h2 class="text-2xl font-bold mb-4">Akun Admin Belum Diverifikasi</h2>
    <p class="text-gray-600 mb-6">
        Akun admin Anda sedang menunggu verifikasi dari super admin. 
        Anda tidak dapat mengakses dashboard admin sampai akun Anda diverifikasi.
    </p>
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">
                    Super admin telah menerima notifikasi untuk memverifikasi akun Anda. 
                    Anda akan menerima email ketika akun Anda sudah diverifikasi.
                </p>
            </div>
        </div>
    </div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Logout
        </button>
    </form>
</div>
@endsection