<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\ZaloOa;
use App\Models\ZnsMessage;
use Aws\Token\Token;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function default()
    {
        return view('web_default');
    }
    //
    public function index()
    {
        $title = 'Dashboard';
        // $toleprice = Transaction::sum('amount');

        $toleprice = ZnsMessage::where('user_id', Auth::user()->id)->where('status', 1)
            ->with('template')
            ->get()
            ->sum(function ($message) {
                return $message->template ? $message->template->price : 0;
            });
        $success = ZnsMessage::where('user_id', Auth::user()->id)->where('status', 1)->count();
        $fail = ZnsMessage::where('user_id', Auth::user()->id)->where('status', 0)->count();
        $oa = ZaloOa::where('user_id', Auth::user()->id)->count();
        return view("admin.dashboard.index", compact('title', 'toleprice', 'success', 'fail', 'oa'));
    }
}
