<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\Response;

class OptionsRequestListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 10],
        ];
    }

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        // Seulement traiter les requêtes OPTIONS (pré-vol)
        if ($request->getMethod() === 'OPTIONS') {
            // Vérifiez l'origine de la requête (devrait être https://localhost:8000)
            $origin = $request->headers->get('Origin');
            if ($origin === 'https://localhost:8000') {
                $response = new Response();
                $response->headers->set('Access-Control-Allow-Origin', 'https://localhost:8000');
                $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
                $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization');
                $response->headers->set('Access-Control-Max-Age', '3600');
                $event->setResponse($response);
            }
        }
    }
}

