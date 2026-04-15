<?php

namespace App\Services;

class BookingService
{
    public function calculateTotal($basePrice, $venueSettings, $pgFeeEstimate = 4000)
    {
        $appFee = $venueSettings->platform_fee; // Diambil dari tabel venues
        $feeMode = $venueSettings->fee_mode; // 'addon' atau 'deduct'
        $pgBearer = $venueSettings->pg_fee_bearer; // 'customer' atau 'owner'

        // 1. Hitung apa yang harus dibayar User
        $userTotal = $basePrice;
        if ($feeMode === 'addon') {
            $userTotal += $appFee;
        }
        if ($pgBearer === 'customer') {
            $userTotal += $pgFeeEstimate;
        }

        // 2. Hitung apa yang diterima Owner (Net)
        $ownerNet = $basePrice;
        if ($feeMode === 'deduct') {
            $ownerNet -= $appFee;
        }
        if ($pgBearer === 'owner') {
            $ownerNet -= $pgFeeEstimate;
        }

        return [
            'base_price' => $basePrice,
            'app_fee' => $appFee,
            'app_fee_bearer' => ($feeMode === 'addon') ? 'customer' : 'owner',
            'pg_fee' => $pgFeeEstimate,
            'pg_fee_bearer' => $pgBearer,
            'total_user_pay' => $userTotal,
            'net_to_owner' => $ownerNet,
        ];
    }
}