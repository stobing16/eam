<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class EmployeeStoreRequest extends FormRequest
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
            'nik' => 'required|string|max:15',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable',
            'join_date' => 'nullable',
            'npwp' => 'nullable',
            'ptkp' => 'nullable',
            'no_rek' => 'nullable',
            'nama_rek' => 'nullable',
            'join_date' => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Form :attribute ini wajib diisi',
            'email' => 'Format form :attribute harus berupa email',
            'max' => 'Maksimal karakter form :attribute tidak boleh lebih dari 255 karakter'
        ];
    }

    // public function failedValidation(Validator $validator)
    // {
    //     throw new \Illuminate\Validation\ValidationException($validator, response()->json([
    //         'status' => 'error',
    //         'message' => 'Validation failed.',
    //         'errors' => $validator->errors()->all()
    //     ]));
    // }
}
