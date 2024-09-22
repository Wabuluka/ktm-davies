<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id' => '',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'password' => ['required', Password::defaults()],
        ];
    }

    /**
     * バリデータインスタンスの設定
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // 編集画面時のみ、idを必須にする（type:hidden）
            $validator->sometimes('id', 'required', function ($input) {
                return ! isset($input->password_confirmation);
            });
            // confirmedが存在する場合、新規管理ユーザー登録画面なので、バリデーションを変える。
            $validator->sometimes('email', 'required|string|email|max:255|unique:users', function ($input) {
                return isset($input->password_confirmation);
            });
            $validator->sometimes('password', ['required', 'confirmed', Rules\Password::defaults()], function ($input) {
                return isset($input->password_confirmation);
            });
        });
    }
}
