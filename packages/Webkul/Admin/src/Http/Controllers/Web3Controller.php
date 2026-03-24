<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Routing\Controller;

class Web3Controller extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin::web3.index');
    }
}
