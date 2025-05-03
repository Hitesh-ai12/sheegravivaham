<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class CashfreeController extends Controller
{
    public function createPlan(Request $request)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-api-version' => '2022-09-01',
            'x-client-id' => env('CASHFREE_CLIENT_ID'),
            'x-client-secret' => env('CASHFREE_CLIENT_SECRET'),
        ])->post('https://sandbox.cashfree.com/pg/plans', [
            'plan_id' => 'silver_plus_800_1m',
            'plan_name' => 'Silver Plus Monthly',
            'plan_type' => 'PERIODIC',
            'plan_currency' => 'INR',
            'plan_recurring_amount' => 80000,
            'plan_max_amount' => 100000,
            'plan_max_cycles' => 12,
            'plan_intervals' => 1,
            'plan_interval_type' => 'MONTH',
            'plan_note' => 'Monthly Silver Plus Plan'
        ]);

        return response()->json([
            'status' => $response->status(),
            'body' => $response->json()
        ]);
    }
}
