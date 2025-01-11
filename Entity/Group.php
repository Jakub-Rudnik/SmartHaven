<?php

declare(strict_types=1);

namespace Entity;

class Group
{
    private int $groupId;
    private int $userId;
    private string $groupName;

    /**
     * Konstruktor encji Group
     *
     * @param int    $groupId
     * @param int    $userId
     * @param string $groupName
     */
    public function __construct(int $groupId, int $userId, string $groupName)
    {
        $this->groupId = $groupId;
        $this->userId = $userId;
        $this->groupName = $groupName;
    }

    /**
     * Zwraca ID grupy
     *
     * @return int
     */
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * Ustawia ID grupy (rzadko potrzebne, zwykle jest nadawane przez bazę)
     *
     * @param int $groupId
     */
    public function setGroupId(int $groupId): void
    {
        $this->groupId = $groupId;
    }

    /**
     * Zwraca ID użytkownika, do którego należy ta grupa
     *
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Ustawia ID użytkownika, do którego należy grupa
     *
     * @param int $userId
     */
    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    /**
     * Zwraca nazwę grupy
     *
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }

    /**
     * Ustawia nazwę grupy
     *
     * @param string $groupName
     */
    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }
}

