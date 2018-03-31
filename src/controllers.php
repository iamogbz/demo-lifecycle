<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Lifecycle\User;

//Request::setTrustedProxies(array('127.0.0.1'));

// home controllers
$app->get('/', function () use ($app) {
    if ($app['session']->get('user') == null) {
        return $app->redirect('/login');
    } else {
        return $app->redirect('/welcome');
    }
})
->bind('homepage');

// login controllers
$app->match('/login', function (Request $request) use ($app) {
    // $request = $app['request_stack']->getCurrentRequest();
    $data = [
        'error' => false,
        'message' => '',
        'username' => '',
    ];
    $form = $app['form.factory']->createBuilder(FormType::class, $data)
        ->add('username', TextType::class, ['required' => true])
        ->add('password', PasswordType::class, ['required' => true])
        ->add('submit', SubmitType::class, ['label' => 'Enter'])
        ->getForm();
    
    if ($request) {
        $form->handleRequest($request);
        $data = $form->getData();
    }
    if (empty($data['username']) || empty($data['password'])) {
        // display the form
        return $app['twig']->render('login.html.twig', $data);
    } else {
        $user = new User($data['username'], $app['db']);
        $authenticated = $user->authenticate($app['db'], $data['password']);
        if ($authenticated) {
            $app['session']->set('user', $user);
            return $app->redirect('/welcome');
        } else {
            $data['error'] = true;
            $data['message'] = "no match found";
            return $app['twig']->render('login.html.twig', $data);
        }
    }
});
$app->error(function (\Exception $e, Request $request, $code) use ($app) {
    if ($app['debug']) {
        return;
    }

    // 404.html, or 40x.html, or 4xx.html, or error.html
    $templates = array(
        'errors/' . $code .'.html.twig',
        'errors/' . substr($code, 0, 2) .'x.html.twig',
        'errors/' . substr($code, 0, 1) .'xx.html.twig',
        'errors/default.html.twig',
    );

    return new Response($app['twig']->resolveTemplate($templates)->render(array('code' => $code)), $code);
});
