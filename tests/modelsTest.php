<?php

namespace LifeCycle;

use Silex\WebTestCase;

class ModelsTest extends WebTestCase
{

    public function createApplication()
    {
        $app = require __DIR__.'/../src/app.php';
        require __DIR__.'/../config/dev.php';
        require __DIR__.'/../src/controllers.php';
        require_once __DIR__.'/../src/models.php';
        $app['session.test'] = true;
        unset($app['exception_handler']);
        return $this->app = $app;
    }

    public function testUserAuthenticate()
    {
        $user = new User('janed');
        $db = $this->app['db'];
        $user->secure($db, '123', 'Jane', 'Doe');
        $this->assertFalse($user->authenticate($db, '321'));
        $this->assertTrue($user->authenticate($db, '123'));
    }

    public function testUserSecure()
    {
        $user = new User('janed');
        $db = $this->app['db'];
        $newpass = 'thefloorislava';
        $db->beginTransaction();
        try {
            $this->assertFalse($user->secure($db, $newpass, 'Jane', 'Does'));
            $this->assertTrue($user->secure($db, $newpass, 'Jane', 'Doe'));
            $this->assertTrue($user->authenticate($db, $newpass));
        } catch (Exception $e) {
            throw $e;
        } finally {
            $db->rollback();
        }
    }

    public function testUserRefresh()
    {
        $username = 'janed';
        $db = $this->app['db'];
        $db->beginTransaction();
        try {
            $user1 = new User($username, $db);
            $user2 = new User($username, $db);
            $this->assertEquals($user1->getFirstName(), $user2->getFirstName());
            
            $newname = 'Whoami';
            $db->executeUpdate('UPDATE users SET first_name=? WHERE username=?', [$newname, $username]);
            $user1->refresh($db);
            $this->assertNotEquals($user1->getFirstName(), $user2->getFirstName());

            $user2->refresh($db);
            $this->assertEquals($user1->getFirstName(), $user2->getFirstName());
        } catch (Exception $e) {
            throw $e;
        } finally {
            $db->rollback();
        }
    }

    public function testUserExists()
    {
        $db = $this->app['db'];

        $user = new User('jane', $db);
        $this->assertFalse($user->exists($db));

        $user = new User('janed', $db);
        $this->assertTrue($user->exists($db));
    }

    public function testUserGetName()
    {
        $db = $this->app['db'];
        $user = new User('janed', $db);
        $this->assertEquals('Jane', $user->getFirstName());
        $this->assertEquals('Doe', $user->getLastName());
    }
}
