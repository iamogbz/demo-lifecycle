<?php

namespace Lifecycle;

use Doctrine\DBAL\Connection;

class User
{
    public $username;
    private $first_name;
    private $last_name;
    private $exists;
    
    /**
     * Initialise user model
     * @param String username
     * @param Connection db connection pass to load values from database
     */
    public function __construct(String $username, Connection $db = null)
    {
        $this->username = $username;
        $this->exists = false;
        if (isset($db)) {
            $this->refresh($db);
        }
    }

    /**
     * Authenticate user with password
     * @param Connection db connection
     * @param String plain password
     * @return Bool
     */
    public function authenticate(Connection $db, String $plain)
    {
        $query = "SELECT password FROM users WHERE username = ?";
        $result = $db->fetchAssoc($query, [$this->username]);
        $hash = $result['password'];
        return password_verify($plain, $hash);
    }

    /**
     * Update user password
     * @param Connection db
     * @param String plain
     * @param String firstname
     * @param String lastname
     * @return Bool
     */
    public function secure(Connection $db, String $plain, String $firstname, string $lastname)
    {
        $query = "UPDATE users SET password=? WHERE username=? AND first_name=? AND last_name=?";
        return $db->executeUpdate(
            $query,
            [
                password_hash($plain, PASSWORD_DEFAULT),
                $this->username,
                $firstname,
                $lastname
            ]
        );
    }

    /**
     * Refresh user model from database
     * @param Connection db connection
     */
    public function refresh(Connection $db)
    {
        $query = "SELECT first_name, last_name FROM users WHERE username = ?";
        $result = $db->fetchAssoc($query, [$this->username]);
        if (empty($result)) {
            $this->exists = false;
        } else {
            $this->exists = true;
            $this->first_name = $result['first_name'];
            $this->last_name = $result['last_name'];
        }
    }

    /**
     * Save user model to database
     * @param Connection db connection
     */
    public function save(Connection $db)
    {
        throw new Exception("unsupported operation");
    }

    /**
     * Check if user exists in database
     * @param Connection db connection
     * @return Bool
     */
    public function exists(Connection $db)
    {
        $this->refresh($db);
        return $this->exists;
    }

    /**
     * Get user first name
     * @return String
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Get user last name
     * @return String
     */
    public function getLastName()
    {
        return $this->last_name;
    }
}
