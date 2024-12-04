<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class EmployeeStoreBatchRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            '*.name' => 'required|string|max:255',
            '*.email' => 'required|email|max:255',
            '*.jabatan' => 'nullable',
            '*.nik' => 'nullable',
            '*.status' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Form :attribute wajib diisi',
            'email' => 'Format form :attribute harus berupa email',
            'max' => 'Maksimal karakter form :attribute tidak boleh lebih dari 255 karakter'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $formattedErrors = [];

        foreach ($errors->messages() as $key => $messages) {
            $formattedKey = preg_replace('/^(\d+)\.(.*)$/', '$2[$1]', $key);
            $formattedMessages = array_map(function ($message) use ($formattedKey) {
                return preg_replace('/\d+\./', '', $message);
            }, $messages);

            $formattedErrors[$formattedKey] = $formattedMessages;
        }

        // Mengembalikan response dengan error yang sudah diformat
        throw new \Illuminate\Validation\ValidationException($validator, response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $formattedErrors
        ], 422));
    }
}
