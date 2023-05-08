# PHP API Code Task (4hr time limit)

## Contents
1. [Task Description](#task-description)
2. [My work](#my-work)
3. [Future Improvements](#future-improvements)

## Task Description
As a marketplace, we need to pay our sellers for every item that has been sold on our platform. In this task, you’ll be working with 2 main entities: Items to sell (products on the website) and Payouts instructions to send (bank transactions to seller accounts). Let’s assume that these entities have the following fields:

Item
- Name
- Price currency
- Price amount

Seller reference
- Payout
- Seller reference
- Amount
- Currency

### Goal
Expose an API endpoint that accepts a list of sold Items and creates Payouts for the sellers. There is a time limit of 4 hours for this task.

### Following limitations apply:
- We are only working with following currencies: USD, EUR, GBP
- A Payout is for a single seller, using a single currency.
- The total amount of the Payout should be equal to the total price of the products in the request.
- We should minimise the number of transactions as they incur a cost to the company; we should send as little Payouts per seller as possible.
- Every Payout amount should not exceed a certain limit (we can’t send a million with one single transaction); if a Payout exceeds said amount, we should split it. This amount may be regularly updated by the business team.
- Every Payout should be linked with at least one Item, so that we know exactly what Items have been paid out with each transaction
- Use Typescript/Node.JS or PHP. Using a framework such as Nest.JS or Symfony is encouraged.

## My Work
I completed the entire task using Laravel, MySQL and Docker.

Endpoint = http://localhost/api/v1/payout

### Example Request Body:
```json
[
    {
        "item-id": "1",
        "price-amount": "999999.00",
        "price-currency": "GBP",
        "seller-reference": "1"
    },
    {
        "item-id": "2",
        "price-amount": "500.00",
        "price-currency": "GBP",
        "seller-reference": "1"
    },
    {
        "item-id": "2",
        "price-amount": "500.00",
        "price-currency": "ds",
        "seller-reference": "1"
    }
]
```

### Example Response
```json
{
    "status": "success",
    "message": "Payouts were created successfully for valid sold items"
}
```

### Tests
```
~/code$ php artisan test

   PASS  Tests\Unit\VestiaireCollective\SoldItemsTest
  ✓ it will group payouts by seller and currency  
  ✓ it will skip sold items with missing fields  
  ✓ it will create new payout if amount exceeds limit  

   PASS  Api\v1\PayoutTest
  ✓ it will create payout  
  ✓ it will return 400 when items missing from payload  

  Tests:    5 passed (16 assertions)
  Duration: 0.67s
```

## Future Improvements
- Saving of multiple records can be done in one query to support large amounts of data
- Logging can be improved to be more detailed on which items caused issues
- More error handling with explicit errors
- `Exercise/SoldItems::convertToPayouts()`, breaks SRP, should be refactored to make it easier to read/modify
- More tests in general for functionality, edge cases and load required
- Use PHP 8 enums for currency values
- Seeders could have been created for initial database seeding instead of queries.
