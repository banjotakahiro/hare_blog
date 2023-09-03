<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    // バリデーションを適用する人としない人で分ける。今回は全員に適用させるのでtrue
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'body' => 'required|string|max:200',
        ];
    }

    public function attributes()
    {
        return [
            'body'=>'コメント',
        ];
    }
}
