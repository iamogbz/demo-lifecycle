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
     * @param string username
     * @param Connection db connection pass to load values from database
     */
    public function __construct(string $username, Connection $db = null)
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
     * @param string plain password
     * @return bool
     */
    public function authenticate(Connection $db, string $plain)
    {
        $query = "SELECT password FROM users WHERE username = ?";
        $result = $db->fetchAssoc($query, [$this->username]);
        $hash = $result['password'];
        return password_verify($plain, $hash);
    }

    /**
     * Update user password
     * @param Connection db
     * @param string plain
     * @param string firstname
     * @param string lastname
     * @return bool
     */
    public function secure(Connection $db, string $plain, string $firstname, string $lastname)
    {
        $query = "UPDATE users SET password=? WHERE username=? AND first_name=? AND last_name=?";
        return (bool) $db->executeUpdate(
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
     * @return bool
     */
    public function exists(Connection $db)
    {
        $this->refresh($db);
        return $this->exists;
    }

    /**
     * Get user first name
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Get user last name
     * @return string
     */
    public function getLastName()
    {
        return $this->last_name;
    }
}
