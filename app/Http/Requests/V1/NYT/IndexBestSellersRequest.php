<?php

namespace App\Http\Requests\V1\NYT;

use Illuminate\Foundation\Http\FormRequest;

class IndexBestSellersRequest extends FormRequest
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
     * Note: The ibn regex is just making sure each passed value ia a valid 10 or 13 digit ISBN
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'api-key' => 'required|string|min:3',
            'author' => ['sometimes', 'string', 'regex:/^[a-zA-Zà-ÿÀ-Ÿ\'’\- ]{1,100}$/'],
            'isbn' => ['sometimes', 'string', 'regex:/^(?:\d{9}[\dXx]|\d{13})$/'],
            'title' => 'sometimes|string',
            'offset' => ['sometimes', 'int', function ($attribute, $value, $fail) {
                if (!is_numeric($value) || $value % 20 !== 0) {
                    $fail("The $attribute must be a multiple of 20.");
                }
            }]
        ];
    }

    public function messages()
    {
        return [
            'isbn.regex' => 'The ISBN must be a valid ISBN-10 or ISBN-13 format.',
            'api-key.required' => 'API key is required. Please register for one and place it in your .env file under `NYT_API_KEY=[your key]` and then restart the server.',
        ];
    }
}
