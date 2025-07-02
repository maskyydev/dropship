@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="px-4 py-5 sm:px-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Proses Pembayaran</h3>
                <p class="mt-1 max-w-2xl text-sm text-gray-500">Selesaikan pembayaran untuk mengaktifkan langganan Anda.</p>
            </div>
            <div class="border-t border-gray-200 px-4 py-5 sm:p-0">
                <div class="sm:py-4 sm:px-6">
                    <div id="midtrans-payment" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result){
            window.location.href = "{{ route('payment.success') }}?order_id=" + result.order_id;
        },
        onPending: function(result){
            window.location.href = "{{ route('payment.success') }}?order_id=" + result.order_id;
        },
        onError: function(result){
            window.location.href = "{{ route('payment.failed') }}?order_id=" + result.order_id;
        },
        onClose: function(){
            // User closed the popup without finishing the payment
        }
    });
</script>
@endsection