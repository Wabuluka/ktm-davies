<?php

namespace App\Http\Requests;

use App\Models\Folder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FolderCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $rawParentId = $this->input('parent_id');
        $parentId = null;

        if ($rawParentId === 'root') {
            $parentId = Folder::where('parent_id', null)->firstOrFail()->id;
        } else {
            $segments = explode('_', $rawParentId);
            $parentId = end($segments);
        }

        if ($parentId) {
            $this->merge([
                'parent_id' => $parentId,
            ]);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => [
                'required',
                'max:255',
                Rule::unique(Folder::class, 'name')
                    ->where(fn ($query) => $query->where('parent_id', $this->parent_id)),
            ],
            'parent_id' => 'required',
        ];
    }
}
