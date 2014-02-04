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
