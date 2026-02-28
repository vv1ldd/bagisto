<?php

namespace Webkul\Shop\Http\Controllers\Customer;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Repositories\SubscribersListRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Product\Repositories\ProductReviewRepository;
use Webkul\Sales\Models\Order;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Shop\Http\Requests\Customer\ProfileRequest;

class CustomerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected ProductReviewRepository $productReviewRepository,
        protected SubscribersListRepository $subscriptionRepository
    ) {
    }

    /**
     * For loading the edit form page.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);

        return view('shop::customers.account.profile.edit', compact('customer'));
    }

    /**
     * Show recovery key page.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function recoveryKey()
    {
        if (!session()->has('recovery_key')) {
            return redirect()->route('shop.customers.account.profile.complete_registration');
        }

        return view('shop::customers.account.profile.recovery-key');
    }

    /**
     * Show the profile completion page after registration.
     *
     * @return \Illuminate\View\View
     */
    public function completeRegistration()
    {
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);

        return view('shop::customers.account.profile.edit', [
            'customer' => $customer,
            'isCompleteRegistration' => true,
        ]);
    }

    /**
     * Show the passkey completion page after registration.
     *
     * @return \Illuminate\View\View
     */
    public function completeRegistrationPasskey()
    {
        $customer = $this->customerRepository->find(auth()->guard('customer')->user()->id);

        return view('shop::customers.account.passkeys.index', [
            'customer' => $customer,
            'isCompleteRegistration' => true,
        ]);
    }

    /**
     * Handle the final success redirect after registration flow.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function completeRegistrationSuccess()
    {
        session()->flash('success', 'Аккаунт успешно создан и вход настроен!');

        return redirect()->route('shop.home.index');
    }

    /**
     * Edit function for editing customer profile.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $profileRequest)
    {
        $isPasswordChanged = false;

        $data = $profileRequest->validated();

        if (!empty($data['gender'])) {
            // No hashing for displayable PII
        } else {
            unset($data['gender']);
        }

        if (!empty($data['date_of_birth'])) {
            try {
                // Normalize from DD.MM.YYYY to YYYY-MM-DD
                $data['date_of_birth'] = Carbon::parse($data['date_of_birth'])->format('Y-m-d');
            } catch (\Exception $e) {
                // If parsing fails (e.g. already normalized), keep it as is
            }
        } else {
            unset($data['date_of_birth']);
        }

        if (!empty($data['birth_city'])) {
            $data['birth_city'] = trim($data['birth_city']);
        } else {
            unset($data['birth_city']);
        }

        if (!empty($data['country_of_residence'])) {
            $data['country_of_residence'] = trim($data['country_of_residence']);
        } else {
            unset($data['country_of_residence']);
        }

        if (!empty($data['citizenship'])) {
            $data['citizenship'] = trim($data['citizenship']);
        } else {
            unset($data['citizenship']);
        }

        $data['subscribed_to_news_letter'] = isset($data['subscribed_to_news_letter']);



        Event::dispatch('customer.update.before');

        if ($customer = $this->customerRepository->update($data, auth()->guard('customer')->user()->id)) {
            if ($isPasswordChanged) {
                Event::dispatch('customer.password.update.after', $customer);
            }

            Event::dispatch('customer.update.after', $customer);

            session()->forget('onboarding_no_password');

            if ($data['subscribed_to_news_letter']) {
                $subscription = $this->subscriptionRepository->findOneWhere(['email' => $data['email']]);

                if ($subscription) {
                    $this->subscriptionRepository->update([
                        'customer_id' => $customer->id,
                        'is_subscribed' => 1,
                    ], $subscription->id);
                } else {
                    $this->subscriptionRepository->create([
                        'email' => $data['email'],
                        'customer_id' => $customer->id,
                        'channel_id' => core()->getCurrentChannel()?->id ?: core()->getDefaultChannel()->id,
                        'is_subscribed' => 1,
                        'token' => $token = uniqid(),
                    ]);
                }
            } else {
                $subscription = $this->subscriptionRepository->findOneWhere(['email' => $data['email']]);

                if ($subscription) {
                    $this->subscriptionRepository->update([
                        'customer_id' => $customer->id,
                        'is_subscribed' => 0,
                    ], $subscription->id);
                }
            }

            if (request()->hasFile('image')) {
                $this->customerRepository->uploadImages($data, $customer);
            } else {
                if (isset($data['image'])) {
                    if (!empty($data['image'])) {
                        Storage::delete((string) $customer->image);
                    }

                    $customer->image = null;

                    $customer->save();
                }
            }

            session()->flash('success', trans('shop::app.customers.account.profile.index.edit-success'));

            if (isset($data['is_complete_registration']) && $data['is_complete_registration']) {
                return redirect()->route('shop.customers.account.profile.complete_registration_passkey');
            }

            if ($customer->passkeys()->count() === 0) {
                return redirect()->route('shop.customers.account.passkeys.index');
            }

            return redirect()->route('shop.customers.account.index');
        }

        session()->flash('success', trans('shop::app.customer.account.profile.edit-fail'));

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function destroy()
    {
        $customerRepository = $this->customerRepository->findOrFail(auth()->guard('customer')->user()->id);

        try {
            // Check for pending orders
            if ($customerRepository->orders->whereIn('status', [Order::STATUS_PENDING, Order::STATUS_PROCESSING])->first()) {
                session()->flash('error', trans('shop::app.customers.account.profile.index.order-pending'));

                return redirect()->route('shop.customers.account.profile.edit');
            }

            // Perform deletion
            $this->customerRepository->delete(auth()->guard('customer')->user()->id);

            auth()->guard('customer')->logout();

            session()->flash('success', trans('shop::app.customers.account.profile.index.delete-success'));

            return redirect()->route('shop.customer.session.index');
        } catch (\Exception $e) {
            session()->flash('error', trans('shop::app.customers.account.profile.index.delete-failed'));

            return redirect()->route('shop.customers.account.profile.edit');
        }
    }

    /**
     * Load the view for the customer account panel, showing approved reviews.
     *
     * @return \Illuminate\View\View
     */
    public function reviews()
    {
        $reviews = $this->productReviewRepository->getCustomerReview();

        return view('shop::customers.account.reviews.index', compact('reviews'));
    }

    /**
     * Taking the customer to account details page.
     *
     * @return \Illuminate\View\View
     */
    public function account()
    {
        return view('shop::customers.account.index');
    }
}
