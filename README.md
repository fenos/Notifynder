Notifynder
==========


Notifynder is a package that implement on your application a management system of internal notifications. Similar to facebook notifications. You cand send, make read, and more stay tuned on the following documentation. 
This package has been released for Laravel 4 Framework.

- - -

####On the Next Release:####

   - Translations notifications **( Added )**
   - Insert on the body text whenever parameter you pass in any position.
   - Extend Notifynder Eloquent **( Added )**
   - Extend Notifynder Class

- - -



* [Installation](#installation)
* [Documentation](#documentation)
* [Notification Categories](#notification-categories)
    * [Add](#add-categories)
    * [Update](#update-categories)
    * [Delete](#delete-categories)
* [Send Notification/s](#send-notification-s)
    * [Send single notification](#send-single-notification)
    * [Send multiple notifications](#send-multiple-notifications)
* [Read Notification/s](#read-notifications)
    * [Read one](#read-one)
    * [Read All](#read-all)
    * [Read Limit](#read-limit)
* [Retrive Notification] (#retrive-notifications)
    * [Get not read](#get-not-read)
    * [Get All](#get-all)
* [Method Category()](#method-category)
* [Translations](#translations)


## Installation ##

### Step 1 ###

Add it on your composer.json

~~~
"fenos/notifynder": "dev-master"
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
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now()
    ),
    
    array (
        'from_id'     => 1, // ID user that send the notification
        'to_id'       => 4, // ID user that receive the notification
        'category_id' => 2, // ID category
        'url'         => 'www.urlofnotification.com', // Url of your notification
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
        'created_at'  => Carbon::now(),
        'updated_at'  => Carbon::now()
    ),
    
    array => (
        'from_id'     => 1, // ID user that send the notification
        'to_id'       => 4, // ID user that receive the notification
        'category_id' => Notifynder::category('londonNews')->id(), // Will give you the notification ID
        'url'         => 'www.urlofnotification.com', // Url of your notification
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


###Method Category() ###

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

When you have a good system of notifications and you want extends that notifications to multiple languages Notifynder use a easy approch for make it work.

You have to publish the configurations files, on there you will find a file called `translations.php` that file is the right place to put your translations.

You will see that file with an array with same keys of the languages availables, feel free to add how many languages you want.

Inside that array you will put another array with they **key as the name of your category** and **the value with the translation of it**.

~~~

return array(
        
        // italian language

        'it' => array(

            // name category  
            'comment' => 'ho postato un commento' // translation

        ),

        'fr' => array (

            'comment' => 'J'ai posté un commentaire'

        )

);

~~~

And now for retrieve that translations when you get the results from the methods `getAll` - `getNotRead` you can easilly translate them like so:

~~~

Notifynder::getAll( 2 )->translate('it');

~~~

You use like normal the method to retrieve the notification and you chain the translate method with the language you wish to translate! That's it.


#### Tests ####

For run the tests make sure to have phpUnit installed

#### Package ####

© Copyright Fabrizio Fenoglio

Released package under MIT Licence.
