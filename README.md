Notifynder
==========

Notifynder is a package that implement on your application a management system of internal notifications. Similar to facebook notifications. You cand send, make read, and more stay tuned on the following documentation.


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

The notification categories are just the body of the your notification. As first release we have only 2 field availables:

- name
- text

### Add categories ###

Let's say an example, We want to add a notification that says: **News recently updated from the city of London**

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
$new_informations = array( 'body' => 'New body of the category' );

Notifynder::category('londonNews')->updateCategory($new_informations);
~~~

On the category method you'll pass the name of the category that you want update and on the update method just the informations.

### Delete categories ###

For the delete a category just:

~~~
Notifynder::deleteCategory(1);
~~~

As first parameter you pass the id of the category you want to delete or again you can use the method `category` and it will think about to get the id for you just passing the name

~~~
Notifynder::category('londonNews')->deleteCategory();
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
$notification_information = array(

    'from_id'     => 1, // ID user that send the notification
    'to_id'       => 2, // ID user that receive the notification
    'url'         => 'www.urlofnotification.com', // Url of your notification
);

Notifynder::category('londonNews')->sendOne($notification_information); // it just send!
~~~
