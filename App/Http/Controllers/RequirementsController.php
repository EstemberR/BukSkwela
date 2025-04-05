<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\GoogleDriveService;
use Exception;
use Illuminate\Validation\ValidationException;

class RequirementsController extends Controller
{
    protected $driveService;

    public function __construct(GoogleDriveService $googleDrive)
    {
        $this->middleware('auth');
        $this->driveService = $googleDrive;
    }

    public function getFolderContents(Request $request, $folderId = null)
    {
        try {
            $contents = $this->driveService->listFolderContents($folderId);
            return response()->json([
                'success' => true,
                'contents' => $contents['files'],
                'path' => $contents['path'] ?? []
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get folder contents: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createFolder(Request $request)
    {
        try {
            $folderName = $request->input('name');
            $folderType = $request->input('folderType', 'Regular');
            $parentId = $request->input('parentId');
            
            if (empty($folderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder name is required'
                ], 422);
            }
            
            // Format folder name with category
            $formattedName = "[$folderType] $folderName";
            
            // Create folder with formatted name
            $folder = $this->driveService->createFolder(
                $formattedName,
                $parentId
            );

            // Add category to the response
            $folder['category'] = $folderType;
            
            return response()->json([
                'success' => true,
                'folder' => $folder,
                'message' => 'Folder created successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage()
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

    public function rename(Request $request, $id)
    {
        try {
            $newName = $request->input('newName');
            $file = $this->driveService->renameFile($id, $newName);
            return response()->json([
                'success' => true,
                'file' => $file
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to rename item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function delete($id)
    {
        try {
            $this->driveService->deleteFile($id);
            return response()->json([
                'success' => true,
                'message' => 'Item deleted successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete item: ' . $e->getMessage()
            ], 500);
        }
    }

    public function index()
    {
        try {
            $contents = $this->driveService->listFolderContents();
            return view('tenant.requirements.index', [
                'contents' => $contents['files'],
                'path' => $contents['path'] ?? []
            ]);
        } catch (Exception $e) {
            return back()->with('error', 'Failed to load requirements: ' . $e->getMessage());
        }
    }

    public function deleteFile($fileId)
    {
        try {
            // Initialize Google Drive client
            $googleDrive = new GoogleDriveService();
            
            // Delete the file
            $deleted = $googleDrive->deleteFile($fileId);

            if ($deleted) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete file'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting file: ' . $e->getMessage()
            ]);
        }
    }

    public function listFolderContents(Request $request)
    {
        try {
            $folderId = $request->query('folderId', null);
            $category = $request->query('category', 'Regular');

            // Get folder contents from Google Drive
            $contents = $this->driveService->listFolderContents($folderId);

            // Filter contents by category
            $filteredContents = collect($contents['files'])->filter(function ($item) use ($category) {
                if ($item['mimeType'] !== 'application/vnd.google-apps.folder') {
                    return false;
                }

                // Extract category from folder name
                $categoryMatch = preg_match('/\[(Regular|Irregular|Probation)\]/', $item['name'], $matches);
                $itemCategory = $categoryMatch ? $matches[1] : 'Regular';

                // Return true only if the category matches
                return $itemCategory === $category;
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
            return response()->json([
                'success' => false,
                'message' => 'Error listing folder contents: ' . $e->getMessage()
            ], 500);
        }
    }
}