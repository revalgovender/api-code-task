<?php

namespace App\Models;

use App\Exercise\SoldItems;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

class Payout extends Model
{
    use HasFactory;
    protected $fillable = ['seller_reference','amount','currency'];
    protected SoldItems $soldItems;

    public function __construct(SoldItems $soldItems)
    {
        parent::__construct();
        $this->soldItems = $soldItems;
    }

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }

    public function saveMultipleFromSoldItems(array $soldItems): void
    {
        $payouts = $this->soldItems->convertToPayouts($soldItems);

        $this->saveMultiple($payouts);
    }

    public function saveMultiple(array $payouts): void
    {
        foreach ($payouts as $payout) {
            $payoutModel = new Payout($this->soldItems);
            $payoutModel->setAttribute('seller_reference', $payout['sellerReference']);
            $payoutModel->setAttribute('amount', $payout['amount']);
            $payoutModel->setAttribute('currency', $payout['currency']);
            $payoutModel->save();
            $payoutModel->items()->attach($payout['items']);
        }
    }
}
