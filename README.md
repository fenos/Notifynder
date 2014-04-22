Notifynder
==========

[![Build Status](https://travis-ci.org/fenos/Notifynder.svg?branch=master)](https://travis-ci.org/fenos/Notifynder)
[![ProjectStatus](http://stillmaintained.com/fenos/Notifynder.png)](http://stillmaintained.com/fenos/Notifynder)
[![License](https://poser.pugx.org/fenos/Notifynder/license.png)](https://packagist.org/packages/fenos/Notifynder)
[![Latest Stable Version](https://poser.pugx.org/fenos/notifynder/v/stable.png)](https://packagist.org/packages/fenos/notifynder)

Notifynder is a package that implement on your application a management system of internal notifications. Similar to facebook notifications. You cand send, make read, and more stay tuned on the following documentation. 
This package has been released for Laravel 4 Framework.

- - -
####What's new####

#####Release 1.4.0#####
* [Advanced Categories](#advanced-categories)

#####Release 1.4.5#####
* [Notifications Handler](#notifications-handler)

- - -



* [Installation](#installation)
* [Documentation](#documentation)
* [Notification Categories](#notification-categories)
    * [Add](#add-categories)
    * [Update](#update-categories)
    * [Delete](#delete-categories)
    * [Advanced Categories](#advanced-categories)
* [Send Notification/s](#send-notification-s)
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
* [Method Category()](#method-category)
* [Translations](#translations)
* [Notifications Handler](#notifications-handler)
* [Extends Model Class](#extend-the-model-class)

## Installation ##

### Step 1 ###

Add it on your composer.json

~~~
"fenos/notifynder": "1.4.*"
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
'Notifynder'	=> 'Fenos\Notifynder\Facades\Notifynder'
~~~

### Step 3 ###

#### Migration ####

Make sure that your settings on **app/config/database.php** are correct, then make the migration typing:

~~~
php artisan migrate --package="fenos/notifynder"
~~~

### Step 4 ###

#### Connecting to the user Model ####

Notifynder use a user model to get informations from who sent the notification and who receive the notification, so if you have a different name of user model, publish the configurations files and fill the path of your model.

~~~
php artisan config:publish fenos/notifynder
~~~

That's it! we have done with the installation!

## Documentation ##

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

### Send Notification / s ###

Notifynder permit to send a single notification or multiple notifications at once. Let's see how:

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


### Notifications Handler ###

What's this new future? Well notifynder handler is a simple class that permit you to have good separation of your logic for send notifications.
Sometimes you wish to send a notification when something happen and sometimes "how is happened to me" I found myself to write 100 rows just for determinate wich category send
why, and when. 

Then this class will support us for separate our logic, let's see how it work:


First you have to create a file called 1notifications.php1 on your `app` folder or whenever you feel right to create it. **It must be autoloaded** so let's adding it on your composer json:

This will not be a class so you will use `files` under autoload section.
~~~
"files": [
    "app/notifications.php"
]
~~~

This file will be more or less similar to your route file where you'll listen this kind the event to be triggered. 
For determinate a listener of notification let's see the code of example:

~~~
Notifynder::listen(['key' => 'EventInvite', 'handler' => 'YourClass@YourMethod']);
~~~

Let's describe what's going on here, we are passing an array with 2 **main keys** `key` and `handler`.

- The `key` will give you the possibility to trigger this event whenever has been called.
- The `handler` will fill with your Class and the method that will contain your logic for the notification. It is separate with `@`

When you set up this listener I'm going to create that class for make more clear the situation:

~~~

class YourClass {

    // you can also use dependency Injection on your constructor the class 
    //will be resolved with App::make behind the scences
    
    /**
    * Logic for send a notification to users
    * Excluding the session id user
    *
    * @param $array_user_id (Array)
    */
    public function YourMethod($array_user_id) // i pass a array of user id that will receive the notification
    {                                          // you can pass any parameters you want
        if ( count($array_user_id) > 0 )
        {

            // delete the id of the user logged that send the notification
            if(($key = array_search(Auth::user()->id, $array_user_id)) !== false) {

                unset($array_user_id[$key]);
            }

            // i set up the array to send for all the remain users
            foreach ($array_user_id as $key => $value)
            {
                $notification_information[] = array(

                    'from_id'     => Auth::user()->id,      // ID user that send the notification
                    'to_id'       => $array_user_id[$key],  // ID user that receive the notification
                    'category_id' => 1,                     // category id
                    'url'         => 'www.urlofnotification.com', // Url of your notification
                );
            }

            // return the array of the users
            return $notification_information;

        }

        // return false if there is no user to send
        return false;

    }

}
~~~

Well this is how you set up your logic for the notifications returning the array of the users that you want that the current notification be sent.
**Remember to return false in case no notifications will be sent** You will understand why in a bit.

Now we had make listen our notification and set up our logic for make it send, now is time to **trigger** this notification.

Instead to use directly the method `Notifynder::sendMultiple()` it will be used with a closure and it permit to invoke that method sending the notification as well.
Let's see this:

~~~
Notifynder::fire('EventInvite',['values' => $myValues, 'use' => function($notifynder,$yourMethodCallBack){
    
    return $notifynder->sendMultiple($yourMethodCallBack); // cool isn't?

}]);
~~~

Let's describe it:

 - First Parameter is the `key` that had set on the listener
 - The second parameter is an array passing `values` if you have any extra values to pass a that function, if you haven't any you can not use it.
   As `use` value you pass a closure, in this closure you'll find as first parameter the object of notifynder, and the second the value that you
   returned from your function

If your method return false, the code Inside the closure will be not triggered and the clouse will return false as well.

Now for complete this example let's see this in action:

~~~

class MyController extends BaseController
{

    public function inviteToEvent()
    {
        $users_selected = Input::get('users_selected'); // Array of users

        Notifynder::fire('EventInvite',['values' => $users_selected, 'use' => function($notifynder, $users){

            return $notifynder->sendMultiple($users); // it send to all your users!

        }]);
    }
}

~~~

I hope you can see the benefits of that. Enjoy it.

### Extend the model class ###

Well, I like have packages more extendible possible, so I give you the possibility to extend the model for make your own staff become real.

Even here you will have to take a look the configuration file. Under the key `notification_model` you have to change the current model with the **namespace** / **name** of your model and extend it with the previous one.

~~~

use Fenos\Notifynder\Models\Notification;

class NewNotificationModel extends Notification
{

}

~~~

#### Tests ####

For run the tests make sure to have phpUnit and Mockery installed

#### Package ####

© Copyright Fabrizio Fenoglio

Released package under MIT Licence.
