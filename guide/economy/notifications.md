# Notifications

These are the notifications that are sent from certain actions in this module.

## Auctions
**auctions.bid**
When a bid is made on the user's auction

*Message variables: :auction_id, :username (bidder)*

**auctions.outbid**
Sent when another users outbids

*Message variables: :auction_id, :username (new bidder), :owner(auction owner)*

**auctions.auto_buy**
Informs the auction's owner when a user auto buys

*Message variables: :auction_id, :username (bidder)*

## Inventory
**item.gift**
When a user sends an item from the inventory to another user.

*Message variables: :other_user(receiver), :item_name, :username (who sent)*

## Shops
**shop.buy**
When a user buys an item in a user's shop

*Message variables: :shop_owner, :item_name, :price, :username (who bought)*

## Trades
**trades.delete**
Sent to users who made a bid on a lot that got deleted

*Message variables: :lot(lot number), :username (trade owner)*

**trades.bid**
When a user makes a bid on a lot

*Message variables: :lot(lot number), :bidder (username of the bidder), :amount (amount of items bid), :items (array with item names)*

**trades.accept**
When a lot owner accepts a bid

*Message variables: :lot(lot number), :username (trade owner)*

**trades.reject**
When a lot owner rejects a bid

*Message variables: :lot(lot number), :username (trade owner)*

**trades.retract**
When a user retracts a bid

*Message variables: :lot(lot number), :username (retractor)*