<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use File;
use Illuminate\Support\Facades\Gate;

class SiteSettingController extends Controller
{
    public function siteSettings()
    {
        abort_unless(Gate::allows('view setting'), 403);

        $setting = Setting::pluck('value', 'key');
        return view('admin.setting.edit', compact('setting'));
    }

    public function updateSiteSettings(Request $request, Setting $setting)
    {
        abort_unless(Gate::allows('update setting'), 403);

        $siteSettings = Setting::pluck('value', 'key');

        $old_company_logo = $siteSettings['company_logo'];
        $old_app_logo = $siteSettings['app_logo'];

        $input = $request->all();
        // Set default for checkboxes if not present
        $input['email_enabled'] = $request->has('email_enabled') ? 1 : 0;
        $input['push_notification_enabled'] = $request->has('push_notification_enabled') ? 1 : 0;

        $company_logo = $this->fileUpload($request, 'company_logo');
        $app_logo = $this->fileUpload($request, 'app_logo');

        //delete old file
        if ($company_logo) {
            $this->removeFile($old_company_logo);
            $input['company_logo'] = $company_logo;
        } else {
            unset($input['company_logo']);
        }

        if ($app_logo) {
            $this->removeFile($old_app_logo);
            $input['app_logo'] = $app_logo;
        } else {
            unset($input['app_logo']);
        }

        foreach ($input as $key => $value) {
            $setting->updateOrCreate(['key' => $key,], [
                'key' => $key,
                'value' => $value,
            ]);
        }

        return redirect()->back()->with('success', 'Site Setting updated successfully.');
    }

    public function fileUpload(Request $request, $name)
    {
        $imageName = '';
        if ($image = $request->file($name)) {
            $destinationPath = public_path() . '/uploads/site';
            $imageName = date('YmdHis') . $name . "-" . $image->getClientOriginalName();
            $image->move($destinationPath, $imageName);
        }
        return $imageName;
    }

    public function removefileFromSite($filename, $type)
    {
        if (!$filename || !$type) {
            return redirect()->back()->with('error', 'Invalid file or type.');
        }
        $this->removeFile($filename);

        Setting::where('key', $type)->update(['value' => null]);

        return redirect()->back()->with('success', 'File removed successfully.');
    }

    public function removeFile($file)
    {
        if ($file) {
            $path = public_path() . '/uploads/site/' . $file;
            if (File::exists($path)) {
                File::delete($path);
            }
        }
    }
}
