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
        $query = 'SELECT * FROM Users WHERE Email = :email';
        $params = [
            
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
     * Logowanie użytkownika na podstawie emailu
     * @param string $email
     * @param string $password
     * @return string
     */
    public function loginUserByEmail(string $email, string $password): string {
        // Sprawdzenie, czy użytkownik istnieje
        $query = 'SELECT * FROM Users WHERE Email = :email';
        $params = [
            ':email' => $email
        ];

        try {
            $user = $this->db->queryWithParams($query, $params)[0];

            // Weryfikacja hasła
            if (password_verify($password, $user['PasswordHash'])) {
                // Logowanie powiodło się, można ustawić sesję użytkownika
                
                $_SESSION['userID'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['email'] = $user['Email'];
                return 'Login successful!';
            } else {
                return 'Invalid email or password!';
            }
        } catch (Exception $e) {
            return 'Invalid email or password!';
        }
    }

    /* Wylogowywanie użytkownika
     * @return string
     */
    public function logoutUser()
{
    
    if (session_status() == PHP_SESSION_ACTIVE) {
       
        $_SESSION = array();

      
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destroy the session
        session_destroy();
    }
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
