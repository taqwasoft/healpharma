<?php

namespace App\Http\Controllers\Api;

use App\Models\Currency;
use App\Models\Option;
use App\Models\Plan;
use App\Models\User;
use App\Models\Business;
use App\Helpers\HasUploader;
use App\Models\UserCurrency;
use Illuminate\Http\Request;
use App\Models\PlanSubscribe;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class BusinessController extends Controller
{
    use HasUploader;

    public function index()
    {
        $business_id = auth()->user()->business_id;

        // Ensure currency exists
        $business_currency = UserCurrency::select('id', 'name', 'code', 'symbol', 'position')
            ->where('business_id', $business_id)
            ->first();

        if (!$business_currency) {
            $currency = Currency::where('is_default', 1)->first();
            UserCurrency::create([
                'name' => $currency->name,
                'code' => $currency->code,
                'rate' => $currency->rate,
                'business_id' => $business_id,
                'symbol' => $currency->symbol,
                'currency_id' => $currency->id,
                'position' => $currency->position,
                'country_name' => $currency->country_name,
            ]);
        }

        // Fetch user and business info
        $user = User::select('id', 'name', 'role', 'visibility', 'lang', 'email')->findOrFail(auth()->id());
        $business = Business::with('category:id,name', 'enrolled_plan:id,plan_id,business_id,price,duration', 'enrolled_plan.plan:id,subscriptionName')->findOrFail($business_id);

        //admin setting option
        $generalValue = Option::where('key', 'general')->first()->value ?? [];
        $develop_by_level = $generalValue['admin_footer_text'] ?? '';
        $develop_by = $generalValue['admin_footer_link_text'] ?? '';
        $develop_by_link = $generalValue['admin_footer_link'] ?? '';

        // Get business settings option
        $option = Option::where('key', 'business-settings')
            ->whereJsonContains('value->business_id', $business_id)
            ->first();

        $invoice_logo = $option->value['invoice_logo'] ?? null;
        $sale_rounding_option = $option->value['sale_rounding_option'] ?? 'none';
        $invoice_note_level = $option->value['invoice_note_level'] ?? null;
        $invoice_note = $option->value['invoice_note'] ?? null;
        $gratitude_message = $option->value['gratitude_message'] ?? null;

        $data = array_merge(
            $business->toArray(),
            ['user' => $user->toArray()],
            ['business_currency' => $business_currency],
            ['invoice_logo' => $invoice_logo],
            ['sale_rounding_option' => $sale_rounding_option],
            ['invoice_size' => !empty(invoice_setting()) ? invoice_setting() : null],
            ['invoice_note_level' => $invoice_note_level],
            ['invoice_note' => $invoice_note],
            ['gratitude_message' => $gratitude_message],
            ['develop_by_level' => $develop_by_level],
            ['develop_by' => $develop_by],
            ['develop_by_link' => $develop_by_link],
        );

        return response()->json([
            'message' => __('Data fetched successfully.'),
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address' => 'nullable|max:250',
            'companyName' => 'required|max:250',
            'pictureUrl' => 'nullable|image|max:5120',
            'shopOpeningBalance' => 'nullable|numeric',
            'business_category_id' => 'required|exists:business_categories,id',
        ]);

        DB::beginTransaction();
        try {

            $user = auth()->user();
            $free_plan = Plan::where('subscriptionPrice', '<=', 0)->orWhere('offerPrice', '<=', 0)->first();

            $business = Business::create($request->except('pictureUrl') + [
                            'phoneNumber' => $request->phoneNumber,
                            'subscriptionDate' => $free_plan ? now() : NULL,
                            'will_expire' => now()->addDays($free_plan->duration),
                            'pictureUrl' => $request->pictureUrl ? $this->upload($request, 'pictureUrl') : NULL
                        ]);

            $user->update([
                'business_id' => $business->id,
                'phone' => $request->phoneNumber,
                'name' => $business->companyName,
            ]);

            if ($free_plan) {
                $subscribe = PlanSubscribe::create([
                                'plan_id' => $free_plan->id,
                                'business_id' => $business->id,
                                'duration' => $free_plan->duration,
                            ]);

                $business->update([
                    'plan_subscribe_id' => $subscribe->id,
                ]);
            }

            Cache::forget('plan-data-' . $business->id);

            DB::commit();
            return response()->json([
                'message' => __('Business setup completed.'),
            ]);

        } catch (\Throwable $th) {
            DB::rollback();
            return response()->json(__('Something was wrong, Please contact with admin.'), 403);
        }
    }

    public function update(Request $request, Business $business)
    {
        $request->validate([
            'address' => 'nullable|max:250',
            'companyName' => 'nullable|required_if:sale_rounding_option,!=,null|max:250',
            'pictureUrl' => 'nullable|image|max:5120',
            'invoice_logo' => 'nullable|image|max:5120',
            'sale_rounding_option' => 'nullable|in:none,round_up,nearest_whole_number,nearest_0.05,nearest_0.1,nearest_0.5',
            'business_category_id' => 'nullable|required_if:sale_rounding_option,!=,null|exists:business_categories,id',
            'phoneNumber'  => ['nullable', 'min:5', 'max:15'],
            'invoice_size' => 'nullable|string|max:100',
            'gratitude_message' => 'nullable|string|max:100',
        ]);

        // Update when sale_rounding_option is not provided
        if (!$request->sale_rounding_option) {
            auth()->user()->update([
                'name' => $request->companyName,
                'phone' => $request->phoneNumber,
            ]);

            $business->update($request->except('pictureUrl') + [
                    'pictureUrl' => $request->pictureUrl ? $this->upload($request, 'pictureUrl', $business->pictureUrl) : $business->pictureUrl
                ]);
        }


        // Update or insert business settings
        $setting = Option::where('key', 'business-settings')
            ->whereJsonContains('value->business_id', $business->id)
            ->first();

        $invoiceLogo = $request->invoice_logo ? $this->upload($request, 'invoice_logo', $setting->value['invoice_logo'] ?? null) : ($setting->value['invoice_logo'] ?? null);

        $settingData = [
            'business_id' => $business->id,
            'invoice_logo' => $invoiceLogo,
            'sale_rounding_option' => $request->sale_rounding_option ?? 'none',
            'invoice_note_level' => $request->invoice_note_level,
            'invoice_note' => $request->invoice_note,
            'gratitude_message' => $request->gratitude_message,
            'tax_name' => $request->tax_name,
            'tax_no' => $request->tax_no,
        ];

        if ($setting) {
            $setting->update([
                'value' => array_merge($setting->value, $settingData),
            ]);
        } else {
            Option::create([
                'key' => 'business-settings',
                'value' => $settingData,
            ]);
        }

        // Update Invoice Settings
        if ($request->filled('invoice_size')) {
            $invoiceKey = 'invoice_setting_' . $business->id;

            Option::updateOrCreate(
                ['key' => $invoiceKey],
                ['value' => $request->invoice_size]
            );

            Cache::forget($invoiceKey);
        }

        Cache::forget("business_setting_{$business->id}");
        Cache::forget("business_sale_rounding_{$business->id}");

        return response()->json([
            'message' => __('Data saved successfully.'),
            'business' => $business,
            'invoice_logo' => $invoiceLogo,
            'sale_rounding_option' => $request->sale_rounding_option,
            'invoice_size' => $request->invoice_size,
            'invoice_note_level' => $request->invoice_note_level,
            'invoice_note' => $request->invoice_note,
            'gratitude_message' => $request->gratitude_message,
            'tax_name' => $request->tax_name,
            'tax_no' => $request->tax_no,
        ]);
    }

    public function updateExpireDate(Request $request)
    {
        $days = $request->query('days', 0);
        $operation = $request->query('operation');
        $business = Business::where('id', auth()->user()->business_id)->first();
        if (!$business) {
            return response()->json([
                'message' => 'Business not found.',
            ], 404);
        }
        if ($operation === 'add') {
            $business->will_expire = now()->addDays($days);
        } elseif ($operation === 'sub') {
            $business->will_expire = now()->subDays($days);
        } else {
            return response()->json([
                'message' => 'Invalid operation. Use "add" or "sub".',
            ], 400);
        }
        $business->save();
        return response()->json([
            'message' => 'Expiry date updated successfully.',
            'will_expire' => $business->will_expire,
        ]);
    }

    public function deleteBusiness(Request $request)
    {
        $request->validate([
            'password' => 'required|string',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => __('The provided password is incorrect.'),
            ], 406);
        }

        Business::where('id', $user->business_id)->delete();

        return response()->json([
            'message' => __('Data deleted successfully.'),
        ]);
    }


}
