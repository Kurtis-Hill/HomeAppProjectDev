<?php


namespace App\Events\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class CastQueryParametersAfterDeserializationSubscriber implements EventSubscriberInterface
{
    private const PAGINATION_QUERY_PARAMS = ['page', 'limit', 'pinNumber'];

    private const GET_PARAMS_NEEDING_CASTING = [];

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getMethod() === Request::METHOD_GET) {
            $queryParams = $request->query->all();

            $paramsListeningForCasting = array_merge(self::GET_PARAMS_NEEDING_CASTING, self::PAGINATION_QUERY_PARAMS);
            foreach ($queryParams as $key => $value) {
                if (in_array($key, $paramsListeningForCasting, true)) {
                    $request->query->set($key, is_numeric($value) ? (int) $value : $value);
                }

                if (str_contains($key, 'ID')) {
                    if (is_array($value)) {
                        $request->query->set($key, array_map(function ($item) {
                            return is_numeric($item) ? (int) $item : $item;
                        }, $value));
                    } else {
                        $request->query->set($key, is_numeric($value) ? (int) $value : $value);
                    }
                }
            }
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }
}
