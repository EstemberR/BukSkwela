<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client as GuzzleClient;
use Exception;

class GoogleDriveService
{
    protected $client;
    public $service;
    protected $rootFolderId;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    protected function initializeGoogleClient()
    {
        try {
            $this->client = new Client();
            
            // Disable SSL verification in development environment
            if (app()->environment('local')) {
                $this->client->setHttpClient(
                    new GuzzleClient([
                        'verify' => false,
                        'timeout' => 60,
                        'connect_timeout' => 60
                    ])
                );
            }

            // Set client configuration
            $this->client->setClientId(config('services.google.client_id'));
            $this->client->setClientSecret(config('services.google.client_secret'));
            $this->client->setRedirectUri(config('services.google.redirect_uri'));
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            
            // Add required scopes
            $this->client->addScope(Drive::DRIVE);
            $this->client->addScope(Drive::DRIVE_FILE);
            $this->client->addScope(Drive::DRIVE_METADATA);

            // Try to get refresh token first from direct config
            $refreshToken = config('services.google.refresh_token');
            
            // If not found, try the old key for backward compatibility
            if (empty($refreshToken)) {
                $refreshToken = env('GOOGLE_DRIVE_REFRESH_TOKEN');
            }
            
            if (!empty($refreshToken)) {
                Log::info('Using refresh token to get access token');
                
                try {
                    // Fetch a new access token using the refresh token
                    $accessToken = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
                    
                    if (isset($accessToken['access_token'])) {
                        Log::info('Successfully obtained access token');
                    } else {
                        Log::warning('No access token returned', ['response' => $accessToken]);
                    }
                    
                    if (isset($accessToken['error'])) {
                        Log::error('Error getting access token', ['error' => $accessToken['error']]);
                    }
                } catch (Exception $e) {
                    Log::error('Error refreshing access token: ' . $e->getMessage());
                    // Continue anyway, maybe we can still initialize the service
                }
            } else {
                Log::warning('No refresh token found in configuration');
            }

            // Initialize Google Drive service
            $this->service = new Drive($this->client);
            
            // Try to get root folder ID from config
            $this->rootFolderId = config('services.google.root_folder_id');
            
            // If no root folder ID in config, try to find or create it
            if (!$this->rootFolderId) {
                $this->findOrCreateRootFolder();
            }
            
            Log::info('Google Drive service initialized successfully', [
                'root_folder_id' => $this->rootFolderId
            ]);
            
        } catch (Exception $e) {
            Log::error('Google Drive initialization error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Failed to initialize Google Drive service: ' . $e->getMessage());
        }
    }

    /**
     * Get the root folder ID
     * 
     * @return string|null The root folder ID
     */
    public function getRootFolderId()
    {
        if (!$this->rootFolderId) {
            try {
                $this->findOrCreateRootFolder();
            } catch (\Exception $e) {
                Log::error('Failed to get root folder ID: ' . $e->getMessage());
                return null;
            }
        }
        return $this->rootFolderId;
    }

    protected function findOrCreateRootFolder()
    {
        try {
            Log::info('Finding or creating root folder');
            
            // Search for existing root folder
            $rootSearch = $this->service->files->listFiles([
                'q' => "name = 'BukSkwela Requirements' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
                'fields' => 'files(id, name)',
                'pageSize' => 1,
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true
            ]);
            
            $rootFiles = $rootSearch->getFiles();
            
            if (count($rootFiles) > 0) {
                $this->rootFolderId = $rootFiles[0]->getId();
                Log::info('Found existing root folder', ['id' => $this->rootFolderId]);
            } else {
                Log::info('No existing root folder found, creating new one');
                // Create new root folder
                $fileMetadata = new DriveFile([
                    'name' => 'BukSkwela Requirements',
                    'mimeType' => 'application/vnd.google-apps.folder'
                ]);
                
                $folder = $this->service->files->create($fileMetadata, [
                    'fields' => 'id',
                    'supportsAllDrives' => true
                ]);
                
                $this->rootFolderId = $folder->getId();
                Log::info('Created new root folder', ['id' => $this->rootFolderId]);
            }
            
            // Save the root folder ID to the configuration
            config(['services.google.root_folder_id' => $this->rootFolderId]);
            
            // Update the .env file
            try {
                $envPath = base_path('.env');
                if (file_exists($envPath)) {
                    $envContent = file_get_contents($envPath);
                    if (strpos($envContent, 'GOOGLE_DRIVE_ROOT_FOLDER_ID=') !== false) {
                        $envContent = preg_replace(
                            '/GOOGLE_DRIVE_ROOT_FOLDER_ID=.*/',
                            'GOOGLE_DRIVE_ROOT_FOLDER_ID=' . $this->rootFolderId,
                            $envContent
                        );
                    } else {
                        $envContent .= "\nGOOGLE_DRIVE_ROOT_FOLDER_ID=" . $this->rootFolderId;
                    }
                    file_put_contents($envPath, $envContent);
                    Log::info('Updated .env file with root folder ID');
                }
            } catch (\Exception $e) {
                Log::warning('Failed to update .env file: ' . $e->getMessage());
                // Continue anyway since we have the root folder ID in memory
            }
            
            return $this->rootFolderId;
        } catch (Exception $e) {
            Log::error('Error finding/creating root folder: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            throw new Exception('Failed to initialize root folder: ' . $e->getMessage());
        }
    }

    public function listRootContents()
    {
        try {
            Log::info('Attempting to list root contents');
            
            $rootFolderId = $this->getRootFolderId();
            if (!$rootFolderId) {
                Log::error('Root folder ID not found');
                return [
                    'success' => false,
                    'message' => 'Root folder ID not found',
                    'files' => [],
                    'path' => []
                ];
            }

            Log::info('Using root folder ID', ['root_folder_id' => $rootFolderId]);

            $query = "'{$rootFolderId}' in parents and trashed = false";
            $optParams = [
                'q' => $query,
                'fields' => 'files(id, name, mimeType, modifiedTime, size)',
                'orderBy' => 'name'
            ];

            Log::debug('Executing drive query', ['query' => $query, 'params' => $optParams]);

            $results = $this->service->files->listFiles($optParams);
            $files = $results->getFiles();

            Log::info('Retrieved files from root folder', [
                'file_count' => count($files),
                'sample_files' => array_slice(array_map(function($file) {
                    return $file->getName();
                }, $files), 0, 5)
            ]);

            return [
                'success' => true,
                'files' => $files,
                'path' => [
                    [
                        'id' => $rootFolderId,
                        'name' => 'BukSkwela Requirements'
                    ]
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Failed to list root contents', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Failed to list root contents: ' . $e->getMessage(),
                'files' => [],
                'path' => []
            ];
        }
    }

    public function listFolderContents($folderId = null)
    {
        try {
            Log::info('Listing folder contents', [
                'folder_id' => $folderId,
                'is_root' => $folderId === null || $folderId === 'root' || $folderId === '.',
                'root_folder_id' => $this->rootFolderId
            ]);

            // For root folder or invalid folder IDs, use listRootContents
            if (!$folderId || $folderId === 'root' || $folderId === '.' || $folderId === '') {
                Log::info('Redirecting to root contents');
                return $this->listRootContents();
            }

            // Try to verify the folder exists
            try {
                $folder = $this->service->files->get($folderId, [
                    'fields' => 'id, name, mimeType',
                    'supportsAllDrives' => true
                ]);
                
                if ($folder->getMimeType() !== 'application/vnd.google-apps.folder') {
                    Log::error('Requested ID is not a folder', ['id' => $folderId]);
                    throw new Exception('The specified ID is not a folder');
                }

                Log::info('Found folder', [
                    'id' => $folder->getId(),
                    'name' => $folder->getName(),
                    'type' => $folder->getMimeType()
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to verify folder', [
                    'folder_id' => $folderId,
                    'error' => $e->getMessage()
                ]);
                throw new Exception('Folder not found or inaccessible');
            }

            // List the folder's contents
            $query = "trashed = false and '" . $folderId . "' in parents";
            Log::info('Drive query', ['query' => $query]);

            $optParams = [
                'q' => $query,
                'fields' => 'files(id, name, mimeType, modifiedTime, webViewLink, parents, size, thumbnailLink)',
                'orderBy' => 'folder,name',
                'pageSize' => 1000,
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true
            ];

            Log::debug('Executing drive query with params', ['params' => $optParams]);

            $result = $this->service->files->listFiles($optParams);
            $files = $result->getFiles();
            
            // Convert Google Drive files to array format
            $filesArray = array_map(function($file) {
                return [
                    'id' => $file->getId(),
                    'name' => $file->getName(),
                    'mimeType' => $file->getMimeType(),
                    'modifiedTime' => $file->getModifiedTime(),
                    'webViewLink' => $file->getWebViewLink(),
                    'parents' => $file->getParents(),
                    'size' => $file->getSize(),
                    'thumbnailLink' => $file->getThumbnailLink(),
                    'isFolder' => $file->getMimeType() === 'application/vnd.google-apps.folder'
                ];
            }, $files);

            // Log the types of files found
            $fileTypes = array_count_values(array_map(function($file) {
                return $file['mimeType'];
            }, $filesArray));
            
            Log::info('Files retrieved', [
                'count' => count($filesArray),
                'folder_id' => $folderId,
                'file_types' => $fileTypes,
                'sample_files' => array_slice(array_map(function($file) {
                    return [
                        'name' => $file['name'],
                        'type' => $file['mimeType'],
                        'isFolder' => $file['isFolder']
                    ];
                }, $filesArray), 0, 5)
            ]);

            // Build the path for breadcrumb navigation
            $path = $this->buildFolderPath($folderId);

            return [
                'success' => true,
                'files' => $filesArray,
                'path' => $path,
                'debug_info' => [
                    'folder_id' => $folderId,
                    'root_folder_id' => $this->rootFolderId,
                    'file_count' => count($filesArray),
                    'has_path' => !empty($path)
                ]
            ];
        } catch (\Exception $e) {
            Log::error('Error listing folder contents', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to list folder contents: ' . $e->getMessage(),
                'files' => [],
                'path' => [],
                'debug_info' => [
                    'folder_id' => $folderId,
                    'root_folder_id' => $this->rootFolderId,
                    'error' => $e->getMessage()
                ]
            ];
        }
    }

    /**
     * Build the folder path for breadcrumb navigation
     */
    private function buildFolderPath($folderId)
    {
        try {
            $path = [];
            $currentId = $folderId;
            $maxDepth = 10; // Prevent infinite loops
            $depth = 0;

            while ($currentId && $depth < $maxDepth) {
                $file = $this->service->files->get($currentId, [
                    'fields' => 'id, name, parents',
                    'supportsAllDrives' => true
                ]);
                
                $path[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName()
                ];

                $parents = $file->getParents();
                if (!$parents || empty($parents) || $parents[0] === 'root') {
                    break;
                }

                $currentId = $parents[0];
                $depth++;
            }

            return array_reverse($path);
        } catch (\Exception $e) {
            Log::error('Error building folder path', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId
            ]);
            return [];
        }
    }

    public function createFolder($name, $parentId = null)
    {
        try {
            Log::info('Creating folder', [
                'name' => $name,
                'parent_id' => $parentId,
                'root_folder_id' => $this->rootFolderId
            ]);

            // Create drive file metadata
            $fileMetadata = new DriveFile();
            $fileMetadata->setName($name);
            $fileMetadata->setMimeType('application/vnd.google-apps.folder');
            
            // If no parent ID is provided, use the root folder ID
            if (!$parentId || $parentId === 'root' || $parentId === '.') {
                // Ensure we have a root folder ID
                if (!$this->rootFolderId) {
                    Log::info('No root folder ID found, attempting to find or create it');
                    $this->findOrCreateRootFolder();
                }
                
                if (!$this->rootFolderId) {
                    throw new Exception('Failed to initialize root folder');
                }
                
                Log::info('Using root folder as parent', ['root_folder_id' => $this->rootFolderId]);
                $fileMetadata->setParents([$this->rootFolderId]);
            } else {
                // Verify the parent folder exists
                try {
                    $parentFolder = $this->service->files->get($parentId, [
                        'fields' => 'id, name, mimeType',
                        'supportsAllDrives' => true
                    ]);
                    
                    if ($parentFolder->getMimeType() !== 'application/vnd.google-apps.folder') {
                        throw new Exception('The specified parent ID is not a folder');
                    }
                    
                    Log::info('Using specified parent folder', ['parent_id' => $parentId]);
                    $fileMetadata->setParents([$parentId]);
                } catch (\Google\Service\Exception $e) {
                    Log::error('Failed to verify parent folder', [
                        'parent_id' => $parentId,
                        'error' => $e->getMessage()
                    ]);
                    throw new Exception('Parent folder not found or inaccessible');
                }
            }
            
            // Create the folder with metadata
            $optParams = [
                'fields' => 'id, name, mimeType, modifiedTime, webViewLink, parents',
                'supportsAllDrives' => true
            ];
            
            $folder = $this->service->files->create($fileMetadata, $optParams);
            
            Log::info('Folder created successfully', [
                'id' => $folder->getId(),
                'name' => $folder->getName(),
                'parent_id' => $parentId ?? $this->rootFolderId
            ]);
            
            return $folder;
        } catch (Exception $e) {
            Log::error('Failed to create folder', [
                'name' => $name,
                'parent_id' => $parentId ?? $this->rootFolderId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (strpos($e->getMessage(), 'Rate Limit Exceeded') !== false) {
                throw new Exception('Google Drive API rate limit exceeded. Please try again later.');
            } else if (strpos($e->getMessage(), 'insufficient permissions') !== false) {
                throw new Exception('Insufficient permissions to create folder. Please check your Google Drive access.');
            } else {
                throw new Exception('Failed to create folder: ' . $e->getMessage());
            }
        }
    }
    public function uploadFile($file, $folderId = null)
    {
        try {
            // Validate file
            if (!$file || !$file->isValid()) {
                throw new Exception('Invalid file provided');
            }

            // Validate file size (50MB limit for multipart upload)
            $maxSize = 50 * 1024 * 1024; // 50MB in bytes
            if ($file->getSize() > $maxSize) {
                throw new Exception('File size exceeds the maximum limit of 50MB');
            }

            // Validate folder ID
            if ($folderId) {
                try {
                    $folder = $this->service->files->get($folderId, [
                        'fields' => 'id, mimeType',
                        'supportsAllDrives' => true
                    ]);
                    if ($folder->getMimeType() !== 'application/vnd.google-apps.folder') {
                        throw new Exception('Invalid folder ID provided');
                    }
                } catch (\Google\Service\Exception $e) {
                    throw new Exception('Folder not found or inaccessible');
                }
            }

            // Prepare file metadata
            $fileMetadata = new DriveFile([
                'name' => $file->getClientOriginalName(),
                'parents' => [$folderId ?: $this->rootFolderId]
            ]);

            Log::info('Starting file upload', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'folder_id' => $folderId ?: $this->rootFolderId
            ]);

            // Upload file with proper error handling
            try {
                $content = file_get_contents($file->getRealPath());
                $uploadedFile = $this->service->files->create($fileMetadata, [
                    'data' => $content,
                    'mimeType' => $file->getMimeType(),
                    'uploadType' => 'multipart',
                    'fields' => 'id, name, mimeType, modifiedTime, webViewLink, size',
                    'supportsAllDrives' => true
                ]);

                Log::info('File uploaded successfully', [
                    'file_id' => $uploadedFile->getId(),
                    'name' => $uploadedFile->getName(),
                    'web_link' => $uploadedFile->getWebViewLink()
                ]);

                return $uploadedFile;
            } catch (\Google\Service\Exception $e) {
                $error = json_decode($e->getMessage(), true);
                $errorMessage = $error['error']['message'] ?? $e->getMessage();
                
                if (strpos($errorMessage, 'Rate Limit Exceeded') !== false) {
                    throw new Exception('Upload failed due to rate limit. Please try again later.');
                } elseif (strpos($errorMessage, 'insufficient permissions') !== false) {
                    throw new Exception('Insufficient permissions to upload file.');
                } else {
                    throw new Exception('Failed to upload file: ' . $errorMessage);
                }
            }
        } catch (Exception $e) {
            Log::error('File upload error', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'folder_id' => $folderId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
    public function renameFile($fileId, $newName)
    {
        try {
            $fileMetadata = new DriveFile([
                'name' => $newName
            ]);

            $file = $this->service->files->update($fileId, $fileMetadata, [
                'fields' => 'id, name, mimeType, modifiedTime, webViewLink'
            ]);

            return $file;
        } catch (Exception $e) {
            Log::error('Rename file error: ' . $e->getMessage());
            throw new Exception($e->getMessage());
        }
    }

    public function deleteFile($fileId)
    {
        try {
            Log::info('Attempting to delete file', ['file_id' => $fileId]);
            
            // Try to delete the file
            $this->service->files->delete($fileId, [
                'supportsAllDrives' => true
            ]);
            
            Log::info('File deleted successfully', ['file_id' => $fileId]);
            return true;
        } catch (Exception $e) {
            Log::error('Failed to delete file', [
                'error' => $e->getMessage(),
                'file_id' => $fileId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * List all files and folders without using parent references
     * This is a fallback method that completely avoids using folder IDs
     * 
     * @return array Files and folders list
     */
    public function listAllFilesAndFolders($limit = 100)
    {
        try {
            // Get all folders and files that are not trashed
            Log::info('Listing all files and folders directly from Google Drive', [
                'limit' => $limit, 
                'authenticated' => ($this->client && $this->client->getAccessToken() !== null)
            ]);
            
            // Use a more permissive query
            $query = "trashed = false";
            Log::info('Using query: ' . $query);
            
            $fields = 'files(id, name, mimeType, modifiedTime, webViewLink, parents), nextPageToken';
            
            $optParams = [
                'q' => $query,
                'fields' => $fields,
                'pageSize' => min($limit, 100), // Google API max page size is 100
                'orderBy' => 'modifiedTime desc',
                'supportsAllDrives' => true,
                'includeItemsFromAllDrives' => true
            ];
            
            // Try to list files
            $result = $this->service->files->listFiles($optParams);
            $files = $result->getFiles();
            
            // Handle pagination if needed
            $nextPageToken = $result->getNextPageToken();
            $pageCount = 1;
            
            while ($nextPageToken && count($files) < $limit && $pageCount < 5) {
                $optParams['pageToken'] = $nextPageToken;
                $additionalResults = $this->service->files->listFiles($optParams);
                $files = array_merge($files, $additionalResults->getFiles());
                $nextPageToken = $additionalResults->getNextPageToken();
                $pageCount++;
            }
            
            // Log the results
            $fileCount = count($files);
            Log::info('Successfully listed files and folders', [
                'count' => $fileCount,
                'pages_retrieved' => $pageCount,
                'has_more' => ($nextPageToken !== null)
            ]);
            
            // Log some sample IDs for debugging
            if ($fileCount > 0) {
                $folderIds = [];
                $fileIds = [];
                
                foreach ($files as $file) {
                    if ($file->getMimeType() === 'application/vnd.google-apps.folder') {
                        $folderIds[] = $file->getId();
                    } else {
                        $fileIds[] = $file->getId();
                    }
                    
                    if (count($folderIds) >= 5 && count($fileIds) >= 5) {
                        break;
                    }
                }
                
                Log::info('Sample folder IDs: ' . implode(', ', $folderIds));
                Log::info('Sample file IDs: ' . implode(', ', $fileIds));
            }
            
            return [
                'files' => $files,
                'path' => [],
                'total_count' => $fileCount,
                'has_more' => ($nextPageToken !== null)
            ];
        } catch (Exception $e) {
            Log::error('Error listing all files and folders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'files' => [],
                'path' => [],
                'error' => $e->getMessage()
            ];
        }
    }

    public function getFolderPath($folderId)
    {
        try {
            $path = [];
            $currentId = $folderId;

            while ($currentId) {
                $file = $this->service->files->get($currentId, ['fields' => 'id, name, parents']);
                
                $path[] = [
                    'id' => $file->getId(),
                    'name' => $file->getName()
                ];

                $parents = $file->getParents();
                $currentId = !empty($parents) ? $parents[0] : null;

                // Stop if we reach the root folder
                if ($currentId === config('services.google.root_folder_id')) {
                    break;
                }
            }

            return array_reverse($path);
        } catch (\Exception $e) {
            \Log::error('Google Drive get folder path error: ' . $e->getMessage());
            throw $e;
        }
    }
}