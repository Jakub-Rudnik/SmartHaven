<?php
declare(strict_types=1);

require_once './Lib/Database.php';

class UsersService {
    private DatabaseConnection $db;

    public function __construct(DatabaseConnection $db) {
        $this->db = $db;
    }

    /**
     * Rejestracja nowego użytkownika
     * @param string $username
     * @param string $email
     * @param string $password
     * @return string
     */
    public function registerUser(string $username, string $email, string $password): string {
        // Sprawdzenie, czy nazwa użytkownika lub email już istnieją
        $query = 'SELECT * FROM Users WHERE Username = :username OR Email = :email';
        $params = [
            ':username' => $username,
            ':email' => $email
        ];

        try {
            $existingUser = $this->db->queryWithParams($query, $params);
            if (count($existingUser) > 0) {
                return 'Username or email already exists!';
            }
        } catch (Exception $e) {
            // Zakładamy, że brak wyników to brak istniejącego użytkownika, co jest OK
        }

        // Hashowanie hasła
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Dodanie użytkownika do bazy danych
        $query = 'INSERT INTO Users (Username, Email, PasswordHash) VALUES (:username, :email, :passwordHash)';
        $params = [
            ':username' => $username,
            ':email' => $email,
            ':passwordHash' => $hashedPassword
        ];

        try {
            $this->db->executeQuery($query, $params);
            return 'Registration successful!';
        } catch (Exception $e) {
            return 'Error during registration: ' . $e->getMessage();
        }
    }

    /**
     * Logowanie użytkownika
     * @param string $username
     * @param string $password
     * @return string
     */
    public function loginUser(string $username, string $password): string {
        // Sprawdzenie, czy użytkownik istnieje
        $query = 'SELECT * FROM Users WHERE Username = :username';
        $params = [
            ':username' => $username
        ];

        try {
            $user = $this->db->queryWithParams($query, $params)[0];

            // Weryfikacja hasła
            if (password_verify($password, $user['PasswordHash'])) {
                // Logowanie powiodło się, można ustawić sesję użytkownika
                session_start();
                $_SESSION['userID'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                return 'Login successful!';
            } else {
                return 'Invalid username or password!';
            }
        } catch (Exception $e) {
            return 'Invalid username or password!';
        }
    }


    /* Wylogowywanie użytkownika
     * @return string
     */
    public function logoutUser(): string {
        session_start();
        // Usuwanie wszystkich zmiennych sesji
        $_SESSION = [];
        // Usunięcie ciasteczka sesji
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }
        // Zniszczenie sesji
        session_destroy();
        return 'Logout successful!';
    }

    /**
     * Sprawdzenie aktywności użytkownika
     * @param int $userID
     * @return bool
     */
    public function isUserActive(int $userID): bool {
        $query = 'SELECT IsActive FROM Users WHERE UserID = :userID';
        $params = [
            ':userID' => $userID
        ];

        try {
            $result = $this->db->queryWithParams($query, $params)[0];
            return $result['IsActive'] === 1;
        } catch (Exception $e) {
            return false;
        }
    }
}

?>
