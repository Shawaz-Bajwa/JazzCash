@extends('layouts.app')

@section('title', 'Make a Payment')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-4">
                    <div class="card-header bg-primary text-white fs-5 fw-bold">
                        Make a Payment
                    </div>

                    <div class="card-body">
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('jazz.payment.initiate') }}">
                            @csrf


                            <div class="mb-3">
                                <label for="firebase_uid" class="form-label">Firebase User Id</label>
                                <input type="text" class="form-control" id="firebase_uid" name="firebase_uid" required>
                            </div>

                            <div class="mb-3">
                                <label for="mobile" class="form-label">JazzCash Mobile Number</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" required
                                    pattern="923\d{9}" placeholder="923xxxxxxxxx">
                                <div class="form-text">Enter number in format: 923XXXXXXXXX</div>
                            </div>

                            <div class="mb-3">
                                <label for="amount" class="form-label">Amount (PKR)</label>
                                <input type="number" class="form-control" id="amount" name="amount" required
                                    min="1" step="any">
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    Proceed to Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
