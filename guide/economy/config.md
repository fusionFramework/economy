# Configuration

## Tasks

*No tasks*

## Cronjobs

**store:restock**
Run this task as a cronjob to ensure that your game's stores restock.

~~~
php {PATH TO GAME FOLDER}minion store:restock
~~~

## Events

**store.buy**
When an item is bought from the store

*Parameters: Model_User $user, Model_Item $item, $price*