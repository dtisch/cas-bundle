<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EcPhp\CasBundle\Configuration\Symfony;
use EcPhp\CasBundle\Controller\Homepage;
use EcPhp\CasBundle\Controller\Login;
use EcPhp\CasBundle\Controller\Logout;
use EcPhp\CasBundle\Controller\ProxyCallback;
use EcPhp\CasBundle\Security\CasGuardAuthenticator;
use EcPhp\CasBundle\Security\Core\User\CasUserProvider;
use EcPhp\CasLib\Cas;
use EcPhp\CasLib\CasInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;

return static function (ContainerConfigurator $container) {
    $container
        ->services()
        ->set('cas', Cas::class)
        ->args([
            service('psr.request'),
            service('cas.configuration'),
            service('psr18.http_client'),
            service('nyholm.psr7.psr17_factory'),
            service('nyholm.psr7.psr17_factory'),
            service('nyholm.psr7.psr17_factory'),
            service('nyholm.psr7.psr17_factory'),
            service('cache.app'),
            service('logger'),
        ]);

    $container
        ->services()
        ->alias(CasInterface::class, 'cas');

    $container
        ->services()
        ->set('cas.configuration', Symfony::class)
        ->args([
            '%cas%',
            service('router'),
        ]);

    $container
        ->services()
        ->set('cas.userprovider', CasUserProvider::class);

    $container
        ->services()
        ->set('cas.guardauthenticator', CasGuardAuthenticator::class)
        ->autowire(true)
        ->autoconfigure(true);

    $container
        ->services()
        ->set(Homepage::class)
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');

    $container
        ->services()
        ->set(Login::class)
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');

    $container
        ->services()
        ->set(Logout::class)
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');

    $container
        ->services()
        ->set(ProxyCallback::class)
        ->autowire(true)
        ->autoconfigure(true)
        ->tag('controller.service_arguments');

    $container
        ->services()
        ->set('symfony.request', RequestStack::class)
        ->factory([
            service('request_stack'),
            'getCurrentRequest',
        ])
        ->private();

    $container
        ->services()
        ->set(RequestInterface::class)
        ->factory([
            service('sensio_framework_extra.psr7.http_message_factory'),
            'createRequest',
        ])
        ->args([
            service('symfony.request'),
        ]);

    $container
        ->services()
        ->alias('psr.request', RequestInterface::class)
        ->public();

    $container
        ->services()
        ->alias(HttpMessageFactoryInterface::class, 'sensio_framework_extra.psr7.http_message_factory');

    $container
        ->services()
        ->alias(HttpFoundationFactoryInterface::class, 'sensio_framework_extra.psr7.http_foundation_factory');
};
