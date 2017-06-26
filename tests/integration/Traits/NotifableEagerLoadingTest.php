<?php

class NotifableEagerLoadingTest extends NotifynderTestCase
{
    public function testGetNotificationRelationReadsConfigurationParameterIfNothingIsPassed()
    {
        $user = $this->createUser();
        $this->sendNotificationTo($user);

        $notification = $user->getNotificationRelation()->first();
        $this->assertModelHasNoLoadedRelations($notification, ['category', 'from', 'to']);
    }

    public function testGetNotificationRelationCanEagerLoadAllRelationsIfTrueIsPassed()
    {
        $user = $this->createUser();
        $this->sendNotificationTo($user);

        $notification = $user->getNotificationRelation(true)->first();

        $this->assertModelHasLoadedRelations($notification, ['category', 'from', 'to']);
    }

    public function testGetNotificationRelationCanEagerLoadASubsetOfRelationsIfAnArrayIsPassed()
    {
        $user = $this->createUser();
        $this->sendNotificationTo($user);

        $notification = $user->getNotificationRelation(['category'])->first();

        $this->assertModelHasLoadedRelations($notification, ['category']);
        $this->assertModelHasNoLoadedRelations($notification, ['from', 'to']);
    }

    public function testGetNotificationRelationDoesnotEagerLoadIfConfigurationParameterIsMissing()
    {
        $user = $this->createUser();
        $this->sendNotificationTo($user);

        $config = app('notifynder.config');
        $config->forget('eager_load');

        $notification = $user->getNotificationRelation()->first();
        $this->assertModelHasNoLoadedRelations($notification, ['category', 'from', 'to']);
    }

    public function testGetNotificationsReadsEagerLoadConfigurationParameter()
    {
        $user = $this->createUser();
        $this->sendNotificationTo($user);

        $config = app('notifynder.config');

        $config->set('eager_load', true);
        $notifications = $user->getNotifications();
        $this->assertModelHasLoadedRelations($notifications[0], ['category', 'from', 'to']);

        $config->set('eager_load', false);
        $notifications = $user->getNotifications();
        $this->assertModelHasNoLoadedRelations($notifications[0], ['category', 'from', 'to']);

        $config->set('eager_load', ['category', 'from']);
        $notifications = $user->getNotifications();
        $this->assertModelHasLoadedRelations($notifications[0], ['category', 'from']);
        $this->assertModelHasNoLoadedRelations($notifications[0], ['to']);
    }

    private function assertModelHasLoadedRelations($model, $relationNames = [])
    {
        $modelLoadedRelations = $model->getRelations();
        foreach ($relationNames as $relationName) {
            $this->assertArrayHasKey($relationName, $modelLoadedRelations, $relationName.' relation was not eager loaded');
        }
    }

    private function assertModelHasNoLoadedRelations($model, $relationNames = [])
    {
        $modelLoadedRelations = $model->getRelations();
        foreach ($relationNames as $relationName) {
            $this->assertArrayNotHasKey($relationName, $modelLoadedRelations, $relationName.' relation was eager loaded');
        }
    }
}
