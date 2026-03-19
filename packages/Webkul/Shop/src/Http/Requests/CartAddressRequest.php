<?php

namespace Webkul\Shop\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Core\Rules\PhoneNumber;
use Webkul\Core\Rules\PostCode;
use Webkul\Customer\Rules\VatIdRule;

class CartAddressRequest extends FormRequest
{
    /**
     * Rules.
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Determine if the product is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        if ($this->has('billing')) {
            $this->mergeAddressRules('billing');
        }

        if (!$this->input('billing.use_for_shipping')) {
            $this->mergeAddressRules('shipping');
        }

        return $this->rules;
    }

    /**
     * Merge new address rules.
     */
    private function mergeAddressRules(string $addressType): void
    {
        $haveStockableItems = \Webkul\Checkout\Facades\Cart::getCart()?->haveStockableItems() ?? true;

        $this->mergeWithRules([
            "{$addressType}.company_name" => ['nullable'],
            "{$addressType}.first_name" => ['required'],
            "{$addressType}.last_name" => ['required'],
            "{$addressType}.email" => ['required'],
            "{$addressType}.address" => $haveStockableItems ? ['required', 'array', 'min:1'] : ['nullable', 'array'],
            "{$addressType}.city" => $haveStockableItems ? ['required'] : ['nullable'],
            "{$addressType}.country" => ($haveStockableItems && core()->isCountryRequired()) ? ['required'] : ['nullable'],
            "{$addressType}.state" => ($haveStockableItems && core()->isStateRequired()) ? ['required'] : ['nullable'],
            "{$addressType}.postcode" => ($haveStockableItems && core()->isPostCodeRequired()) ? ['required', new PostCode] : [new PostCode],
            "{$addressType}.phone" => $haveStockableItems ? ['required', new PhoneNumber] : ['nullable', new PhoneNumber],
        ]);

        if ($addressType == 'billing') {
            $this->mergeWithRules([
                "{$addressType}.vat_id" => [(new VatIdRule)->setCountry($this->input('billing.country'))],
                "{$addressType}.is_gift" => ['nullable', 'boolean'],
                "{$addressType}.gift_email" => ['required_if:billing.is_gift,true', 'nullable', 'email'],
            ]);
        }
    }

    /**
     * Merge additional rules.
     */
    private function mergeWithRules($rules): void
    {
        $this->rules = array_merge($this->rules, $rules);
    }
}
