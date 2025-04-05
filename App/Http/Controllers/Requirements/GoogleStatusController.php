<?php

namespace App\Http\Controllers\Requirements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoogleStatusController extends Controller
{
    public function checkStatus()
    {
        try {
            // Check Google configuration
            $config = config('services.google');
            $configStatus = [
                'client_id_exists' => !empty($config['client_id']),
                'client_secret_exists' => !empty($config['client_secret']),
                'redirect_uri_exists' => !empty($config['redirect_uri']),
                'refresh_token_exists' => !empty($config['refresh_token']),
                'root_folder_id_exists' => !empty($config['root_folder_id']),
                'config_values' => [
                    'client_id' => substr($config['client_id'] ?? '', 0, 10) . '...',
                    'client_secret' => substr($config['client_secret'] ?? '', 0, 5) . '...',
                    'redirect_uri' => $config['redirect_uri'] ?? 'not set',
                    'refresh_token' => substr($config['refresh_token'] ?? '', 0, 5) . '...',
                    'root_folder_id' => $config['root_folder_id'] ?? 'not set',
                ]
            ];
            
            // Check Google client
            $client = new \Google\Client();
            $client->setClientId($config['client_id']);
            $client->setClientSecret($config['client_secret']);
            $client->setRedirectUri($config['redirect_uri']);
            $client->setAccessType('offline');
            
            // Check scopes
            $client->addScope(\Google\Service\Drive::DRIVE);
            
            // Disable SSL verification in development
            if (app()->environment('local')) {
                $client->setHttpClient(
                    new \GuzzleHttp\Client([
                        'verify' => false
                    ])
                );
            }
            
            // Try to get refresh token
            $hasValidToken = false;
            $tokenError = null;
            
            if (!empty($config['refresh_token'])) {
                try {
                    $client->fetchAccessTokenWithRefreshToken($config['refresh_token']);
                    $hasValidToken = $client->getAccessToken() !== null;
                } catch (\Exception $e) {
                    $tokenError = $e->getMessage();
                }
            }
            
            // Try listing files as a test
            $driveAccessible = false;
            $driveError = null;
            
            if ($hasValidToken) {
                try {
                    $service = new \Google\Service\Drive($client);
                    $files = $service->files->listFiles([
                        'pageSize' => 5,
                        'fields' => 'files(id, name)'
                    ]);
                    $driveAccessible = true;
                    $filesList = array_map(function($file) {
                        return ['id' => $file->getId(), 'name' => $file->getName()];
                    }, $files->getFiles());
                } catch (\Exception $e) {
                    $driveError = $e->getMessage();
                }
            }
            
            return response()->json([
                'success' => true,
                'config_status' => $configStatus,
                'client_status' => [
                    'initialized' => $client !== null,
                    'has_valid_token' => $hasValidToken,
                    'token_error' => $tokenError,
                ],
                'drive_status' => [
                    'accessible' => $driveAccessible,
                    'error' => $driveError,
                    'files_sample' => $filesList ?? []
                ],
                'php_version' => PHP_VERSION,
                'environment' => app()->environment(),
                'time' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            Log::error('Google Drive status check error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check Google Drive status: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
    
    public function getAuthUrl()
    {
        try {
            $config = config('services.google');
            $client = new \Google\Client();
            $client->setClientId($config['client_id']);
            $client->setClientSecret($config['client_secret']);
            $client->setRedirectUri($config['redirect_uri']);
            $client->setAccessType('offline');
            $client->setPrompt('consent'); // Force to get refresh token
            $client->setIncludeGrantedScopes(true);
            $client->addScope(\Google\Service\Drive::DRIVE);
            
            $authUrl = $client->createAuthUrl();
            
            return response()->json([
                'success' => true,
                'auth_url' => $authUrl,
                'instructions' => 'Visit this URL in your browser, authorize the app, and copy the code from the redirect URL'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate auth URL: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function handleCallback(Request $request)
    {
        try {
            $code = $request->input('code');
            if (empty($code)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authorization code is required'
                ], 400);
            }
            
            $config = config('services.google');
            $client = new \Google\Client();
            $client->setClientId($config['client_id']);
            $client->setClientSecret($config['client_secret']);
            $client->setRedirectUri($config['redirect_uri']);
            
            $token = $client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['refresh_token'])) {
                return response()->json([
                    'success' => true,
                    'refresh_token' => $token['refresh_token'],
                    'instructions' => 'Add this refresh_token to your .env file as GOOGLE_DRIVE_REFRESH_TOKEN'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No refresh token received. Make sure you have set access_type=offline and prompt=consent',
                    'token_data' => $token
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to exchange code for token: ' . $e->getMessage()
            ], 500);
        }
    }
} 