<?php

namespace Api\v1;

use App\Models\Item;
use App\Models\Seller;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_will_create_payout(): void
    {
        // Arrange.
        $itemOne = Item::factory()->create();
        $itemTwo = Item::factory()->create();
        $seller = Seller::factory()->create();
        $payload = [
            [
                'item-id' => $itemOne['id'],
                'price-amount' => '200.00',
                'price-currency' => 'GBP',
                'seller-reference' => $seller['id'],
            ],
            [
                'item-id' => $itemTwo['id'],
                'price-amount' => '500.00',
                'price-currency' => 'GBP',
                'seller-reference' => $seller['id'],
            ],
        ];

        // Act.
        $response = $this->post('/api/v1/payout', $payload);

        // Assert.
        $this->assertDatabaseCount('payouts', 1);
        $this->assertDatabaseHas('payouts', [
            'seller_reference' => $seller['id'],
            'amount' => 700.00,
            'currency' => 'GBP',
        ]);
        $response->assertStatus(200);
    }

    public function test_it_will_return_400_when_items_missing_from_payload(): void
    {
        // Arrange.
        $payload = [];

        // Act.
        $response = $this->post('/api/v1/payout', $payload);
        $responseMessage = $response->json();

        // Assert.
        $response->assertStatus(400);
        $this->assertArrayHasKey('message', $responseMessage);
        $this->assertEquals('There were no items provided in the request', $responseMessage['message']);
    }
}
