<?php

namespace Tests\Unit\Exercise;

use App\Exercise\SoldItems;
use Tests\TestCase;

class SoldItemsTest extends TestCase
{
    protected SoldItems $soldItems;

    public function setUp(): void
    {
        parent::setUp();
        $this->soldItems = new SoldItems();
    }

    public function test_it_will_group_payouts_by_seller_and_currency(): void
    {
        // Arrange.
        $sellerOne = '24';
        $gbp = 'GBP';
        $eur = 'EUR';
        $soldItems = [
            [
                'item-id' => '1',
                'price-amount' => '200.00',
                'price-currency' => $gbp,
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '2',
                'price-amount' => '500.00',
                'price-currency' => $gbp,
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '3',
                'price-amount' => '600.00',
                'price-currency' => $eur,
                'seller-reference' => $sellerOne,
            ],
        ];
        $sellerCurrencyReferenceGbp = $sellerOne . '_' . $gbp;
        $sellerCurrencyReferenceEur = $sellerOne . '_' . $eur;

        // Act.
        $result = $this->soldItems->convertToPayouts($soldItems);

        // Assert.
        $this->assertCount(2, $result);
        $this->assertEquals(700, $result[$sellerCurrencyReferenceGbp]['amount']);
        $this->assertEquals('GBP', $result[$sellerCurrencyReferenceGbp]['currency']);
        $this->assertEquals(600, $result[$sellerCurrencyReferenceEur]['amount']);
        $this->assertEquals('EUR', $result[$sellerCurrencyReferenceEur]['currency']);
    }

    public function test_it_will_skip_sold_items_with_missing_fields(): void
    {
        // Arrange.
        $sellerOne = '24';
        $gbp = 'GBP';
        $eur = 'EUR';
        $soldItemsWithMissingFields = [
            [
                'price-amount' => '200.00',
                'price-currency' => $gbp,
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '2',
                'price-currency' => $gbp,
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '3',
                'price-amount' => '600.00',
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '4',
                'price-amount' => '600.00',
                'price-currency' => $eur,
            ],
        ];

        // Act.
        $result = $this->soldItems->convertToPayouts($soldItemsWithMissingFields);

        // Assert.
        $this->assertEmpty($result);
    }

    public function test_it_will_create_new_payout_if_amount_exceeds_limit(): void
    {
        // Arrange.
        $sellerOne = '24';
        $gbp = 'GBP';
        $eur = 'EUR';
        $soldItems = [
            [
                'item-id' => '1',
                'price-amount' => '999999.00',
                'price-currency' => $gbp,
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '2',
                'price-amount' => '500.00',
                'price-currency' => $gbp,
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '2',
                'price-amount' => '500.00',
                'price-currency' => $eur,
                'seller-reference' => $sellerOne,
            ],
            [
                'item-id' => '2',
                'price-amount' => '500.00',
                'price-currency' => $eur,
                'seller-reference' => $sellerOne,
            ],
        ];

        // Act.
        $result = $this->soldItems->convertToPayouts($soldItems);

        // Assert.
        $this->assertCount(3, $result);
        $this->assertEquals('500249.5', $result['24_GBP_1']['amount']);
        $this->assertEquals('500249.5', $result['24_GBP_2']['amount']);
        $this->assertEquals('1000.0', $result['24_EUR']['amount']);
    }
}
