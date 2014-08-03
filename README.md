Notifynder
==========

[![Build Status](https://travis-ci.org/fenos/Notifynder.svg?branch=master)](https://travis-ci.org/fenos/Notifynder)
[![ProjectStatus](http://stillmaintained.com/fenos/Notifynder.png)](http://stillmaintained.com/fenos/Notifynder)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fenos/Notifynder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fenos/Notifynder/?branch=master)
[![Total Downloads](https://poser.pugx.org/fenos/notifynder/downloads.svg)](https://packagist.org/packages/fenos/notifynder)
[![License](https://poser.pugx.org/fenos/Notifynder/license.png)](https://packagist.org/packages/fenos/Notifynder)
[![Latest Stable Version](https://poser.pugx.org/fenos/notifynder/v/stable.png)](https://packagist.org/packages/fenos/notifynder)

Notifynder is a package that implement on your application a management system of internal notifications. Similar to facebook notifications.
With this solid API you will implent the notifications system in no time.

- - -
####What's new####

#####Release 1.4.0#####
* [Advanced Categories](#advanced-categories)

#####Release 1.4.5#####
* [Notifications Handler](#notifications-handler)

#####Release 1.6.0#####
* [Notifynder Polymorphic](#notifynder-polymorphic)

#####Release 2.0.0#####
* Re Built from scratch the library, All the futures are compatible with the older versions
* **Only the handler has been completely changed** Will be compatible with >= 2.0
* New Futures:
    * [Artisan Commands](#artisan-commands)
    * [Queue Notifications](#notifications-queue)
    * [Group Notifications](#notifications-groups)
    * [New Notifications Handler](#notifications-handler)
    * [New Senders methods](#send)
* Good architecture of the classes
* Heavily Tested with Unit and Integration

- - -

* [Installation](#installation)
* [Documentation](#documentation)
* [Artisan Commands](#artisan-commands) (new 2.0)
* [Notification Categories](#notification-categories)
    * [Add](#add-categories)
    * [Update](#update-categories)
    * [Delete](#delete-categories)
    * [Advanced Categories](#advanced-categories)
* [Send Notification/s](#send-notification-s)
    * [Send](#send) (new 2.0)
    * [Send single notification](#send-single-notification)
    * [Send multiple notifications](#send-multiple-notifications)
* [Read Notification/s](#read-notifications)
    * [Read one](#read-one)
    * [Read All](#read-all)
    * [Read Limit](#read-limit)
* [Retrive Notifications](#retrive-notifications)
    * [Get not read](#get-not-read)
    * [Get All](#get-all)
* [Delete Notification/s](#delete-notifications)
    * [Delete single](#delete-single)
    * [Delete All](#delete-all)
    * [Delete Limit](#delete-limit)
* [Notifications Handler](#notifications-handler) (new 2.0)
   * [Listeners](#listeners) (new 2.0)
   * [Fire Methods](#fire-methods) (new 2.0)
   * [Handler Class](#handler-class) (new 2.0)
   * [Delegate Events](#delegate-events) (new 2.0)
* [Group Notifications](#group-notifications) (new 2.0)
* [Method Category()](#method-category)
* [Notifications Queue](#notifications-queue) (new 2.0)
* [Notifynder Polymorphic](#notifynder-polymorphic)
* [Translations](#translations)
* [Extends Model Class](#extend-the-model-class)
* [Upgrade Release](#upgrade-release)

## Installation ##

### Step 1 ###

Add it on your composer.json

~~~
"fenos/notifynder": "2.0.*"
~~~

and run **composer update**


### Step 2 ###

Add the following string to **app/config/app.php**

**Providers array:**

~~~
'Fenos\Notifynder\NotifynderServiceProvider'
~~~

**Aliases array:**

~~~
'Notifynder'    => 'Fenos\Notifynder\Facades\Notifynder'
~~~

### Step 3 ###

#### Migration ####

Make sure that your settings on **app/config/database.php** are correct, then make the migration typing:

~~~
php artisan migrate --package="fenos/notifynder"
~~~

### Step 4 ###

#### Connecting to the user Model ####

Notifynder use a user model to get informations from who sent the notification and who receive the notification, so if you have a different name of User model, publish the configurations files and fill the path of your model.

~~~
php artisan config:publish fenos/notifynder
~~~

That's it! we have done with the installation!

- - -

## Documentation ##

### Artisan Commands ###

Once you installed Notifynder you should see 4 new commands

* php artisan notifynder:category-add
* php artisan notifynder:category-delete
* php artisan notifynder:group-add
* php artisan notifynder:group-add-category

#### Category Commans ####

**Add Category**

This command add a category in your database it has 2 arguments

- name of the category : name.category
- text of the category : "Text of the category"

~~~
php artisan notifynder:category-add name.category "text of the category"
~~~

**Delete Category**

This command will delete a category created it has 1 argument

- name of the category : name.category

~~~
php artisan notifynder:category-delete name.category
~~~

#### Groups Commands ####

**Group Add**

This command will create a group in your database it has 1 argument

- name of the group : name.group

~~~
php artisan notifynder:group-add name.group
~~~

**Group Add Category**

It add a category in a group (In your pivot table) it has 1 argument and 1 option

- argument - name of the group : name.group
- option - categories : --categories="category.A, categoryB"

~~~
php artisan notifynder:group-add-category name.group --categories="category.A, categoryB"
~~~

- - -

### Notification Categories ###

The notification categories are just the body of the your notification. As first release we have only 2 fields availables:

- name
- text

### Add categories ###

Let's say an example, We want to add a **Body Text** notification that says: **News recently updated from the city of London**

Well for create that it's easy:

~~~
Notifynder::addCategory('londonNews','News recently updated from the city of London');
~~~

Like so we have added a category in our database.
As First parameter we will add the key of the notification, as second paramenter we add the body.

### Update categories ###

~~~
$new_informations = array( 'body' => 'New body of the category' );

Notifynder::updateCategory($new_informations,1);
~~~

As first parameter you pass an array with keys of the fields that you want update and the value with the new content.
As second paramenter you pass the id of the current category to update.

**Second option**

How second option if you don't want hardcode the id of the category you can update your row simply like so:

~~~
try
{
    $new_informations = array( 'body' => 'New body of the category' );

    Notifynder::category('londonNews')->updateCategory($new_informations);
}
catch(Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException $e)
{

    // category not found

}
~~~

On the category method you'll pass the name of the category that you want update and on the update method just the informations.

### Delete categories ###

For the delete a category just:

~~~
Notifynder::deleteCategory(1);
~~~

As first parameter you pass the id of the category you want to delete or again you can use the method `category` and it will think about to get the id for you just passing the name

~~~
try
{
    Notifynder::category('londonNews')->deleteCategory();
}
catch(Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException $e)
{

    // category not found

}
~~~

### Advanced Categories ###

**In the release 1.4.0 the migration file has changed adding one more column called "extra" to the table notifications**

The advanced categories permit to have a really nice body text inseting "special values". this "special values" will be dynamic and parsed from notifynder.
Let's see an example for understand better the logic:

We want to have a notificaton that says: `User X has invited you on the event NOTIFYNDER`/

But we know that user `X` and `NOTIFYNDER` will change for different users and events.
For achieve this result when you create the category see the code example:

~~~
Notifynder:addCategory('inviteEvent','User {user.name} has invited you on the event {extra}'); // that's it!
~~~

The values between `{}` are the specials values but how you saw on the example I used the first one with the `dot` annotation and the second one without it why?

This two values are really different, because the first one get the value from the current relation of the user table so you can use all the felds about the user that sent the notification, example: `user.surname`.
Instead the `{extra}` value a static special value and it will be replaced from the value `extra` in your table notifications.
So for now you are limited to have as many as you want for relation values and 1 **extra** value on your body text. on the future release this limit will be deleted.

- - -

### Send Notification / s ###

Notifynder allow to send a single notification or multiple notifications at once. Let's see how:

#### Send ####

With the new version 2.0 the only method you need to use is the `send` method. Doesn't matter if is a single o multiple
notifications notifynder will take care of it. The others methods are still available for the older versions.

~~~

$notification_information = array(

    'from_id'     => 1, // ID user that send the notification
    'to_id'       => 2, // ID user that receive the notification
    'category_id' => 1, // category notification ID
    'url'         => 'www.urlofnotification.com', // Url of your notification
);

Notifynder::send($notification_information); // it just send!

~~~

#### Send Single Notification ####

~~~

$notification_information = array(

    'from_id'     => 1, // ID user that send the notification
    'to_id'       => 2, // ID user that receive the notification
    'category_id' => 1, // category notification ID
    'url'         => 'www.urlofnotification.com', // Url of your notification
);

Notifynder::sendOne($notification_information); // it just send!

~~~

But remember you can always use the method `category()` and don't hard code the category id!

~~~
try
{

    $notification_information = array(

        'from_id'     => 1, // ID user that send the notification
        'to_id'       => 2, // ID user that receive the notification
        'url'         => 'www.urlofnotification.com', // Url of your notification
    );

    Notifynder::category('londonNews')->sendOne($notification_information); // it just send!
}
catch(Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException $e)
{

    // category not found

}
~~~


#### Send multiple notifications ####

Now it's time to send multiple notification at once! The only thing you need to keep attention is that `created_at` and `updated_at` are not updated automatically so just put it on your array :)

~~~
$notification_information = array(

    array (
        'from_id'     => 1, // ID user that send the notification
        'to_id'       => 2, // ID user that receive the notification
        'category_id' => 1, // ID category
        'url'         => 'www.urlofnotification.com', // Url of your notification
        'extra'       => 'extra value' // extra value that will be replace if present {extra} in the category body
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now()
    ),

    array (
        'from_id'     => 1, // ID user that send the notification
        'to_id'       => 4, // ID user that receive the notification
        'category_id' => 2, // ID category
        'url'         => 'www.urlofnotification.com', // Url of your notification
        'extra'       => 'extra value' // extra value that will be replace if present {extra} in the category body
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now()
    )
);


Notifynder::sendMultiple($notification_information);
~~~

In this method the `category()` method will not work on chaining, but instead let's see how can you use it here!

~~~
$notification_information = array(

    array => (
        'from_id'     => 1, // ID user that send the notification
        'to_id'       => 2, // ID user that receive the notification
        'category_id' => Notifynder::category('londonNews')->id(), // Will give you the notification ID
        'url'         => 'www.urlofnotification.com', // Url of your notification
        'extra'       => 'extra value' // extra value that will be replace if present {extra} in the category body
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now()
    ),

    array => (
        'from_id'     => 1, // ID user that send the notification
        'to_id'       => 4, // ID user that receive the notification
        'category_id' => Notifynder::category('londonNews')->id(), // Will give you the notification ID
        'url'         => 'www.urlofnotification.com', // Url of your notification
        'extra'       => 'extra value' // extra value that will be replace if present {extra} in the category body
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now()
    )
);


Notifynder::sendMultiple($notification_information);
~~~

### Read Notification/s ###

Now is time to make it read when the user read the notification/s! Let's see how!

#### Read One ####

With this method will make read a single notification giving the id of it.
if you give an ID that doesn't exist prepare yourself to catch an exception

~~~
try
{

    Notifynder::readOne($notification_id); // That's It
}
catch(Fenos\Notifynder\Exceptions\NotificationNotFoundException $e)
{

    // Notification not found

}
~~~


#### Read All ####

With this method will make read all the notifications that hasn't been read but this time you will pass the id of the user. The equivalent to the id of the `to_id` filed on the you table.

~~~
Notifynder::readAll($to_id); // this will make read all the notifications not read of this user user
~~~

#### Read Limit ####
Well when is time to have thousand of notifications to read you will need to set up a Queues or something and this method will be really useful to you. In few words this method will let you make read only the number of notifications you set! If you want to update the last 10 notifications or the first one Let's see how:

~~~
$to_id = 1;

Notifynder::readLimit($to_id,10,'ASC'); // This method will update the first 10 notifications of the user with ID 1
~~~

**Parameters**
- first : To id (Id of the user)
- second : numbers of notifications you want to make read,
- third : direction of the order - 'ASC' / 'DESC'

### Retrive notifications ###

Now we did everything but we need somehow to get this notifications! Let's see how:

#### Get Not Read ####

This method will get all notifications not read giving the user id

~~~
Notifynder::getNotRead($user_id); // get all not read
~~~

You can also limit the results passing the value as second parameter:

~~~
Notifynder::getNotRead($user_id,10); // get 10 notifications not read
~~~

Do you need to paginate the notifications? give a true value as third parameter it will do the trick!

~~~
Notifynder::getNotRead($user_id,10,true); // get notifications not read paginating 10 results per page
~~~

#### Get All ####

This method will get all notifications **Even the read**, about the current user, of course the notifications not read will be at first position.

~~~
Notifynder:getAll($user_id); // get all notifications
~~~

Limiting

~~~
Notifynder::getAll($user_id,10) // get all notifications limiting at 10 the result
~~~

Paginate the result

~~~
Notifynder::getAll($user_id,10,true);
~~~


### Delete Notification ###

As before we have 3 methods availables to manage the action to delete the notifications let's see how use it.

#### Delete single ####

For delete a single notification we pass the id of the current notification to delete simply like so:

~~~
Notifynder::delete( 2 ); // it will detele the notification with ID 2
~~~


#### Delete All ####

This method will delete all notifications about the current user passed as first parameter.

~~~
Notifynder::deleteAll( 1 ); // it will delete all notification of the user with ID 1
~~~

#### Delete Limit ####

This method will be very useful as the `Notifynder::readLimit()` because it give you possibility to limit the notifications to delete very useful associated to a cron job.

~~~
Notifynder::deleteLimit( 1, 10, 'ASC' )
~~~

as first parameter you will pass the id of the user, as second parameter you will pass the number of notifications to delete, as third parameter you will pass the order that will start to count the number of notifications `"ASC" - "DESC"`

- - -

### Notifications Handler ###

The Notifynder handler is a gold resource when your application has many notifications to handle.
The scope of this handler is just return the array with the right information for send the notification.

**Scenario when the handler is useful:**

I created an `sport event` in the my application, and i want send notifications to all my followers.
The handler will be responsable to get the followers of the user and build the tipical array that notifynder will store
in the database.

Let's see how to use it.

#### Listeners ####

in your `app/start/global.php` Initialize the listeners that you will go to listen, like so.

~~~
Notifynder::bootListeners();
~~~

Next in the configuration of your package you will see a `listeners.php` file. It will store all your listeners for the notifications.

**Add a listener**
The handler use the `EventDispatcher` that laravel provide, so for add a listener use the following convention:

~~~
return [

   'listeners' => [
      
      'event.*' => 'EventHandler' // full namespace of the class
      
   ]
];
~~~

it means that for every event that start with `event` "namespace" it will Trigger the `EventHandler` Class.
That's it we have the listener set up.

**Create the class handler**

Now you need to create the class that will fire every time the event has been fired. 
**Make sure that the class extends** `Fenos\Notifynder\Handler\NotifynderDispatcher`

#### Fire a listener ####

At this point we need to fire the event listener. Again the handler will be responsable only to **return** the array that notifynder need to send the notifications.

When you fire a method it will get the built array and will **send automatically** the notifications.

For fire a method you'll use the `fire()` method.

~~~
Notifynder::fire($key,$name_category,$extraData)
~~~

it except 3 arguments:

- `$key` will be the key that will fire the listener
- `$name_category` will be the name of the category you wish to send
- `$extraData` will be the data you will pass in the handler method

Continuing with our example let's say that we fire the key `event.users.followers`

#### Handler Class ####

Let's create out Handler Class for this example:

~~~
use Fenos\Notifynder\Handler\NotifynderDispatcher;

class EventHandler extends NotifynderDispatcher
{
   public function usersFollowers($extraData,$name_category,Notifynder $notifynder)
   {
      $followers = // same logic for get the followers;
      
      $notifications = [];
      
      foreach($followers as $follower)
      {
         $notifications[] = [
             'from_id'     => 1, 
             'from_type'   => "User", 
             'to_id'       => 2, 
             'to_type'     => "User",  
             'category_id' => 1, 
             'url'         => 'www.urlofnotification.com',
         ];
      }
      
      return $notifications;
   }
}
~~~

The event that fire with the given key `event.users.followers` will fire the method `UsersFollowers`.

**How the convention work?**

In this case `event` is only the namespace of our event. After the first `dot` will be the name of the method in camel case so `UsersFollowers`.

**The method fired**

The method will have 3 arguments to work with:

- `$extraData` : The extra data you passed on the fire method
- `$category_name` : name of the category to send
- `$notifynder`: Notifynder Object

if It return `false` or an empty `array` the notifications will be not sent.

#### Delegate Events ####

Another beautiful future of notifynder is the delegation of events. It will be useful when a given action you need
to send differents categories of notifications. It use extacly the same logic of the handler but just the method `fire` change.

**Scenario:**

Coninuing the event example, when I create an event I want notify even the admin that an event has been created with a differnt notification.

I will another method called `admins` on the event class

so now is time to delegate the notifictions

~~~
Notifynder::delegate($extraData,[

'user.follower' => 'event.users.followers',
'admin'         => 'event.admins'

]);
~~~

The `delegate` method accept 2 arguments:

- `$extraData` : the data passed to the handler class,
-  array: Associative array with the **key** name of the category and value the **key** of the event

Now the followers will receive the notification with the body of the category `user.follower` and the admins with the category `admin`

- - -

### Group Notifications ###

The group of notifications are useful when you want send the differents notifications to the same member, not be confused with the `delegate method`.

The difference is that this method will send *differents categories* to **the same member**.
Instead the delegation send *differents or same categories* to *differents members*.

**Group Categories**

To get started you have to create the group by `artisan` command [see section commands](#artisan-commands), and add even
categories to group created always with the `artisan` command.

** Send Group **

Now we want to send the categories of the group to a member:

~~~
$info = [
    'from_id'     => 1,
    'to_id'       => 2,
    'url'         => 'www.urlofnotification.com',
];

Notifynder::sendGroup($info); // will send to the member 2 all the categories associated with group
~~~

### Method Category() ###

The method category before used on the documentation give you the possibility to don't hard code the id of the category but instead write the name of it.
But how it work? It does a query to the database for get the correct ID. But hey stay easy it is **lazy loading** like so even if you use that method for 100 notifications of the same type it will do only 1 quick query.

**Also remember: If you digit a name that doesn't exist on the database it will throw an exception but you can always catch it.**

~~~

try
{

Notifynder::category('notFound')->id(); // or whenever method you use

}
catch(Fenos\Notifynder\Exceptions\NotificationCategoryNotFoundException $e)
{

// category not found

}
~~~

- - -

## Notifications Queue ##

Notifynder use a easily approch to send notifications via queue. We all know that the notifications are strongly associated to a queue system. If you want enable automatically the queue when send notifications, go in the **config.php** file of the package

change the `queue` value to **true** That's it! (Obviously make sure your queue settings are correct)
Every time you send a notification notifynder will push the job to a queue.

if instead you have the queue enabled and you don't want send the notification via queue but send it immediately.
use the **sendNow()** method.

~~~
Notifynder::sendNow(array);
~~~
- - -

## Notifynder Polymorphic ##

If you have a **previous release** already installed before upgrade to the 1.6.0 please see the [Upgrade Release ](#upgrade-release) section.

On the release 1.6.0 Notifynder becomes polymorphic, or well just if you want!! What does it mean that Notifynder is polymorphic? Notifynder was binded with a User model, so the system of notifications could be use it only beetween users. But now you can decide if use it as normal just for users or if you need more power transform it Polymorphically.

Let's see how it work:

The magic is on the configutation file, so if you didn't push them just do it:

~~~
php artisan config:publish fenos/notifynder
~~~

then you will see a new value `polymorphic` setted to false just swap it to true and that's it now notifynder work polymorphically.

####Set up relations on your models####

The models that you wish to have as morphed to Notifynder must include the following relations:

~~~

public function notifications_sender()
{
    return $this->morphMany('Fenos\Notifynder\Models\Notification','from');
}

public function notifications_receiver()
{
    return $this->morphMany('Fenos\Notifynder\Models\Notification','to');
}

~~~

####Entity method() ####

An **Important** method will help you to differentiate the models associated with notifynder when it comes polymorphcally.

So every time you will use a method that require the `$user_id` when **polymorphic is disabled** you'll now use the method entity to specify the model like so:

~~~
// Get all notifications about the User with ID 1
Notifynder::entity('Users')->getAll(1);

// Delete All notifications about the Team with ID 2
Notifynder::entity('Teams')->deleteAll(2);
~~~

That's It! :)

####Send notifications polymorphically####

Just remeber to add of the default array this 2 field: `from_type`, `to_type`

~~~
$notification_information = array(

    'from_id'     => 1, // ID user that send the notification
    'from_type'   => "User", // Type of model used for polymorphic relation
    'to_id'       => 2, // ID user that receive the notification
    'to_type'   => "User",  // Type of model used for polymorphic relation
    'category_id' => 1, // category notification ID
    'url'         => 'www.urlofnotification.com', // Url of your notification
);

Notifynder::sendOne($notification_information);
~~~

### Translations ###

When you have a good system of notifications and you want extends that notifications to multiple languages, Notifynder use a easy approch for make it work.

You have to publish the configurations files, doing:

~~~
php artisan config:publish fenos/notifynder
~~~

Under the directory `app/config/packages/fenos/notifynder` you will find a file called `translations.php` that file is the right place to put your translations.

You will see that file with an array with same keys of the languages availables, feel free to add how many languages you want.

Inside that array you will put another array with they **key as the name of your category** and **the value with the translation of it**.

~~~

return array(

        'it' => array( // italian language

            // name category
            'comment' => 'ho postato un commento' // translation

        ),

        'fr' => array ( // french language

            'comment' => 'J'ai posté un commentaire'
        )
);

~~~

And now for retrieve the notifications translated, you use as normal the methods `getAll` - `getNotRead` but you easilly chain the method translate with the language as first parameter:

~~~

Notifynder::getAll( 2 )->translate('it');

~~~

It will return to you the body translated!

- - -

### Extend the model class ###

Well, I like have packages more extendible possible, so I give you the possibility to extend the model for make your own staff become real.

Even here you will have to take a look the configuration file. Under the key `notification_model` you have to change the current model with the **namespace** / **name** of your model and extend it with the previous one.

~~~

use Fenos\Notifynder\Models\Notification;

class NewNotificationModel extends Notification
{

}

~~~

#### Upgrade Release ####

**Migrate to Release 1.4.5 to 1.6.0 Important Notes:**

1) Migrations file are changed just adding 2 columns `from_type`, `from_id`
2) Now for access to the relation of the sender when you get the notifications use `from` instead `user`

Example

~~~
$allNotifications = Notifynder::getAll(1);

foreach($allNotifications as $notification)
{
    // New release 1.6.0
    <li>The notification has been sent from: {{ $notification->from->name }}</li>

    // Preious release
    <li>The notification has been sent from: {{ $notification->user->name }}</li>
}
~~~

#### Tests ####

For run the tests make sure to have phpUnit and Mockery installed

#### Package ####

© Copyright Fabrizio Fenoglio

Released package under MIT Licence.
