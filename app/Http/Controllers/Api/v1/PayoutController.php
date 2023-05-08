<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Payout;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    protected Item $item;
    protected Payout $payout;

    public function __construct(Item $item, Payout $payout)
    {
        $this->item = $item;
        $this->payout = $payout;
    }

    public function create(Request $request): JsonResponse
    {
        $payouts = $request->all();

        if (empty($payouts)) {
            Log::warning('An application is sending requests without the body to api.');

            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'There were no items provided in the request',
                ],
                400
            );
        }

        try {
            $this->payout->saveMultipleFromSoldItems($payouts);
        } catch (Exception $exception){
            Log::warning('Something went wrong with saving items.');
            return response()->json(
                [
                    'status' => 'error',
                    'message' => $exception->getMessage(),
                ],
                $exception->getCode()
            );
        }

        return response()->json(
            [
                'status' => 'success',
                'message' => 'Payouts were created successfully for valid sold items',
            ],
            200
        );
    }
}
