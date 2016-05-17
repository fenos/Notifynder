<?php

$factory('Fenos\Notifynder\Models\NotificationCategory', [

    'name' => $faker->name,
    'text' => 'test notification',
]);

$factory('Fenos\Tests\Models\User', [

    'name' => $faker->name,
    'surname' => $faker->lastName,
]);

$factory('Fenos\Notifynder\Models\Notification', [

    'from_id' => 'factory:Fenos\Tests\Models\User',
    'from_type' => 'Fenos\Tests\Models\User',
    'to_id' => 'factory:Fenos\Tests\Models\User',
    'to_type' => 'Fenos\Tests\Models\User',
    'category_id' => 'factory:Fenos\Notifynder\Models\NotificationCategory',
    'url' => $faker->url,
    'extra' => json_encode(['exta.name' => $faker->name]),
    'read' => 0,
    'expire_time' => null,
    'created_at' => $faker->dateTime,
    'updated_at' => $faker->dateTime,
]);

$factory('Fenos\Notifynder\Models\NotificationGroup', [
    'name' => $faker->name,
]);
