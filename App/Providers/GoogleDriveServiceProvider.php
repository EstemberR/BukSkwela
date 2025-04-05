<?php

namespace App\Providers;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\ServiceProvider;

class GoogleDriveServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Client::class, function ($app) {
            $client = new Client();
            $client->setClientId(config('services.google.client_id'));
            $client->setClientSecret(config('services.google.client_secret'));
            $client->setRedirectUri(config('services.google.redirect'));
            
            // Set only the required scopes
            $client->setScopes([
                'https://www.googleapis.com/auth/drive.file',
                'https://www.googleapis.com/auth/drive.metadata'
            ]);
            
            $client->setAccessType('offline');
            $client->setPrompt('consent');

            // Set refresh token if available
            if ($refreshToken = config('services.google.refresh_token')) {
                $client->setRefreshToken($refreshToken);
            }

            return $client;
        });

        $this->app->singleton(Drive::class, function ($app) {
            $client = $app->make(Client::class);
            return new Drive($client);
        });
    }
} 