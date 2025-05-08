<?php

namespace App\Http\Controllers\Requirements;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\GoogleDriveService;

class RequirementsController extends Controller
{
    protected $driveService;

    public function __construct(GoogleDriveService $driveService)
    {
        // Allow both admin and staff (instructors) to access these routes
        // This allows the controller to be used by both admins and instructors
        $this->middleware('auth:admin,staff', ['except' => ['uploadFile']]);
        $this->driveService = $driveService;
    }

    public function index()
    {
        // Check if the request is coming from an instructor
        if (auth()->guard('staff')->check() && auth()->guard('staff')->user()->role === 'instructor') {
            return view('tenant.Instructors.requirements.index');
        }
        
        // Default to admin view
        return view('tenant.requirements.index');
    }

    public function getFolderContents(Request $request, $folderId = null)
    {
        try {
            $contents = $this->driveService->listFolderContents($folderId);
            $category = $request->query('category', 'Regular');
            $tenantId = tenant('id');
            
            if (isset($contents['files'])) {
                $filteredFiles = array_filter($contents['files'], function($file) use ($tenantId) {
                    $name = is_array($file) ? $file['name'] : $file->getName();
                    $mimeType = is_array($file) ? $file['mimeType'] : $file->getMimeType();
                    $isFolder = strpos($mimeType, 'folder') !== false;
                    
                    // Check if file/folder belongs to current tenant using flexible matching
                    $hasPrefix = false;
                    
                    // First try exact prefix match
                    if (strpos($name, "[{$tenantId}]") === 0) {
                        $hasPrefix = true;
                    } else {
                        // Try to extract tenant ID for comparison
                        $tenantIdMatch = [];
                        if (preg_match('/^\[([^\]]+)\]/', $name, $tenantIdMatch)) {
                            $fileTenantId = $tenantIdMatch[1] ?? '';
                            
                            // Normalize both tenant IDs for comparison
                            $normalizedFileTenantId = strtolower(trim($fileTenantId));
                            $normalizedCurrentTenantId = strtolower(trim($tenantId));
                            
                            // Check if normalized versions match or one contains the other
                            $hasPrefix = $normalizedFileTenantId === $normalizedCurrentTenantId ||
                                strpos($normalizedFileTenantId, $normalizedCurrentTenantId) !== false ||
                                strpos($normalizedCurrentTenantId, $normalizedFileTenantId) !== false;
                                
                            \Log::debug('Flexible tenant matching in getFolderContents', [
                                'name' => $name,
                                'extracted_tenant' => $fileTenantId,
                                'normalized_file_tenant' => $normalizedFileTenantId,
                                'normalized_current_tenant' => $normalizedCurrentTenantId,
                                'match_result' => $hasPrefix
                            ]);
                        }
                    }
                    
                    return $hasPrefix;
                });
                
                $contents['files'] = array_values($filteredFiles);
            }
            
            // Add pagination info for consistency even though this method doesn't paginate
            $totalItems = count($contents['files'] ?? []);
            
            return response()->json([
                'success' => true,
                'files' => $contents['files'] ?? [],
                'path' => $contents['path'] ?? [],
                'notice' => $contents['notice'] ?? null,
                'current_folder' => $folderId ?? 'root',
                'pagination' => [
                    'total' => $totalItems,
                    'per_page' => $totalItems,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => $totalItems > 0 ? 1 : 0,
                    'to' => $totalItems
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Folder contents error', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId,
                'category' => $category,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load folder contents: ' . $e->getMessage(),
                'pagination' => [
                    'total' => 0,
                    'per_page' => 10,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0
                ]
            ], 500);
        }
    }

    public function listFolderContents(Request $request)
    {
        try {
            $folderId = $request->query('folderId', null);
            $category = $request->query('category', 'Regular');
            $tenantId = tenant('id');
            $perPage = 10;

            \Log::info('Listing folder contents', [
                'tenant_id' => $tenantId,
                'folder_id' => $folderId,
                'category' => $category
            ]);

            // Get folder contents from Google Drive
            $contents = $this->driveService->listFolderContents($folderId);

            // Log the raw contents for debugging
            \Log::info('Raw folder contents', [
                'file_count' => count($contents['files'] ?? []),
                'sample_files' => array_slice($contents['files'] ?? [], 0, 3)
            ]);

            // Make sure files array exists
            if (!isset($contents['files']) || !is_array($contents['files'])) {
                \Log::warning('Invalid contents format received from Google Drive service', [
                    'contents' => $contents
                ]);
                
                return response()->json([
                    'success' => true,
                    'files' => [],
                    'pagination' => [
                        'total' => 0,
                        'per_page' => $perPage,
                        'current_page' => 1,
                        'last_page' => 1,
                        'from' => 0,
                        'to' => 0
                    ],
                    'path' => $contents['path'] ?? [],
                    'currentFolder' => [
                        'id' => $folderId ?? 'root'
                    ],
                    'message' => 'No files found or invalid response format'
                ]);
            }

            // Filter contents by tenant and category
            $filteredContents = collect($contents['files'])->filter(function ($item) use ($category, $tenantId) {
                // First check if the item is a valid array or object
                if (!is_array($item) && !is_object($item)) {
                    \Log::warning('Invalid item in folder contents', [
                        'item' => $item
                    ]);
                    return false;
                }
                
                // Make sure mimeType exists
                $mimeType = is_array($item) ? ($item['mimeType'] ?? '') : ($item->mimeType ?? '');
                if (!$mimeType) return false;
                
                // Only include folders
                if ($mimeType !== 'application/vnd.google-apps.folder') {
                    return false;
                }

                // Get the name for checking
                $name = is_array($item) ? ($item['name'] ?? '') : ($item->name ?? '');
                if (!$name) return false;

                // Check if folder belongs to current tenant - use a more flexible pattern
                $pattern = '/\[' . preg_quote($tenantId, '/') . '\]/i'; // Case-insensitive matching
                $tenantMatch = preg_match($pattern, $name);
                
                // If no match, try more flexible matching by extracting the tenant ID
                if (!$tenantMatch) {
                    // Extract the tenant ID from the folder name
                    $tenantIdMatch = [];
                    if (preg_match('/^\[([^\]]+)\]/', $name, $tenantIdMatch)) {
                        $folderTenantId = $tenantIdMatch[1] ?? '';
                        
                        // Normalize both tenant IDs for comparison
                        $normalizedFolderTenantId = strtolower(trim($folderTenantId));
                        $normalizedCurrentTenantId = strtolower(trim($tenantId));
                        
                        // Check if normalized versions match or one contains the other
                        $tenantMatch = $normalizedFolderTenantId === $normalizedCurrentTenantId ||
                            strpos($normalizedFolderTenantId, $normalizedCurrentTenantId) !== false ||
                            strpos($normalizedCurrentTenantId, $normalizedFolderTenantId) !== false;
                        
                        \Log::debug('Flexible tenant matching', [
                            'name' => $name,
                            'extracted_tenant' => $folderTenantId,
                            'normalized_folder_tenant' => $normalizedFolderTenantId,
                            'normalized_current_tenant' => $normalizedCurrentTenantId,
                            'match_result' => $tenantMatch
                        ]);
                    }
                }
                
                \Log::debug('Checking folder tenant match', [
                    'folder_name' => $name,
                    'tenant_id' => $tenantId,
                    'has_match' => $tenantMatch ? 'Yes' : 'No'
                ]);
                
                if (!$tenantMatch) {
                    return false;
                }

                // Extract category from folder name
                $categoryMatch = preg_match('/\[(Regular|Irregular|Probation)\]/i', $name, $matches); // Case-insensitive
                $itemCategory = $categoryMatch ? $matches[1] : 'Regular';
                
                // Case-insensitive category comparison
                $categoryMatches = (strtolower($itemCategory) === strtolower($category));

                // Return true only if the category matches
                return $categoryMatches;
            })->values();

            \Log::info('Filtered contents', [
                'count' => $filteredContents->count(),
                'items' => $filteredContents->take(3)
            ]);

            // Paginate the filtered contents
            $page = $request->query('page', 1);
            $paginatedContents = new \Illuminate\Pagination\LengthAwarePaginator(
                $filteredContents->forPage($page, $perPage),
                $filteredContents->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // Handle null pagination values
            $paginationData = [
                'total' => $paginatedContents->total(),
                'per_page' => $paginatedContents->perPage(),
                'current_page' => $paginatedContents->currentPage(),
                'last_page' => $paginatedContents->lastPage(),
                'from' => $paginatedContents->firstItem() ?: 0,
                'to' => $paginatedContents->lastItem() ?: 0
            ];

            return response()->json([
                'success' => true,
                'files' => $paginatedContents->items(),
                'pagination' => $paginationData,
                'path' => $contents['path'] ?? [],
                'currentFolder' => [
                    'id' => $folderId ?? 'root'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Folder contents error', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId ?? 'null',
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error listing folder contents: ' . $e->getMessage(),
                'pagination' => [
                    'total' => 0,
                    'per_page' => 10,
                    'current_page' => 1,
                    'last_page' => 1,
                    'from' => 0,
                    'to' => 0
                ],
                'files' => []
            ], 500);
        }
    }

    public function uploadFile(Request $request, $folderId)
    {
        try {
            // Ensure this is being accessed by either an admin or a student
            if (!auth('admin')->check() && !auth('student')->check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access'
                ], 401);
            }
            
            $file = $request->file('file');
            
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file uploaded'
                ], 422);
            }

            // Get tenant ID
            $tenantId = tenant('id');
            
            // Get original filename
            $originalName = $file->getClientOriginalName();
            
            // Use custom filename if provided, otherwise create a tenant-prefixed name
            $customFilename = $request->input('custom_filename');
            $uploadFilename = $customFilename ?: "[{$tenantId}] {$originalName}";
            
            // Log upload attempt for auditing
            $userType = auth('admin')->check() ? 'admin' : 'student';
            $userId = auth('admin')->check() ? auth('admin')->id() : auth('student')->id();
            
            \Log::info('File upload attempt', [
                'user_type' => $userType,
                'user_id' => $userId,
                'folder_id' => $folderId,
                'original_filename' => $originalName,
                'upload_filename' => $uploadFilename,
                'tenant_id' => $tenantId
            ]);
            
            // Upload file to Google Drive folder
            $result = $this->driveService->uploadFile($file, $folderId, $uploadFilename);
            
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file' => $result
            ]);
        } catch (\Exception $e) {
            \Log::error('Upload file error', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteFile($fileId)
    {
        try {
            $this->driveService->deleteFile($fileId);
            return response()->json([
                'success' => true,
                'message' => 'File deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete file error', [
                'error' => $e->getMessage(),
                'file_id' => $fileId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createFolder(Request $request)
    {
        try {
            $folderName = $request->input('name');
            $parentId = $request->input('parent_id');
            $category = $request->input('category', 'Regular');
            
            if (empty($folderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder name is required'
                ], 422);
            }

            // Validate category
            if (!in_array($category, ['Regular', 'Irregular', 'Probation'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid category'
                ], 422);
            }

            $tenantId = tenant('id');
            
            // Clean the folder name first
            $cleanFolderName = trim(str_replace(['[', ']'], '', $folderName));
            
            // Format: [TenantID] [Category] FolderName
            $formattedName = "[{$tenantId}] [{$category}] {$cleanFolderName}";
            
            $folder = $this->driveService->createFolder($formattedName, $parentId);
            
            // Add display information to the response
            $folder['display_name'] = "[{$category}] {$cleanFolderName}";
            $folder['category'] = $category;
            
            return response()->json([
                'success' => true,
                'folder' => $folder,
                'message' => 'Folder created successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Create folder error', [
                'error' => $e->getMessage(),
                'folder_name' => $folderName ?? null,
                'category' => $category ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Rename a folder
     */
    public function renameFolder(Request $request, $folderId)
    {
        try {
            $newName = $request->input('name');
            
            if (empty($newName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'New folder name is required'
                ], 422);
            }
            
            // Get folder details to preserve category and tenant information
            $folderDetails = $this->driveService->getFolderDetails($folderId);
            
            if (!$folderDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder not found'
                ], 404);
            }
            
            $originalName = $folderDetails['name'] ?? '';
            $tenantId = tenant('id');
            
            // Extract category from original name
            $categoryMatch = preg_match('/\[(Regular|Irregular|Probation)\]/', $originalName, $matches);
            $category = $categoryMatch ? $matches[1] : 'Regular';
            
            // Clean the new folder name
            $cleanFolderName = trim(str_replace(['[', ']'], '', $newName));
            
            // Format: [TenantID] [Category] NewFolderName
            $formattedName = "[{$tenantId}] [{$category}] {$cleanFolderName}";
            
            // Rename the folder
            $result = $this->driveService->renameFile($folderId, $formattedName);
            
            if (!$result) {
                throw new \Exception('Failed to rename folder');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Folder renamed successfully',
                'folder' => [
                    'id' => $folderId,
                    'name' => $formattedName,
                    'display_name' => $cleanFolderName
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Rename folder error', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId,
                'new_name' => $newName ?? null,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to rename folder: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a folder
     */
    public function deleteFolder($folderId)
    {
        try {
            // Check if the folder exists
            $folderDetails = $this->driveService->getFolderDetails($folderId);
            
            if (!$folderDetails) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder not found'
                ], 404);
            }
            
            // Delete the folder
            $result = $this->driveService->deleteFile($folderId);
            
            if (!$result) {
                throw new \Exception('Failed to delete folder');
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Folder deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Delete folder error', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete folder: ' . $e->getMessage()
            ], 500);
        }
    }
}
