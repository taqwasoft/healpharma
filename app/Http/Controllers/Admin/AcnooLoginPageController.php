<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HasUploader;
use App\Models\Option;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class AcnooLoginPageController extends Controller
{
    use HasUploader;
    public function index()
    {
        $login_page = Option::where('key','login-page')->first();
        return view('admin.settings.login',compact('login_page'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'login_page_icon' => 'nullable|image'
        ]);

        $general = Option::findOrFail($id);
        Cache::forget($general->key);
        $general->update([
            'value' => $request->except('_token','_method','login_page_icon') + [
                'login_page_icon' => $request->login_page_icon ? $this->upload($request, 'login_page_icon', $general->login_page_icon) : $general->value['login_page_icon'],
            ]
        ]);

        return response()->json([
            'message'   => __('Login Page updated successfully'),
            'redirect'  => route('admin.login-pages.index')
        ]);
    }
}
