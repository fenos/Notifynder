# Notifynder 4 - Laravel 5

[![GitHub release](https://img.shields.io/github/release/fenos/Notifynder.svg?style=flat-square)](https://github.com/fenos/Notifynder/releases)
[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](https://raw.githubusercontent.com/fenos/Notifynder/master/LICENSE)
[![GitHub issues](https://img.shields.io/github/issues/fenos/Notifynder.svg?style=flat-square)](https://github.com/fenos/Notifynder/issues)
[![Total Downloads](https://img.shields.io/packagist/dt/fenos/notifynder.svg?style=flat-square)](https://packagist.org/packages/fenos/notifynder)
[![VersionEye](https://www.versioneye.com/user/projects/5878c014a21fa90051522611/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/5878c014a21fa90051522611)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/ef2a6768-337d-4a88-ae0b-8a0eb9621bf5.svg?style=flat-square&label=SensioLabs)](https://insight.sensiolabs.com/projects/ef2a6768-337d-4a88-ae0b-8a0eb9621bf5)

[![Travis branch](https://img.shields.io/travis/fenos/Notifynder/master.svg?style=flat-square&label=TravisCI)](https://travis-ci.org/fenos/Notifynder/branches)
[![StyleCI](https://styleci.io/repos/18425539/shield)](https://styleci.io/repos/18425539)
[![Scrutinizer Build](https://img.shields.io/scrutinizer/build/g/fenos/Notifynder.svg?style=flat-square&label=ScrutinizerCI)](https://scrutinizer-ci.com/g/fenos/Notifynder/?branch=master)

[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/fenos/Notifynder.svg?style=flat-square)](https://scrutinizer-ci.com/g/fenos/Notifynder/?branch=master)
[![Code Climate](https://img.shields.io/codeclimate/github/fenos/Notifynder.svg?style=flat-square)](https://codeclimate.com/github/fenos/Notifynder)
[![Coveralls](https://img.shields.io/coveralls/fenos/Notifynder.svg?style=flat-square)](https://coveralls.io/github/fenos/Notifynder)

[![Slack Team](https://img.shields.io/badge/slack-astrotomic-orange.svg?style=flat-square)](https://astrotomic.slack.com)
[![Slack join](https://img.shields.io/badge/slack-join-green.svg?style=social)](https://notifynder.signup.team)


Notifynder is designed to manage notifications in a powerful and easy way. With the flexibility that Notifynder offer, It provide a complete API to work with your notifications, such as storing, retrieving, and organise your codebase to handle hundreds of notifications. You get started in a couple of minutes to "enable" notifications in your Laravel Project.

Compatible DBs: **MySQL** - **PostgreSQL** - **SQLite**

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

#### Migration & Config

Publish the migration as well as the configuration of notifynder with the following command:

```
php artisan vendor:publish --provider="Fenos\Notifynder\NotifynderServiceProvider"
```

Run the migration

```
php artisan migrate
```

## Senders

A list of official supported custom senders is in the [Notifynder Doc](http).

We also have a [collect issue](https://github.com/fenos/Notifynder/issues/242) for all additional senders we plan or already have.

If you want any more senders or want to provide your own please [create an issue](https://github.com/fenos/Notifynder/issues/new?milestone=Senders).

## ToDo

Tasks we still have to do:

* add unittests for parser and models
* complete the new documentation

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

## Services

* [Travis CI](https://travis-ci.org/fenos/Notifynder)
* [Style CI](https://styleci.io/repos/18425539)
* [Code Climate](https://codeclimate.com/github/fenos/Notifynder)
* [Scrutinizer](https://scrutinizer-ci.com/g/fenos/Notifynder)
* [Coveralls](https://coveralls.io/github/fenos/Notifynder)
