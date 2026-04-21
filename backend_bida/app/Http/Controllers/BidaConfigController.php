<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class BidaConfigController extends Controller
{
    public function getHourlyRates()
    {
        $thuong = config('bida.hourly_rates.thuong', 50000);
        $vip = config('bida.hourly_rates.vip', 70000);
        return response()->json([
            'thuong' => (int) $thuong,
            'vip' => (int) $vip,
        ]);
    }

    public function setHourlyRates(Request $request)
    {
        $payload = $request->validate([
            'thuong' => 'required|integer|min:0',
            'vip' => 'required|integer|min:0',
        ]);
        $envPath = base_path('.env');
        $env = file_exists($envPath) ? file_get_contents($envPath) : '';

        // Helper: replace hoặc append biến env
        $setEnvVar = function ($key, $value) use (&$env) {
            if (preg_match('/^' . $key . '=.*/m', $env)) {
                $env = preg_replace('/^' . $key . '=.*/m', $key . '=' . $value, $env);
            } else {
                $env = rtrim($env) . "\n" . $key . '=' . $value . "\n";
            }
        };

        $setEnvVar('BIDA_HOURLY_RATE_THUONG', $payload['thuong']);
        $setEnvVar('BIDA_HOURLY_RATE_VIP', $payload['vip']);

        file_put_contents($envPath, $env);
        // Reload config
        Artisan::call('config:clear');
        return response()->json([
            'message' => 'Cập nhật giá giờ thành công',
            'thuong' => $payload['thuong'],
            'vip' => $payload['vip'],
        ]);
    }
}
