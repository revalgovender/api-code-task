<?php

namespace App\Exercise;

use Illuminate\Support\Facades\Log;

class SoldItems
{
    protected float $max_payout_limit;

    public function __construct()
    {
        $this->max_payout_limit = env('MAX_PAYOUT_LIMIT');
    }

    public function convertToPayouts(array $soldItems): array
    {
        $payouts = [];
        $groupKeys = [];

        // Group by seller and currency.
        foreach ($soldItems as $soldItem) {
            if (!$this->validate($soldItem)) {
                Log::error('Item was invalid');
                continue;
            }

            // Prepare data.
            $sellerReference = $soldItem['seller-reference'];
            $priceAmount = $soldItem['price-amount'];
            $priceCurrency = $soldItem['price-currency'];
            $itemId = $soldItem['item-id'];
            $groupKey = $soldItem['seller-reference'] . '_' . $soldItem['price-currency'];

            if (!in_array($groupKey, $groupKeys)) {
                $groupKeys[] = $groupKey;
            }

            // Do grouping.
            if (isset($payouts[$groupKey])) {
                // Add amount to existing payout.
                $payouts[$groupKey]['amount'] += (float)$priceAmount;
                $payouts[$groupKey]['items'][] = (int)$itemId;
            } else {
                // Add new payout.
                $payouts[$groupKey] = [
                    'sellerReference' => $sellerReference,
                    'amount' => (float)$priceAmount,
                    'currency' => $priceCurrency,
                    'items' => [(int)$itemId]
                ];
            }
        }

        // Split payouts if amount exceeds limit.
        if (!empty($groupKeys)) {
            foreach ($groupKeys as $groupKey) {
                $totalAmount = $payouts[$groupKey]['amount'];
                if ($totalAmount > $this->max_payout_limit) {
                    // Split payout.
                    $numberOfPayoutsToCreate = ceil($totalAmount / $this->max_payout_limit);
                    $amountPerPayout = $totalAmount / $numberOfPayoutsToCreate;

                    for ($i = 1; $i <= $numberOfPayoutsToCreate; $i++) {
                        $payouts[$groupKey . '_' . $i] = $amountPerPayout;
                        $payouts[$groupKey . '_' . $i] = [
                            'sellerReference' => $payouts[$groupKey]['sellerReference'],
                            'amount' => $amountPerPayout,
                            'currency' => $payouts[$groupKey]['currency'],
                            'items' => $payouts[$groupKey]['items']
                        ];
                    }

                    // Unset original payout.
                    unset($payouts[$groupKey]);
                }
            }
        }

        return $payouts;
    }

    private function validate(mixed $soldItem): bool
    {
        // Check if fields present.
        if (!isset($soldItem['item-id']) ||
            !isset($soldItem['price-amount']) ||
            !isset($soldItem['price-currency']) ||
            !isset($soldItem['seller-reference'])) {
            return false;
        }

        // Only allow specific currencies.
        $allowedCurrencies = ['GBP', 'EUR', 'USD'];

        if (!in_array($soldItem['price-currency'], $allowedCurrencies)) {
            return false;
        }

        return true;
    }
}
