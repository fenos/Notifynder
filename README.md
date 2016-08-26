# Notifynder 4 - Laravel 5

[![GitHub release](https://img.shields.io/github/release/fenos/Notifynder.svg?style=flat-square)](https://github.com/fenos/Notifynder/releases)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/fenos/Notifynder/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/fenos/Notifynder.svg?style=flat-square)](https://github.com/fenos/Notifynder/issues)
[![Total Downloads](https://img.shields.io/packagist/dt/fenos/notifynder.svg?style=flat-square)](https://packagist.org/packages/fenos/notifynder)

[![Travis branch](https://img.shields.io/travis/fenos/Notifynder/master.svg?style=flat-square)](https://travis-ci.org/fenos/Notifynder/branches)
[![StyleCI](https://styleci.io/repos/18425539/shield)](https://styleci.io/repos/18425539)
[![Scrutinizer Build](https://img.shields.io/scrutinizer/build/g/fenos/Notifynder.svg?style=flat-square)](https://scrutinizer-ci.com/g/fenos/Notifynder/?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/fenos/Notifynder.svg?style=flat-square)](https://scrutinizer-ci.com/g/fenos/Notifynder/?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/fenos/Notifynder.svg?style=flat-square)](https://codeclimate.com/github/fenos/Notifynder)
[![Code Climate](https://img.shields.io/codeclimate/issues/github/fenos/Notifynder.svg?style=flat-square)](https://codeclimate.com/github/fenos/Notifynder/issues)

[![Slack Team](https://img.shields.io/badge/slack-notifynder-orange.svg?style=flat-square)](https://notifynder.slack.com)
[![Slack join](https://img.shields.io/badge/slack-join-green.svg?style=social)](https://notifynder.signup.team)


Notifynder is designed to manage notifications in a powerful and easy way.
With the flexibility that Notifynder offer, It provide a complete API to work with your notifications,
such as storing, retrieving, and organise your codebase to handle hundreds of notifications.
You get started in a couple of minutes to "enable" notifications in your Laravel Project.

Compatible DBs: **MySql** - **PostgresSql** - **Sqlite**

Documentation: **[Notifynder Docu](http://notifynder.info)**

-----

## Installation

### Step 1

Add it on your `composer.json`

```
"fenos/notifynder": "^4.0"
```

and run 

```
composer update
```

or run

```
composer require fenos/notifynder
```


### Step 2

Add the following string to `config/app.php`

**Providers array:**

```
Fenos\Notifynder\NotifynderServiceProvider::class,
```

**Aliases array:**

```
'Notifynder' => Fenos\Notifynder\Facades\Notifynder::class,
```


### Step 3

#### Migration

Publish the migration as well as the configuration of notifynder with the following command:

```
php artisan vendor:publish --provider="Fenos\Notifynder\NotifynderServiceProvider"
```

Run the migration

```
php artisan migrate
```

## Usage

This Branch isn't ready for any kind of usage! It's development in progress and will bring a whole new code-base for this package.
Everyone is welcome to support us or give feedback for the new major version in our Slack Team.

## Versioning

Starting with `v4.0.0` we are following the [Semantic Versioning Standard](http://semver.org).

### Summary

Given a version number `MAJOR`.`MINOR`.`PATCH`, increment the:

* **MAJOR** version when you make incompatible API changes,
* **MINOR** version when you add functionality in a backwards-compatible manner, and
* **PATCH** version when you make backwards-compatible bug fixes.

Additional labels for pre-release (`alpha`, `beta`, `rc`) are available as extensions to the `MAJOR`.`MINOR`.`PATCH` format.

## Contributors

Thanks for everyone [who contributed](https://github.com/fenos/Notifynder/graphs/contributors) to Notifynder and a special thanks for the most active contributors

- [Gummibeer](https://github.com/Gummibeer)