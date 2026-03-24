<?php

namespace Webkul\Admin\Http\Controllers\User;

use Webkul\Admin\Http\Controllers\Controller;

class SessionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        if (auth()->guard('admin')->check()) {
            return redirect()->route('admin.dashboard.index');
        }

        if (strpos(url()->previous(), 'admin') !== false) {
            $intendedUrl = url()->previous();
        } else {
            $intendedUrl = route('admin.dashboard.index');
        }

        session()->put('url.intended', $intendedUrl);

        return view('admin::users.sessions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->validate(request(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = request('remember');

        if (! auth()->guard('admin')->attempt(request(['email', 'password']), $remember)) {
            session()->flash('error', trans('admin::app.settings.users.login-error'));

            return redirect()->back();
        }

        $user = auth()->guard('admin')->user();

        if (! $user->status) {
            session()->flash('warning', trans('admin::app.settings.users.activate-warning'));

            auth()->guard('admin')->logout();

            return redirect()->route('admin.session.create');
        }

        // Onboarding check: Redirect to security setup if no Passkey is configured
        if (! $user->passkeys->count()) {
            return redirect()->route('admin.security.onboarding.index');
        }

        if (! bouncer()->hasPermission('dashboard')) {
            $allPermissions = collect(config('acl'));

            $permissions = auth()->guard('admin')->user()->role->permissions;

            foreach ($permissions as $permission) {
                if (bouncer()->hasPermission($permission)) {
                    $permissionDetails = $allPermissions->firstWhere('key', $permission);

                    // If key is single level (no dots), find the first child entry
                    if (! str_contains($permission, '.')) {
                        $childPermission = $allPermissions->first(function ($item) use ($permission) {
                            return str_starts_with($item['key'], $permission.'.')
                                && substr_count($item['key'], '.') === 1
                                && bouncer()->hasPermission($item['key']);
                        });

                        if ($childPermission) {
                            return redirect()->route($childPermission['route']);
                        }
                    }

                    return redirect()->route($permissionDetails['route']);
                }
            }
        }

        return redirect()->intended(route('admin.dashboard.index'));
    }

    /**
     * Show recovery form.
     */
    public function showRecovery()
    {
        return view('admin::users.sessions.recovery');
    }

    /**
     * Recover access using mnemonic.
     */
    public function recover()
    {
        $this->validate(request(), [
            'mnemonic' => 'required',
        ]);

        $mnemonic = trim(request('mnemonic'));
        $admins = \Webkul\User\Models\Admin::all();
        $user = null;

        foreach ($admins as $admin) {
            if ($admin->mnemonic_hash && \Illuminate\Support\Facades\Hash::check($mnemonic, $admin->mnemonic_hash)) {
                $user = $admin;
                break;
            }
        }

        if (!$user) {
            session()->flash('error', 'Неверная сид-фраза.');
            return redirect()->back();
        }

        auth()->guard('admin')->login($user);

        return redirect()->route('admin.dashboard.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        auth()->guard('admin')->logout();

        return redirect()->route('admin.session.create');
    }
}
