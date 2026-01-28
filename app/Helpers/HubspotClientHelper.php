<?php

namespace App\Helpers;

use App\Models\User;
use HubSpot\Discovery\Discovery;
use HubSpot\Factory;

class HubspotClientHelper {

    public static function createFactory(User $user): Discovery
    {
        $handlerStack = \GuzzleHttp\HandlerStack::create();
        $handlerStack->push(
            \HubSpot\RetryMiddlewareFactory::createRateLimitMiddleware(
                \HubSpot\Delay::getConstantDelayFunction()
            )
        );

        $handlerStack->push(
            \HubSpot\RetryMiddlewareFactory::createInternalErrorsMiddleware(
                \HubSpot\Delay::getExponentialDelayFunction(2)
            )
        );

       $client = new \GuzzleHttp\Client(['handler' => $handlerStack]);
       $accessToken = self::refreshAndGetAccessToken($user);
       return Factory::createWithAccessToken($accessToken, $client);
    }

    public static function refreshAndGetAccessToken(User $user): string {
        $tokens = Factory::create()->auth()->oAuth()->tokensApi()->createToken(
            'refresh_token',
            null,
            config('services.hubspot.redirect'),
            config('services.hubspot.client_id'),
            config('services.hubspot.client_secret'),
            $user->hubspot_refreshToken
        );

        return $tokens['access_token'];
    }

}
