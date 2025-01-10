<?php

declare(strict_types=1);

namespace Services;

use Exception;
use Lib\DatabaseConnection;
// Opcjonalnie możesz mieć własną encję Group:
use Entity\Group;

class GroupService
{
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db)
    {
        $this->db = $db;
    }

    /**
     * Tworzy nową grupę dla danego użytkownika.
     *
     * @param int $userId
     * @param string $groupName
     * @return int Zwraca ID nowo utworzonej grupy (lub 0 w razie błędu).
     */
    public function createGroup(int $userId, string $groupName): int
    {
        $sql = "INSERT INTO `Groups` (UserID, GroupName) VALUES (:userId, :groupName)";
        $params = [
            ':userId'    => $userId,
            ':groupName' => $groupName
        ];

        try {
            $this->db->execute($sql, $params);
            return (int) $this->db->getLastInsertId();
        } catch (Exception $e) {
            echo "Error creating group: " . $e->getMessage();
            return 0;
        }
    }

    /**
     * Pobiera grupę po jej ID (zwraca obiekt Group lub null, jeśli nie znaleziono).
     *
     * @param int $groupId
     * @return Group|null
     */
    public function getGroupById(int $groupId): ?Group
    {
        $sql = "
            SELECT GroupID, UserID, GroupName
            FROM `Groups`
            WHERE GroupID = :groupId
        ";
        $params = [':groupId' => $groupId];

        try {
            $rows = $this->db->query($sql, $params);
            if (count($rows) === 0) {
                return null;
            }
            $row = $rows[0];
            return new Group(
                (int)$row['GroupID'],
                (int)$row['UserID'],
                $row['GroupName']
            );
        } catch (Exception $e) {
            echo "Error fetching group by ID: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Pobiera wszystkie grupy należące do danego użytkownika.
     *
     * @param int $userId
     * @return Group[]
     */
    public function getGroupsByUserId(int $userId): array
    {
        $sql = "
            SELECT GroupID, UserID, GroupName
            FROM `Groups`
            WHERE UserID = :userId
            ORDER BY GroupName
        ";
        $params = [':userId' => $userId];

        try {
            $rows = $this->db->query($sql, $params);
        } catch (Exception $e) {
            echo "Error fetching groups by UserID: " . $e->getMessage();
            return [];
        }

        $groups = [];
        foreach ($rows as $row) {
            $groups[] = new Group(
                (int)$row['GroupID'],
                (int)$row['UserID'],
                $row['GroupName']
            );
        }
        return $groups;
    }

    /**
     * Pobiera wyłącznie nazwy grup danego użytkownika (np. do wyświetlenia w dropdown).
     *
     * @param int $userId
     * @return string[] Tablica nazw grup
     */
    public function getAllGroupNamesByUser(int $userId): array
    {
        $sql = "
            SELECT GroupName
            FROM `Groups`
            WHERE UserID = :userId
            ORDER BY GroupName
        ";
        $params = [':userId' => $userId];

        try {
            $rows = $this->db->query($sql, $params);
        } catch (Exception $e) {
            echo "Error fetching group names by UserID: " . $e->getMessage();
            return [];
        }

        // Zwracamy tylko listę nazw (bez ID)
        return array_column($rows, 'GroupName');
    }

    /**
     * Aktualizuje nazwę grupy (lub inne pola – jeśli w przyszłości dodasz więcej).
     *
     * @param int $groupId
     * @param string $newName
     * @return bool
     */
    public function updateGroup(int $groupId, string $newName): bool
    {
        $sql = "
            UPDATE `Groups`
            SET GroupName = :groupName
            WHERE GroupID = :groupId
        ";
        $params = [
            ':groupName' => $newName,
            ':groupId'   => $groupId
        ];

        try {
            $this->db->execute($sql, $params);
            return true;
        } catch (Exception $e) {
            echo "Error updating group: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Usuwa grupę z bazy danych.  
     * Pamiętaj, że w tabeli Device mamy klauzulę ON DELETE SET NULL w relacji do Groups,
     * więc po usunięciu grupy wszystkie urządzenia z tej grupy dostaną GroupID = NULL.
     *
     * @param int $groupId
     * @return bool
     */
    public function deleteGroup(int $groupId): bool
    {
        $sql = "DELETE FROM `Groups` WHERE GroupID = :groupId";
        $params = [':groupId' => $groupId];

        try {
            $this->db->execute($sql, $params);
            return true;
        } catch (Exception $e) {
            echo "Error deleting group: " . $e->getMessage();
            return false;
        }
    }
}

