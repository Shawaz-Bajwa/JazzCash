<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class JazzCashController extends Controller
{
    protected FirebaseService $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function initiatePayment(Request $request)
    {
        $firebaseUid = $request->input('firebase_uid');
        $firebaseUser = $this->firebase->getUserByFirebaseUid($firebaseUid);

        if (!$firebaseUser) {
            Log::warning('Firebase user not found: ' . $firebaseUid);
            return response()->json(['message' => 'Unauthorized user'], 401);
        }

        $txnRefNo = 'T' . time(); // Unique transaction ref
        $amount = number_format($request->amount, 2, '', ''); // Must be in paisa

        // Store mapping between transaction reference and user ID
        Cache::put("jazz_txn_user:{$txnRefNo}", $firebaseUid, now()->addHours(24));

        $fields = [
            "pp_Version" => "1.1",
            "pp_TxnType" => "MWALLET",
            "pp_Language" => "EN",
            "pp_MerchantID" => config('jazzcash.merchant_id'),
            "pp_Password" => config('jazzcash.password'),
            "pp_TxnRefNo" => $txnRefNo,
            "pp_Amount" => $amount,
            "pp_TxnCurrency" => config('jazzcash.currency'),
            "pp_TxnDateTime" => now()->format('YmdHis'),
            "pp_BillReference" => "billRef",
            "pp_Description" => "Test Transaction",
            "pp_ReturnURL" => config('jazzcash.return_url'),
            "pp_SecureHash" => "", // will generate later
            "ppmpf_1" => "923147851757",
        ];

        // Create secure hash
        $sorted = collect($fields)->sortKeys();
        $hashString = config('jazzcash.integrity_salt') . '&' . $sorted->implode('&');
        $fields['pp_SecureHash'] = hash_hmac('sha256', $hashString, config('jazzcash.integrity_salt'));

        $action_url = config('jazzcash.post_url');

        $html = '<form id="jazzcash_redirect_form" method="POST" action="' . $action_url . '">';
        foreach ($fields as $key => $value) {
            $html .= '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars($value) . '">';
        }
        $html .= '</form>';
        $html .= '<script>document.getElementById("jazzcash_redirect_form").submit();</script>';

        return response($html);
    }

    public function paymentResponse(Request $request)
    {
        $response = $request->all();
        $txnRefNo = $request->input('pp_TxnRefNo');
        $status = $response['pp_ResponseCode'] === '000' ? 'success' : 'failed';

        // Retrieve the user ID from cache using the transaction reference
        $userId = Cache::get("jazz_txn_user:{$txnRefNo}");

        if (!$userId) {
            Log::error("User ID not found for transaction: {$txnRefNo}");
            return redirect()->route('jazz.fail')
                ->with('error', 'Payment verification failed! User not found.')
                ->with('txn_ref', $txnRefNo ?? 'N/A');
        }

        $firebaseData = [
            'payment_status' => $status,
            'transaction_id' => $txnRefNo,
            'amount' => $request->input('pp_Amount') / 100,
            'payment_date' => now()->timestamp,
            'failure_reason' => $status === 'failed' ? $request->input('pp_ResponseMessage') : null,
        ];

        $orderId = $request->input('pp_BillReference');
        try {
            $this->firebase->updatePaymentStatus($userId, $orderId, $firebaseData);

            // Clean up the cache entry
            Cache::forget("jazz_txn_user:{$txnRefNo}");
        } catch (\Throwable $e) {
            Log::error("Firebase update exception: {$e->getMessage()}");
            return response()->json(['error' => 'Failed to update Firebase'], 500);
        }

        // Always log or store this for record-keeping
        Log::info('JazzCash Response:', [
            'userId' => $userId,
            'response' => $response
        ]);

        if ($response['pp_ResponseCode'] === '000') {
            return redirect()->route('jazz.success')
                ->with('message', 'Payment Successful!')
                ->with('txn_ref', $txnRefNo);
        } else {
            return redirect()->route('jazz.fail')
                ->with('error', 'Payment Failed!')
                ->with('response_message', $response['pp_ResponseMessage'] ?? 'Unknown Error')
                ->with('txn_ref', $txnRefNo ?? 'N/A');
        }
    }

    public function success()
    {
        return view('jazz.success');
    }

    public function fail()
    {
        return view('jazz.failure');
    }
}
