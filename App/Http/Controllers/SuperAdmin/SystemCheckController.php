<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SystemCheckController extends Controller
{
    /**
     * Run MySQL connection check and show results
     */
    public function checkMySQLConnections()
    {
        try {
            // Run the command
            Artisan::call('system:check-mysql');
            
            // Get the output
            $output = Artisan::output();
            
            // Determine status based on output content
            if (strpos($output, 'WARNING:') !== false) {
                $status = 'warning';
            } elseif (strpos($output, 'CAUTION:') !== false) {
                $status = 'warning';
            } else {
                $status = 'success';
            }
            
            return view('super-admin.system-check.mysql', [
                'output' => $output,
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return view('super-admin.system-check.mysql', [
                'output' => "Error: " . $e->getMessage(),
                'status' => 'error'
            ]);
        }
    }
    
    /**
     * Check MySQL status in background using Ajax
     */
    public function ajaxCheckMySQLStatus()
    {
        try {
            // Run the command
            Artisan::call('system:check-mysql');
            
            // Get the output
            $output = Artisan::output();
            
            // Determine status based on output content
            if (strpos($output, 'WARNING:') !== false || strpos($output, 'CAUTION:') !== false) {
                return response()->json([
                    'status' => 'warning',
                    'output' => $output,
                    'message' => 'MySQL connection status shows potential issues.'
                ]);
            } else {
                return response()->json([
                    'status' => 'success',
                    'output' => $output,
                    'message' => 'MySQL connection status is good.'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'output' => "Error: " . $e->getMessage(),
                'message' => 'Failed to check MySQL status.'
            ]);
        }
    }
} 