# User items

This part of the guide discusses items that the user has, this means items that were loaded as a `Model_User_Item`.

You can retrieve user items in several ways:

 - [By location](items/helper#how-to-retrieve-items-by-location)
 - [By checking if the user has it](items/helper#check-if-the-user-has-the-item)
 - By loading it directly

~~~
// Load the user item with id 1
$item = ORM::factory('User_Item', 1);

// Load the item by name and location from user with 1 as id
$item = ORM::Factory('User_Item')
	->where('item.name', '=', 'Apple')
	->where('user_item.location', '=', 'inventory')
	->where('user_id', '=', 1)
	->find();
~~~

[!!] User info and Item info will always be loaded alongside your User item data

This means that if you need any information stored in the Item model you can access it through `$item->item`, the same
goes for user info `$item->user`.

Read [this article](https://github.com/jheathco/kohana-orm/wiki) if you're not too familiar with Kohana's ORM and relations.

## Item stacks

To save space in the database we don't store user items individually, instead the same items get stored in stacks.
You can always check how many items are stored in a stack by calling `$item->amount`

To modify the amount the `Model_User_Item` class has a method called `amount`, which works like this:

### Parameters

|Parameter |Description                                                                      |
|----------|---------------------------------------------------------------------------------|
|$type     |The value can be '+' or 'add' when increasing, '-' or 'subtract' when decreasing |
|$amount   |How many copies from this stack should be added or substracted (defaults to 1)   |

The method will return `FALSE` if it's unable to add or subtract the amount (if `$amount` isn't a valid number or if `$amount` is more than
there's present), otherwise it will return `TRUE`

### Examples

It can't get simpler than this:

~~~
// Add 3 copies
$item->amount('+', 3);
$item->amount('add', 3);

// Remove 2 copies
$item->amount('-', 2);
$item->amount('subtract', 2);
~~~

I would, however, advise to check if subtracting works:

~~~
// Remove 2 copies
$remove = $item->amount('-', 2);

if($remove == false)
{
	// Oops, did not work out as planned
}
~~~

## Moving the item to another location

Items don't just stay in one point, some stay in the inventory, some in the user's shop, some go to trades, they can go anywhere.
To keep track of the item stacks a nifty little method was added called `move` that helps you to easily do this without having to worry too much.

This method returns `false` if you're trying to move more items than the user has,
otherwise it will return the newly created/updated `Model_User_Item`.

### Parameters
|Parameter      |Description                                                                                            |
|--------------|--------------------------------------------------------------------------------------------------------|
|$location     |Where to move the item to (defaults to inventory)                                                       |
|$amount       |How many copies from this stack should be moved (defaults to 1)                                         |
|$single_stack |Should it automatically become a new item stack and not be added to an existing one? (defaults to true) |
|$parameter_id |Should parameter_id be set during the move you should provide an integer                                |

[!!] `$amount` can be filled with '*' to move all copies to the new location.

### Examples

Let's say we currently have the *Apple* loaded that's located in the user's *inventory*, let's move all of them to the user's safe:

~~~
$item->move('safe', '*');
~~~

Now let's move 5 copies to the user's shop, we'll have to check if it's possible (the user might not have 5 apples)

~~~
$move = $item->move('shop', 5);

if($move == false)
{
	RD::error('You don\'t have 5 apples to move to your shop');
}
else
{
	RD::success('5 apples were placed in your shop');
}
~~~

What if the user just created an auction and wants 1 apple to be assigned to the auction?
We'll have to assign the auction's id to the item's parameter_id.

This also means that the items should be stored in seperate stacks, since other stacks will be assigned to other auctions.

~~~
// do the auction creation

$move = $item->move('auction', 1, FALSE, $auction->id);

if($move == false)
{
	RD::error('You don\'t have an apple to put up for auction');
}
else
{
	RD::success('You\'ve successfully created an auction');
}
~~~

## Transfer the item to another user

Items don't always stay in one account, items can get transfered as gifts, after winning an auction or trade,...

Transfering an item is simple with this method, it will only transfer items that aren't locked a user's account
(when you put transferable to false in the admin when creating the item).

This method returns `false` if you're trying to transfer more items than the user has,
otherwise it will return the newly created/updated `Model_User_Item`.

### Parameters
|Parameter |Description                                                           |
|----------|----------------------------------------------------------------------|
|$user     |A `Model_User` instance of the user you want to send it to            |
|$amount   |How many copies from this stack should be transfered (defaults to 1)  |

[!!] `$amount` can be filled with '*' to transfer all copies to the new user.

### Exceptions

|Class thrown           |Description                                                                                    |
|-----------------------|-------------------------------------|
|Item_Exception         | When the item is not transferable   |

### Examples

Let's move 5 copies of the loaded item to the currently logged in user

~~~
$item->transfer(Fusion::$user, 5);
~~~

That was easy, but let's make sure the transfer is successful

~~~
try {
	$transfer = $item->transfer(Fusion::$user, 5);

	// The amount is less than 5
	if($transfer == false)
	{
		RD::error('You don\'t have :item', [':item' => $item->item->name(5)]);
	}
	else
	{
		RD::success(':item was transfered to :user', [':item' => $item->item->name(5), ':user' => Fusion::$user->username]);
	}
}
catch(Item_Exception $e)
{
	// Item is not transferable
	RD::error($e->getMEssage());
}

~~~

## How to retrieve the item's name

There are several ways to access an item's name.

If you only need the name of the item as defined in the admin you should call

	// would return apple
	$item->item->name

If you want the item's name prefixed with its amount you should call

	// would return 5 apples if the stack's amount is 5
	$item->name()

If you want to define the amount yourself you should call this

	// would return 3 apples
	$item->item->name(3)

	// would return 1 apple
	$item->item->name(1)

## How to retrieve the item's image

If you need to access the item's image URL you can just call

	$item->img();