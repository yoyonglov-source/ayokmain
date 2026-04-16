<?php

namespace App\Services;

class BookingService
{
    public function calculateTotal($basePrice, $venue)
    {
        // Mengambil setting asli dari database admin Anda
        $appFee = 5000; 
        $pgFeeEstimate = 4000;

        // Logic Bearer berdasarkan setting admin
        // Jika fee_mode adalah 'addon', maka customer yang bayar. Selain itu owner.
        $appFeeBearer = ($venue->fee_mode === 'addon') ? 'customer' : 'owner';
        
        // Jika pg_fee_bearer adalah 'customer', maka customer yang bayar. Selain itu owner.
        $pgFeeBearer = ($venue->pg_fee_bearer === 'customer') ? 'customer' : 'owner';

        // 1. Hitung TOTAL BAYAR USER
        $totalUserPay = $basePrice;
        if ($appFeeBearer === 'customer') {
            $totalUserPay += $appFee;
        }
        if ($pgFeeBearer === 'customer') {
            $totalUserPay += $pgFeeEstimate;
        }

        // 2. Hitung NET TERIMA OWNER (Sesuai simulasi gambar admin Anda)
        $netToOwner = $basePrice;
        if ($appFeeBearer === 'owner') {
            $netToOwner -= $appFee;
        }
        if ($pgFeeBearer === 'owner') {
            $netToOwner -= $pgFeeEstimate;
        }

        return [
            'base_price' => $basePrice,
            'app_fee' => $appFee,
            'app_fee_bearer' => $appFeeBearer,
            'pg_fee' => $pgFeeEstimate,
            'pg_fee_bearer' => $pgFeeBearer,
            'total_user_pay' => $totalUserPay,
            'net_to_owner' => $netToOwner,
        ];
    }
}