# Fusion Framework Economy module

Contains an item engine, along with:
 - Inventory
 - Safety deposit box
 - User shops
 - Auctions
 - Trades
 - Stores

## Tasks

## Cronjobs

**store:restock**
Run this task as a cronjob to ensure that your game's stores restock.

```php {PATH TO GAME FOLDER}minion store:restock```

## Notifications

**item.gift**
When sending a user an item from the inventory.

Message variables: :other_user(receiver), :item_name, :username (who sent)

**shop.buy**
When a user buys an item in your shop

Message variables: :shop_owner, :item_name, :price, :username (who bought)

**trades.delete**
When a lot is deleted after bidding on it

Message variables: :lot(lot number), :username (trade owner)

**trades.bid**
When a bid has been made on a lot

Message variables: :lot(lot number), :bidder (user name of the bidder), :amount (amount of items bid), :items (array with item names)

**trades.accept**
When a bid is accepted

Message variables: :lot(lot number), :username (trade owner)

**trades.reject**
When a bid is rejected

Message variables: :lot(lot number), :username (trade owner)

**trades.retract**
When a bid is retracted

Message variables: :lot(lot number), :username (retractor)