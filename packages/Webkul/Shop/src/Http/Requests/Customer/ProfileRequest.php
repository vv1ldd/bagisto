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
        $customer = auth()->guard('customer')->user();
        $id = $customer->id;

        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'username' => ['required', 'unique:customers,username,' . $id, 'max:255', 'regex:/^[a-zA-Z0-9_.]+$/'],
            'gender' => $customer->gender ? 'nullable' : 'required|in:Other,Male,Female',
            'date_of_birth' => $customer->date_of_birth ? 'nullable' : 'required|string',
            'birth_city' => $customer->birth_city ? 'nullable' : 'required|string',
            'email' => 'email|unique:customers,email,' . $id,

            'image' => 'array',
            'image.*' => 'mimes:bmp,jpeg,jpg,png,webp',
            'phone' => ['nullable', new PhoneNumber, 'unique:customers,phone,' . $id],
            'subscribed_to_news_letter' => 'nullable',
            'is_complete_registration' => 'boolean|nullable',
            'country_of_residence' => 'nullable|string',
            'citizenship' => 'nullable|string',
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
            'username' => trans('shop::app.customers.account.profile.username'),
            'gender' => trans('shop::app.customers.account.profile.edit.gender'),
            'date_of_birth' => trans('shop::app.customers.account.profile.edit.dob'),
            'birth_city' => trans('shop::app.customers.account.profile.edit.birth-city'),
            'email' => trans('shop::app.customers.signup.email'),
            'phone' => trans('shop::app.customers.signup.phone'),
            'country_of_residence' => 'Страна резиденции',
            'citizenship' => 'Гражданство',
        ];
    }
}
