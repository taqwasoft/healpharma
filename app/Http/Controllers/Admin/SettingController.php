<?php

namespace App\Http\Controllers\Admin;

use App\Models\Option;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Helpers\HasUploader;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    use HasUploader;

    public function __construct()
    {
        $this->middleware('permission:settings-read')->only('index');
        $this->middleware('permission:settings-update')->only('update');
    }

    public function index()
    {
        $general = Option::where('key', 'general')->first();
        return view('admin.settings.general', compact('general'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'copy_right' => 'required|string|max:100',
            'admin_footer_text' => 'required|string|max:100',
            'admin_footer_link_text' => 'required|string|max:100',
            'admin_footer_link' => 'required|string|max:100',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'common_header_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'footer_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'admin_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'frontend_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'invoice_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'app_link' => 'nullable|url',
        ]);

        $general = Option::findOrFail($id);
        $path = 'uploads/qr-codes/qrcode.svg';
        if (!file_exists($path)) {
            $qr = QrCode::size(300)->format('svg')->generate($general->value['admin_footer_link'] ?? '');
            Storage::put($path, $qr);
        }
        Cache::forget($general->key);
        $general->update([
            'value' => $request->except('_token', '_method', 'logo', 'favicon', 'common_header_logo', 'footer_logo', 'admin_logo', 'frontend_logo', 'invoice_logo') + [
                'favicon' => $request->favicon ? $this->upload($request, 'favicon', $general->value['favicon'] ?? null) : ($general->value['favicon'] ?? null),
                'admin_logo' => $request->admin_logo ? $this->upload($request, 'admin_logo', $general->value['admin_logo'] ?? null) : ($general->value['admin_logo'] ?? null),
                'frontend_logo' => $request->frontend_logo  ? $this->upload($request, 'frontend_logo', $general->value['frontend_logo'] ?? null) : ($general->value['frontend_logo'] ?? null),
                'invoice_logo' => $request->invoice_logo ? $this->upload($request, 'invoice_logo', $general->value['invoice_logo'] ?? null) : ($general->value['invoice_logo'] ?? null),
            ]
        ]);

        return response()->json([
            'message'   => __('General Setting updated successfully'),
            'redirect'  => route('admin.settings.index')
        ]);
    }
}
