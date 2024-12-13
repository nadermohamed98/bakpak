<?php

namespace App\Imports;

use App\Models\User;
use App\Traits\ImageTrait;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Validator;

class StudentsImport implements ToModel, WithHeadingRow
{
    use ImageTrait;

    public $importedStudents = [];
    public $skippedStudents = [];
    
    public function model(array $row)
    {
        $filteredRow = array_filter($row, function ($key) {
            return in_array($key, [
                'first_name', 'last_name', 'phone', 'phone_country_id', 'email',
                'password', 'address', 'postal_code', 'country_id', 'state_id',
                'city_id', 'gender', 'about'
            ]);
        }, ARRAY_FILTER_USE_KEY);
    
        $filteredRow = array_map(function($value) {
            return is_string($value) ? trim($value) : $value;
        }, $filteredRow);
    
        // required fields
        $requiredFields = ['first_name', 'last_name', 'email', 'phone', 'password'];
        foreach ($requiredFields as $field) {
            if (empty($filteredRow[$field])) {
                $this->skippedStudents[] = [
                    'row' => $row,
                    'reason' => "Missing required field: $field"
                ];
                return null; // Skip this row
            }
        }
    
        $email = strtolower($filteredRow['email']);
        $phone = preg_replace('/\D/', '', $filteredRow['phone']); 
        
        // Check if email or phone already exists
        $emailExists = User::where('email', $email)->exists();
        $phoneExists = User::where('phone', $phone)->exists();
    
        if ($emailExists || $phoneExists) {
            $reason = [];
            if ($emailExists) {
                $reason[] = "Duplicate email";
            }
            if ($phoneExists) {
                $reason[] = "Duplicate phone";
            }
            $this->skippedStudents[] = [
                'row' => $row,
                'reason' => implode(' and ', $reason)
            ];
            return null; // Skip this row
        }
    
        // Validate the data
        $validator = Validator::make($filteredRow, [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|min:6'
        ]);
    
        if ($validator->fails()) {
            // Collect validation errors
            $this->skippedStudents[] = [
                'row' => $row,
                'reason' => $validator->errors()->all()
            ];
            return null; // Skip this row
        }
    
        // Create the User instance
        $student = new User([
            'first_name'       => $filteredRow['first_name'],
            'last_name'        => $filteredRow['last_name'],
            'phone'            => $phone,
            'phone_country_id' => $filteredRow['phone_country_id'] ?? null,
            'email'            => $email,
            'password'         => bcrypt($filteredRow['password']),
            'address'          => $filteredRow['address'] ?? null,
            'postal_code'      => $filteredRow['postal_code'] ?? null,
            'country_id'       => $filteredRow['country_id'] ?? null,
            'state_id'         => $filteredRow['state_id'] ?? null,
            'city_id'          => $filteredRow['city_id'] ?? null,
            'gender'           => $filteredRow['gender'] ?? null,
            'about'            => $filteredRow['about'] ?? null,
            'role_id'          => 3,
        ]);
    
        $this->importedStudents[] = $student;
        return $student;
    }
  
  
    public function rules(): array
    {
        return [
            '*.first_name' => 'required|string|max:255',
            '*.last_name'  => 'required|string|max:255',
            '*.email'      => 'required|email|unique:users,email',
            '*.password'   => 'required|min:6',
            '*.phone'      => 'required|numeric|unique:users,phone',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'first_name.required' => 'First name is required',
            'last_name.required'  => 'Last name is required',
            'email.required'      => 'Email is required',
            'email.email'         => 'Email must be a valid email address',
            'email.unique'        => 'Email already exists',
            'password.required'   => 'Password is required',
            'password.min'        => 'Password must be at least 6 characters',
            'phone.required'      => 'Phone number is required',
            'phone.numeric'       => 'Phone number must be a number',
            'phone.unique'        => 'Phone number already exists',
        ];
    }

}
