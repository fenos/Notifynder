Notifynder 3.1 - Laravel 5
==========

[![Build Status](https://travis-ci.org/fenos/Notifynder.svg?branch=master)](https://travis-ci.org/fenos/Notifynder)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fenos/Notifynder/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fenos/Notifynder/?branch=master)
[![Total Downloads](https://poser.pugx.org/fenos/notifynder/downloads.svg)](https://packagist.org/packages/fenos/notifynder)
[![License](https://poser.pugx.org/fenos/Notifynder/license.png)](https://packagist.org/packages/fenos/Notifynder)
[![Latest Stable Version](https://poser.pugx.org/fenos/notifynder/v/stable.png)](https://packagist.org/packages/fenos/notifynder)

Notifynder is designed to manage notifications in a powerful and easy way.
With the flexibility that Notifynder offer, It provide a complete API to work with your notifications,
such as storing, retriving, and organise your codebase to handle hundreds of notifications.
You get started in a couple of minutes to "enable" notifications in your Laravel Project.

Compatible DBs: **MySql** - **PostgresSql** - **Sqlite**

Documentation: **[Notifynder Wiki](https://github.com/fenos/Notifynder/wiki)**
- - -

## Installation ##

### Step 1 ###

Add it on your `composer.json`

    "fenos/notifynder": "3.1.*"

and run 

    composer update


### Step 2 ###

Add the following string to `config/app.php`

**Providers array:**

    Fenos\Notifynder\NotifynderServiceProvider::class,

**Aliases array:**

    'Notifynder'=>Fenos\Notifynder\Facades\Notifynder::class,


### Step 3 ###

#### Migration ####

Publish the migration as well as the configuration of notifynder with the following command:

    php artisan vendor:publish --provider="Fenos\Notifynder\NotifynderServiceProvider"

Run the migration

    php artisan migrate

### Quick Usage ###

Set up category of notification, think about it as the
body of the notification:

    php artisan notifynder:create:category "user.following" "{from.username} started to follow you"

To send a notification with notifynder, that's all
you have to do.

~~~php
Notifynder::category('user.following')
            ->from($from_user_id)
            ->to($to_user_id)
            ->url('http://www.yourwebsite.com/page')
            ->send();
~~~

**Retrieving Notifications**

~~~php
// @return Collection
Notifynder::getAll($user_id,$limit,$paginateBool);
~~~

**Reading Notifications:**
~~~php
// @return number of notifications read
Notifynder::readAll($user_id);
~~~

To know more, such as the advance usage of Notifynder Visit the **[Notifynder Wiki](https://github.com/fenos/Notifynder/wiki)**.
