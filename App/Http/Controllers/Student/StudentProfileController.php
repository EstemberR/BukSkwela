<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Student\Student;

class StudentProfileController extends Controller
{
    /**
     * Update the student's personal information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updatePersonalInfo(Request $request)
    {
        try {
            // Get the authenticated student
            $student = Auth::guard('student')->user();
            
            if (!$student) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Student not found'
                ], 404);
            }
            
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'last_name' => 'required|string|max:255',
                'suffix_name' => 'nullable|string|max:50',
                'sex' => 'required|in:MALE,FEMALE',
                'birth_date' => 'required|date',
                'civil_status' => 'required|string|max:50',
                'religion' => 'nullable|string|max:100',
                'blood_type' => 'nullable|string|max:10',
                'contact_number' => 'nullable|string|max:20',
                'email' => 'required|email|max:255',
                'facebook_username' => 'nullable|string|max:255',
                'has_indigenous_group' => 'required|boolean',
                'indigenous_group' => 'nullable|string|max:100',
                'other_indigenous_group' => 'nullable|string|max:100',
                'dswd_number' => 'nullable|string|max:100',
                'disability' => 'nullable|string|max:100',
            ]);
            
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update the student model directly with all fields
            $student->first_name = $request->first_name;
            $student->middle_name = $request->middle_name;
            $student->last_name = $request->last_name;
            $student->suffix_name = $request->suffix_name;
            $student->sex = $request->sex;
            $student->birth_date = $request->birth_date;
            $student->civil_status = $request->civil_status;
            $student->religion = $request->religion;
            $student->blood_type = $request->blood_type;
            $student->email = $request->email;
            $student->facebook_username = $request->facebook_username;
            $student->has_indigenous_group = $request->has_indigenous_group;
            $student->indigenous_group = $request->indigenous_group;
            $student->other_indigenous_group = $request->other_indigenous_group;
            $student->dswd_number = $request->dswd_number;
            $student->disability = $request->disability;
            
            // Update the name field which is a combination of first, middle, last names
            $student->name = trim($request->first_name . ' ' . $request->middle_name . ' ' . $request->last_name);
            
            // Save phone if present
            if ($request->has('contact_number')) {
                $student->contact_number = $request->contact_number;
            }
            
            $student->save();
            
            // Log that an update was made
            Log::info('Student profile updated', ['student_id' => $student->id]);
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Personal information updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating student profile: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your personal information'
            ], 500);
        }
    }
    
    /**
     * Update the student's academic information
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateAcademicInfo(Request $request)
    {
        try {
            // Log the request data for debugging
            Log::info('Academic info update request received', [
                'request_data' => $request->all()
            ]);
            
            // Get the authenticated student
            $student = Auth::guard('student')->user();
            
            if (!$student) {
                Log::warning('Student not found during academic info update');
                return response()->json([
                    'success' => false, 
                    'message' => 'Student not found'
                ], 404);
            }
            
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'educational_status' => 'required|string|max:100',
                'lrn' => 'nullable|string|max:50',
                'school_name' => 'required|string|max:255',
                'year_from' => 'required|string|max:10',
                'year_to' => 'required|string|max:10',
                'education_level' => 'required|string|max:100',
                'school_type' => 'required|string|max:50',
                'strand' => 'nullable|string|max:100',
                'is_philippines' => 'required|boolean',
                'region' => 'required|string|max:100',
                'province' => 'required|string|max:100',
                'city' => 'required|string|max:100',
                'barangay' => 'required|string|max:100',
                'street' => 'nullable|string|max:255',
            ]);
            
            if ($validator->fails()) {
                Log::warning('Academic info validation failed', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Update academic information fields directly
            $student->educational_status = $request->educational_status;
            $student->lrn = $request->lrn;
            $student->school_name = $request->school_name;
            $student->year_from = $request->year_from;
            $student->year_to = $request->year_to;
            $student->education_level = $request->education_level;
            $student->school_type = $request->school_type;
            $student->strand = $request->strand;
            $student->is_philippines = $request->is_philippines;
            $student->region = $request->region;
            $student->province = $request->province;
            $student->city = $request->city;
            $student->barangay = $request->barangay;
            $student->street = $request->street;
            
            // Set derived fields
            $student->year_level = $request->education_level;
            $student->school_year = $request->year_from . '-' . $request->year_to;
            
            $student->save();
            
            // Log that an update was made
            Log::info('Student academic info updated successfully', [
                'student_id' => $student->id,
                'educational_status' => $student->educational_status,
                'school_name' => $student->school_name
            ]);
            
            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'Academic information updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating student academic info: ' . $e->getMessage(), [
                'student_id' => Auth::guard('student')->id(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating your academic information: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get the current student data for the dashboard
     * 
     * @return \Illuminate\Http\Response
     */
    public function getStudentData()
    {
        try {
            // Get the authenticated student
            $student = Auth::guard('student')->user();
            
            if (!$student) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Student not found'
                ], 404);
            }
            
            // Return the student data
            return response()->json([
                'success' => true,
                'student' => $student
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error getting student data: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving your data'
            ], 500);
        }
    }
} 