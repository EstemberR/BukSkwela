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
        $this->driveService = $driveService;
    }

    public function index()
    {
        return view('tenant.requirements.index');
    }

    public function getFolderContents(Request $request, $folderId = null)
    {
        try {
            $contents = $this->driveService->listFolderContents($folderId);
            $category = $request->query('category', 'Regular');
            
            if (isset($contents['files'])) {
                $filteredFiles = array_filter($contents['files'], function($file) use ($category) {
                    $tenantId = tenant('id');
                    $name = is_array($file) ? $file['name'] : $file->getName();
                    $mimeType = is_array($file) ? $file['mimeType'] : $file->getMimeType();
                    $isFolder = strpos($mimeType, 'folder') !== false;
                    $hasPrefix = str_starts_with($name, "[{$tenantId}]");
                    
                    // Always process folders, and files with tenant prefix
                    if ($isFolder || $hasPrefix) {
                        if (is_array($file)) {
                            $file['formatted_name'] = $hasPrefix ? str_replace("[{$tenantId}]", '', $name) : $name;
                            $file['is_folder'] = $isFolder;
                            $file['type'] = $isFolder ? 'folder' : 'file';
                            $file['last_modified'] = isset($file['modifiedTime']) ? 
                                date('Y-m-d H:i:s', strtotime($file['modifiedTime'])) : '';
                            $file['size'] = isset($file['size']) ? $file['size'] : 0;
                            $file['web_view_link'] = isset($file['webViewLink']) ? $file['webViewLink'] : '';
                        } else {
                            $file->formatted_name = $hasPrefix ? str_replace("[{$tenantId}]", '', $name) : $name;
                            $file->is_folder = $isFolder;
                            $file->type = $isFolder ? 'folder' : 'file';
                            $file->last_modified = $file->getModifiedTime() ? 
                                date('Y-m-d H:i:s', strtotime($file->getModifiedTime())) : '';
                            $file->size = $file->getSize() ?? 0;
                            $file->web_view_link = $file->getWebViewLink() ?? '';
                        }
                        return true;
                    }
                    
                    return false;
                });
                
                $contents['files'] = array_values($filteredFiles);
            }
            
            return response()->json([
                'success' => true,
                'files' => $contents['files'] ?? [],
                'path' => $contents['path'] ?? [],
                'notice' => $contents['notice'] ?? null,
                'current_folder' => $folderId ?? 'root',
                'total_items' => count($contents['files'] ?? [])
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
                'message' => 'Failed to load folder contents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function listFolderContents(Request $request, $folderId = null)
    {
        try {
            $contents = $this->driveService->listFolderContents($folderId);
            $category = $request->query('category', 'Regular');
            $tenantId = tenant('id');

            // Filter contents by category
            $filteredContents = collect($contents['files'])->filter(function ($item) use ($category, $tenantId) {
                // Check if it's a folder
                $mimeType = is_array($item) ? $item['mimeType'] : $item->getMimeType();
                if ($mimeType !== 'application/vnd.google-apps.folder') {
                    return false;
                }

                // Get the name without tenant prefix
                $name = is_array($item) ? $item['name'] : $item->getName();
                if (str_starts_with($name, "[{$tenantId}]")) {
                    $name = str_replace("[{$tenantId}] ", "", $name);
                }

                // Extract category from folder name
                $categoryMatch = preg_match('/\[(Regular|Irregular|Probation)\]/', $name, $matches);
                $itemCategory = $categoryMatch ? $matches[1] : 'Regular';

                // Return true only if the category matches
                return $itemCategory === $category;
            })->map(function ($item) use ($tenantId) {
                // Clean up the display name
                $name = is_array($item) ? $item['name'] : $item->getName();
                if (str_starts_with($name, "[{$tenantId}]")) {
                    $name = str_replace("[{$tenantId}] ", "", $name);
                }
                
                // Convert DriveFile object to array with necessary properties
                $fileData = [];
                if (is_array($item)) {
                    $fileData = $item;
                } else {
                    $fileData = [
                        'id' => $item->getId(),
                        'name' => $item->getName(),
                        'mimeType' => $item->getMimeType(),
                        'modifiedTime' => $item->getModifiedTime(),
                        'webViewLink' => $item->getWebViewLink(),
                    ];
                }
                
                $fileData['display_name'] = $name;
                $fileData['original_name'] = is_array($item) ? $item['name'] : $item->getName();
                
                return $fileData;
            })->values()->all();

            return response()->json([
                'success' => true,
                'files' => $filteredContents,
                'path' => $contents['path'] ?? [],
                'currentFolder' => [
                    'id' => $folderId ?? 'root'
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('List folder contents error', [
                'error' => $e->getMessage(),
                'folder_id' => $folderId,
                'category' => $category,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error listing folder contents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function uploadFile(Request $request)
    {
        try {
            $file = $request->file('file');
            $folderId = $request->input('folderId');
            
            $uploadedFile = $this->driveService->uploadFile($file, $folderId);
            return response()->json([
                'success' => true,
                'file' => $uploadedFile
            ]);
        } catch (Exception $e) {
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

            $tenantId = tenant('id');
            // Format: [TenantID] [Category] FolderName
            $formattedName = "[{$tenantId}] [{$category}] {$folderName}";
            
            $folder = $this->driveService->createFolder($formattedName, $parentId);
            
            // Add display information to the response
            $folder['display_name'] = "[{$category}] {$folderName}";
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
}
