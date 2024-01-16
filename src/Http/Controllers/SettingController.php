<?php

namespace Isotope\ShopBoss\Http\Controllers;

use Carbon\Carbon;
use Isotope\ShopBoss\Models\Setting;
use Isotope\ShopBoss\Models\Currency;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Isotope\ShopBoss\Http\Requests\StoreSettingsRequest;
use Isotope\ShopBoss\Http\Requests\StoreSmtpSettingsRequest;

class SettingController extends Controller
{
    public static $permissions = [
        'index'   => ['access_settings', 'Setting List'],
        'update'  => ['update_settings', 'Setting update'],
    ];

    public function index()
    {
        abort_if(Gate::denies('access_settings'), 403);

        $currencies = Currency::all();
        $settings   = Setting::firstOrFail();

        return view('pos::setting.index', compact('settings','currencies'));
    }

    public function update(StoreSettingsRequest $request)
    {
        $data = [];
        if ($files = $request->file('logo')) {
            $currentDate = Carbon::now()->toDateString();
            $imageName   = $currentDate . '-' . uniqid() . '.' . $files->getClientOriginalExtension();

            $folderPath = 'logo';
            $dir = 'storage/logo'; 
            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }
            $files->move($dir, $imageName);
            $data['logo'] ='/storage/logo/' . $imageName;
        }

        Setting::firstOrFail()->update([
            'company_name'              => $request->company_name,
            'company_email'             => $request->company_email,
            'company_phone'             => $request->company_phone,
            'notification_email'        => $request->notification_email,
            'company_address'           => $request->company_address,
            'default_currency_id'       => $request->default_currency_id,
            'default_currency_position' => $request->default_currency_position,
            'footer_text'               => $request->footer_text,
            'logo_bg_color'             => $request->logo_bg_color,
            'sidebar_bg_color'          => $request->sidebar_bg_color,
            'menu_active_color'         => $request->menu_active_color,
            'menu_hover_color'          => $request->menu_hover_color,
            'button_color'              => $request->button_color,
            'card_header_color'         => $request->card_header_color,
            'table_header_color'        => $request->table_header_color,
        ]+$data);

        cache()->forget('settings');

        toast('Settings Updated!', 'info');

        return redirect()->route('settings.index');
    }

    public function updateSmtp(StoreSmtpSettingsRequest $request)
    {
        $toReplace = array(
            'MAIL_MAILER=' . env('MAIL_HOST'),
            'MAIL_HOST="' . env('MAIL_HOST') . '"',
            'MAIL_PORT=' . env('MAIL_PORT'),
            'MAIL_FROM_ADDRESS="' . env('MAIL_FROM_ADDRESS') . '"',
            'MAIL_FROM_NAME="' . env('MAIL_FROM_NAME') . '"',
            'MAIL_USERNAME="' . env('MAIL_USERNAME') . '"',
            'MAIL_PASSWORD="' . env('MAIL_PASSWORD') . '"',
            'MAIL_ENCRYPTION="' . env('MAIL_ENCRYPTION') . '"'
        );

        $replaceWith = array(
            'MAIL_MAILER=' . $request->mail_mailer,
            'MAIL_HOST="' . $request->mail_host . '"',
            'MAIL_PORT=' . $request->mail_port,
            'MAIL_FROM_ADDRESS="' . $request->mail_from_address . '"',
            'MAIL_FROM_NAME="' . $request->mail_from_name . '"',
            'MAIL_USERNAME="' . $request->mail_username . '"',
            'MAIL_PASSWORD="' . $request->mail_password . '"',
            'MAIL_ENCRYPTION="' . $request->mail_encryption . '"'
        );

        try {
            file_put_contents(base_path('.env'), str_replace($toReplace, $replaceWith, file_get_contents(base_path('.env'))));
            Artisan::call('cache:clear');

            toast('Mail Settings Updated!', 'info');
        } catch (\Exception $exception) {
            Log::error($exception);
            session()->flash('settings_smtp_message', 'Something Went Wrong!');
        }

        return redirect()->route('settings.index');
    }
}
