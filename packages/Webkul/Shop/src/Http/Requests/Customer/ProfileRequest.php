<?php

namespace Webkul\Shop\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Core\Rules\PhoneNumber;

class ProfileRequest extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'first_name'  => ['nullable', 'string', 'max:255'],
            'last_name'   => ['nullable', 'string', 'max:255'],
            'username'    => ['required', 'string', 'min:3', 'max:30', 'regex:/^[a-zA-Z0-9_\-\.]+$/', 'unique:customers,username,' . auth()->guard('customer')->user()->id],
            'gender'      => ['nullable', 'in:Male,Female,Other'],
            'date_of_birth'=> ['nullable', 'date', 'before:today'],
            'birth_city'  => ['nullable', 'string', 'max:255'],
            'email'       => ['nullable', 'email', 'unique:customers,email,' . auth()->guard('customer')->user()->id],
            'phone'       => ['nullable', new PhoneNumber, 'unique:customers,phone,' . auth()->guard('customer')->user()->id],
            'image'       => ['array', 'nullable'],
            'image.*'     => ['nullable', 'mimes:bmp,jpg,jpeg,png,svg,webp'],
            'oldpassword' => ['nullable', 'required_with:password'],
            'password'    => ['nullable', 'min:6', 'confirmed'],
            'subscribed_to_news_letter' => 'nullable',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'username.regex' => 'Псевдоним может содержать только латиницу, цифры, минус, подчеркивание и точку',
            'username.min' => 'Псевдоним должен содержать от 3 до 30 символов',
            'username.max' => 'Псевдоним должен содержать от 3 до 30 символов',
        ];
    }

    /**
     * Get the custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'first_name' => trans('shop::app.customers.signup.first-name'),
            'last_name' => trans('shop::app.customers.signup.last-name'),
            'username' => 'Алиас',
            'gender' => trans('shop::app.customers.account.profile.edit.gender'),
            'date_of_birth' => trans('shop::app.customers.account.profile.edit.dob'),
            'birth_city' => trans('shop::app.customers.account.profile.edit.birth-city'),
            'email' => trans('shop::app.customers.signup.email'),
            'phone' => trans('shop::app.customers.signup.phone'),
        ];
    }
}
