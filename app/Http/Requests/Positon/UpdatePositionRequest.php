<?php

namespace App\Http\Requests\Positon;

use App\Acl\Acl;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePositionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return checkPermission(Acl::PERMISSION_POSITION_EDIT_HR);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'max:100',
                'unique:positions,name,' . $this->position->id,
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Trường :attribute không được để trống.',
            'max' => 'Trường :attribute tối đa :max kí tự.',
            'unique' => 'Trường :attribute đã tồn tại.',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'tên',
        ];
    }
}