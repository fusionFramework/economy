# Notifications

### Inventory
**item.gift**
When a user sends an item from the inventory to another user.

*Message variables: :other_user(receiver), :item_name, :username (who sent)*

### Shops
**shop.buy**
When a user buys an item in a user's shop

*Message variables: :shop_owner, :item_name, :price, :username (who bought)*

### Trades
**trades.delete**
Sent to users who made a bid on a lot that got deleted

*Message variables: :lot(lot number), :username (trade owner)*

**trades.bid**
When a user makes a bid on a lot

*Message variables: :lot(lot number), :bidder (user name of the bidder), :amount (amount of items bid), :items (array with item names)*

**trades.accept**
When a lot owner accepts a bid

*Message variables: :lot(lot number), :username (trade owner)*

**trades.reject**
When a lot owner rejects a bid

*Message variables: :lot(lot number), :username (trade owner)*

**trades.retract**
When a user retracts a bid

*Message variables: :lot(lot number), :username (retractor)*