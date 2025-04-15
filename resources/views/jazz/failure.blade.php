@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
    <div class="container py-5">
        <div class="alert alert-danger text-center rounded-4 shadow-sm">
            <h1 class="display-5">⚠️ Payment Failed</h1>
            <p class="lead mt-3">{{ session('error') }}</p>
            <hr>
            <p class="mb-0">
                {{ session('response_message') ?? 'Something went wrong. Please try again later.' }}
            </p>
            <strong class="d-block mt-2">Transaction Ref: {{ session('txn_ref') ?? 'N/A' }}</strong>
        </div>

        <div class="text-center mt-4">
            <a href="{{ route('home') }}" class="btn btn-outline-danger">Try Again</a>
        </div>
    </div>
@endsection
