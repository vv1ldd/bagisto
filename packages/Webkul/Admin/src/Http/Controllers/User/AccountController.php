<?php

namespace Webkul\Admin\Http\Controllers\User;

use Hash;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Webkul\Admin\Http\Controllers\Controller;

class AccountController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        $user = auth()->guard('admin')->user();

        return view('admin::account.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $user = auth()->guard('admin')->user();

        $this->validate(request(), [
            'name' => 'required',
            'email' => 'email|unique:admins,email,'.$user->id,
            'password' => 'nullable|min:6|confirmed',
            'image.*' => 'nullable|mimes:bmp,jpeg,jpg,png,webp',
        ]);

        $data = request()->only([
            'name',
            'email',
            'password',
            'password_confirmation',
            'current_password',
            'image',
        ]);

        $isPasskeyVerified = false;
        $passkeyResponse = request()->input('passkey_verification_response');

        if ($passkeyResponse) {
            try {
                $optionsJson = session()->get('admin-passkey-authentication-options-json');
                
                if ($optionsJson) {
                    $findPasskeyAction = app(\Spatie\LaravelPasskeys\Actions\FindPasskeyToAuthenticateAction::class);
                    $passkey = $findPasskeyAction->execute($passkeyResponse, $optionsJson);

                    if ($passkey && $passkey->authenticatable_id == $user->id) {
                        $isPasskeyVerified = true;
                        
                        // Update session trust
                        session()->put('passkey_unlocked_at', now()->timestamp);
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Admin Profile Update Passkey Verification Failed: ' . $e->getMessage());
            }
        }

        if (! $isPasskeyVerified && ! Hash::check($data['current_password'] ?? '', $user->password)) {
            session()->flash('warning', trans('admin::app.account.edit.invalid-password'));

            return redirect()->back();
        }

        $isPasswordChanged = false;

        if (! isset($data['password']) || ! $data['password']) {
            unset($data['password']);
        } else {
            $isPasswordChanged = true;

            $data['password'] = bcrypt($data['password']);
        }

        if (request()->hasFile('image')) {
            $data['image'] = current(request()->file('image'))->store('admins/'.$user->id);
        } else {
            if (! isset($data['image'])) {
                if (! empty($user->image)) {
                    Storage::delete($user->image);
                }

                $data['image'] = null;
            } else {
                $data['image'] = $user->image;
            }
        }

        $user->update($data);

        if ($isPasswordChanged) {
            Event::dispatch('admin.password.update.after', $user);
        }

        session()->flash('success', trans('admin::app.account.edit.update-success'));

        return back();
    }
}
