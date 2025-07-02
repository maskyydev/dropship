@extends('layouts.admin')

@section('content')
@php
    use Carbon\Carbon;
    use App\Models\User;
@endphp

<div class="space-y-6">
    <div class="bg-white shadow rounded-lg p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold">Manajemen User</h2>
            <div class="flex space-x-2">
                <a href="#" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition-colors">
                    Tambah User
                </a>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Langganan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">Bergabung {{ $user->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $user->email }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $user->role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                                {{ User::$roles[$user->role] ?? $user->role }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($user->hasActiveSubscription())
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    Aktif sampai {{ $user->subscription_expiry->translatedFormat('d F Y') }}
                                </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    Tidak berlangganan
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Langganan Terbaru -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Langganan Terbaru</h2>
            <div class="space-y-4">
                @forelse($recentSubscriptions as $subscription)
                <div class="border-b pb-4 last:border-b-0 last:pb-0">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $subscription->user->name }}</span>
                        <span class="text-sm text-gray-500">{{ $subscription->plan_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-500">
                        <span>Mulai: {{ \Carbon\Carbon::parse($subscription->start_date)->translatedFormat('d F Y') }}</span>
                        <span>Berakhir: {{ \Carbon\Carbon::parse($subscription->end_date)->translatedFormat('d F Y') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-4">
                    Tidak ada data langganan
                </div>
                @endforelse
            </div>
        </div>

        <!-- Pembayaran Terbaru -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-4">Pembayaran Terbaru</h2>
            <div class="space-y-4">
                @forelse($recentPayments as $payment)
                <div class="border-b pb-4 last:border-b-0 last:pb-0">
                    <div class="flex justify-between">
                        <span class="font-medium">{{ $payment->user->name }}</span>
                        <span class="text-sm {{ $payment->status === 'success' ? 'text-green-600' : 'text-red-600' }}">
                            {{ ucfirst($payment->status) }}
                        </span>
                    </div>
                    <div class="text-sm text-gray-500">
                        <span>Metode: {{ ucfirst($payment->payment_method) }}</span> - 
                        <span>Rp{{ number_format($payment->amount, 0, ',', '.') }}</span>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-4">
                    Tidak ada data pembayaran
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
