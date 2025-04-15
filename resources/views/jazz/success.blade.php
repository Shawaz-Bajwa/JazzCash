@extends('layouts.app')

@section('title', 'Payment Success')

@section('content')
    <div class="container py-5">
        <div class="alert alert-success text-center rounded-4 shadow-sm">
            <h1 class="display-5">🎉 Payment Successful!</h1>
            <p class="lead mt-3">{{ session('message') }}</p>
            <hr>
            <p class="mb-0">Thank you for your payment. Your transaction reference number is:</p>
            <strong>{{ session('txn_ref') ?? 'N/A' }}</strong>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-outline-primary">Go to Home</a>
        </div>
    </div>
@endsection
