<?php

namespace Fenos\Notifynder\Contracts;

use Closure;

interface NotifynderContract
{
    public function category($name);

    public function entity($name);

    public function addCategory($name, $text);

    public function updateCategory(array $updates, $categoryId);

    public function send($info = []);

    public function sendNow($info = []);

    public function sendOne($info = []);

    public function sendMultiple($info = []);

    public function sendGroup($groupName, $info = []);

    public function readOne($notificationId);

    public function readLimit($toId, $numbers, $order = 'ASC');

    public function readAll($toId);

    public function delete($notificationId);

    public function deleteLimit($toId, $number, $order = 'ASC');

    public function deleteAll($toId);

    public function deleteByCategory($categoryName, $expired = false);

    public function getNotRead($toId, $limit = null, $paginate = false, $order = 'desc', Closure $filterScope = null);

    public function getAll($toId, $limit = null, $paginate = false, $order = 'desc', Closure $filterScope = null);

    public function countNotRead($toId, Closure $filterScope = null);

    public function findNotificationById($notificationId);

    public function getLastNotification($toId, $category = null, Closure $filterScope = null);

    public function addCategoryToGroupByName($groupName, $categoryName);

    public function addCategoryToGroupById($groupId, $categoryId);

    public function addCategoriesToGroup();

    public function fire($key, $categoryName, $values = []);

    public function delegate(array $delegation, $data = []);

    public function bootListeners(array $listeners);

    public function builder();

    public function extend($name, $registrar);

    public function id();

    public function getCategoriesContainer($name);

    public function dispatchWith($customSenderName);
}
