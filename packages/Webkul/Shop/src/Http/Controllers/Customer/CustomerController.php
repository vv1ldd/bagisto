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
     * Show the recovery key (seed phrase) to the user.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function recoveryKey()
    {
        $recoveryKey = session('pending_recovery_key');

        if (! $recoveryKey) {
            return redirect()->route('shop.customers.account.index');
        }

        return view('shop::customers.account.profile.recovery-key', [
            'words' => explode(' ', $recoveryKey)
        ]);
    }

    /**
     * Show the screen to verify the recovery key.
     * Generates 3 random word indices to quiz the user.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function showVerifyRecoveryKey()
    {
        $recoveryKey = session('pending_recovery_key');

        if (! $recoveryKey) {
            return redirect()->route('shop.customers.account.index');
        }

        $words = explode(' ', $recoveryKey);
        
        $indices = session('verification_indices');
        if (! $indices) {
            // Generate 3 unique random zero-based indices
            $keys = array_rand($words, 3);
            sort($keys);
            $indices = $keys;
            session(['verification_indices' => $indices]);
        }

        return view('shop::customers.account.profile.verify-recovery-key', compact('indices'));
    }

    /**
     * Verify the entered recovery key words.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyRecoveryKey(\Illuminate\Http\Request $request)
    {
        $recoveryKey = session('pending_recovery_key');
        $indices = session('verification_indices');

        if (! $recoveryKey || ! $indices) {
            return redirect()->route('shop.customers.account.index');
        }

        $words = explode(' ', $recoveryKey);
        
        // Validate inputs
        $rules = [];
        $messages = [];
        foreach ($indices as $i) {
            $fieldName = 'word_' . $i;
            $pos = $i + 1;
            $rules[$fieldName] = 'required|string';
            $messages["{$fieldName}.required"] = "Введите слово №{$pos}";
        }
        $request->validate($rules, $messages);

        // Check if words match
        $allCorrect = true;
        foreach ($indices as $i) {
            $expected = strtolower(trim($words[$i]));
            $actual = strtolower(trim($request->input('word_' . $i)));
            
            if ($expected !== $actual) {
                $allCorrect = false;
                break;
            }
        }

        if (! $allCorrect) {
            return back()->withErrors(['message' => 'Введены неверные слова. Пожалуйста, проверьте свою секретную фразу и попробуйте снова.']);
        }

        // Success: clear session, complete registration
        session()->forget(['pending_recovery_key', 'verification_indices']);
        
        $customer = auth()->guard('customer')->user();
        if ($customer && $customer->is_complete_registration === null) {
            $this->customerRepository->update([
                'is_complete_registration' => 1
            ], $customer->id);
        }

        session()->flash('success', 'Регистрация полностью завершена!');
        return redirect()->route('shop.customers.account.profile.complete_registration_success');
    }

    /**
     * Show the profile completion page after registration.
     *
     * @return \Illuminate\View\View
     */


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
     * Check if username is available.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUsername()
    {
        $username = request()->get('username');

        if (!$username) {
            return response()->json(['available' => false]);
        }

        // Allow current user to keep their own username
        $customer = auth()->guard('customer')->user();

        $exists = $this->customerRepository->scopeQuery(function ($query) use ($username, $customer) {
            return $query->where('username', $username)
                ->where('id', '!=', $customer->id);
        })->first();

        return response()->json([
            'available' => !$exists,
            'message' => $exists ? 'Этот псевдоним уже занят' : ''
        ]);
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

        $data['subscribed_to_news_letter'] = isset($data['subscribed_to_news_letter']);



        Event::dispatch('customer.update.before');

        if ($customer = $this->customerRepository->update($data, auth()->guard('customer')->user()->id)) {
            if ($isPasswordChanged) {
                Event::dispatch('customer.password.update.after', $customer);
            }

            Event::dispatch('customer.update.after', $customer);

            session()->forget('onboarding_no_password');

            $email = $data['email'] ?? $customer->email;

            if ($email) {
                if ($data['subscribed_to_news_letter']) {
                    $subscription = $this->subscriptionRepository->findOneWhere(['email' => $email]);

                    if ($subscription) {
                        $this->subscriptionRepository->update([
                            'customer_id' => $customer->id,
                            'is_subscribed' => 1,
                        ], $subscription->id);
                    } else {
                        $this->subscriptionRepository->create([
                            'email' => $email,
                            'customer_id' => $customer->id,
                            'channel_id' => core()->getCurrentChannel()?->id ?: core()->getDefaultChannel()->id,
                            'is_subscribed' => 1,
                            'token' => $token = uniqid(),
                        ]);
                    }
                } else {
                    $subscription = $this->subscriptionRepository->findOneWhere(['email' => $email]);

                    if ($subscription) {
                        $this->subscriptionRepository->update([
                            'customer_id' => $customer->id,
                            'is_subscribed' => 0,
                        ], $subscription->id);
                    }
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
                session()->flash('success', 'Регистрация полностью завершена!');
                return redirect()->route('shop.customers.account.profile.complete_registration_success');
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
