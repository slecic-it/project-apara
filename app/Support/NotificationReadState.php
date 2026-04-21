<?php

namespace App\Support;

class NotificationReadState
{
    public function readIds(string $scope): array
    {
        $value = session($this->sessionKey($scope), []);

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_unique(array_map('intval', $value)));
    }

    public function isRead(string $scope, int $notificationId): bool
    {
        return in_array($notificationId, $this->readIds($scope), true);
    }

    public function markRead(string $scope, int $notificationId): void
    {
        $ids = $this->readIds($scope);

        if (!in_array($notificationId, $ids, true)) {
            $ids[] = $notificationId;
        }

        session([$this->sessionKey($scope) => array_values(array_unique($ids))]);
    }

    public function markUnread(string $scope, int $notificationId): void
    {
        $ids = array_values(array_filter(
            $this->readIds($scope),
            fn (int $id) => $id !== $notificationId
        ));

        session([$this->sessionKey($scope) => $ids]);
    }

    public function markAllRead(string $scope, array $notificationIds): void
    {
        session([
            $this->sessionKey($scope) => array_values(array_unique(array_map('intval', $notificationIds))),
        ]);
    }

    private function sessionKey(string $scope): string
    {
        return 'notifications.read.' . $scope;
    }
}
