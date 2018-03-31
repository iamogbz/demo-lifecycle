<?php

namespace LifeCycle;

use Silex\WebTestCase;

class ControllersTest extends WebTestCase
{
    public function testGetHomepage()
    {
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('Welcome', $crawler->filter('body')->text());
    }

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

    public function testDashboardRedirect()
    {
        $user = new User('janed', $this->app['db']);
        $this->app['session']->set('user', $user);
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('successful login', $crawler->filter('body')->text());
    }

    public function testLoginRedirect()
    {
        $this->app['session']->clear('user');
        $client = $this->createClient();
        $client->followRedirects(true);
        $crawler = $client->request('GET', '/');

        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('please complete', $crawler->filter('body')->text());
    }

    public function testLoginSuccess()
    {
        unset($this->app['exception_handler']);
        $client = $this->createClient();
        $client->followRedirects(true);
        $data = [
            'username'=>'janed',
            'password'=>'123',
        ];
        $crawler = $client->request('POST', '/login', ['form'=>$data]);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('successful login', $crawler->filter('body')->text());
    }

    public function testLoginFailure()
    {
        unset($this->app['exception_handler']);
        $client = $this->createClient();
        $client->followRedirects(true);
        $data = [
            'username'=>'nouser',
            'password'=>'nopass',
        ];
        $crawler = $client->request('POST', '/login', ['form'=>$data]);
        $this->assertTrue($client->getResponse()->isOk());
        $this->assertContains('no match found', $crawler->filter('body')->text());
    }
    }
}
