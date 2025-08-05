<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

use function Adminer\view;

class SetDatabaseConnection
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (config('app.env') !== 'local') {
            // Extract subdomain from the host
            $host = $request->getHost();
            $hostParts = explode('.', $host) ?? [];
            $subdomain = $hostParts[0] ?? ''; // Fallback to 'default' if no subdomain

            $cacheKey = 'db_sajiloattendance1112_' . $subdomain;

            $dbConfig = Cache::remember($cacheKey, 18000, function () use ($subdomain) { // 5 min
                $response = Http::get('https://sajiloattendance.com/api/getdatabase', [
                    'subdomain' => $subdomain,
                ]);

                if ($response->successful()) {
                    return $response->json();
                }
                return null;
            });

            if (isset($dbConfig['data']) && isset($dbConfig['data']['Website_status'])) {
                if ($dbConfig['data']['Website_status'] != 'Active') {
                    if ($request->expectsJson()) {
                        return response()->json(['error' => 'Website is not active'], 500);
                    } else {
                        return response()->view('errors.inactive');
                    }
                }
            }

            if (isset($dbConfig['data']) && isset($dbConfig['data']['Expiry_date'])) {
                $expDate = $dbConfig['data']['Expiry_date'];

                if(strtotime(date('Y-m-d')) > strtotime($expDate)){
                    if ($request->expectsJson()) {
                        return response()->json(['error' => 'Website is expired'], 500);
                    } else {
                        return response()->view('errors.expire');
                    }
                }
            }


            if (isset($dbConfig['data']) && isset($dbConfig['data']['Database'])) {
                Config::set('database.connections.mysql', [
                    'driver' => 'mysql',
                    'host' => env('DB_HOST', '127.0.0.1'),
                    'port' => env('DB_PORT', '3306'),
                    'database' => $dbConfig['data']['Database'],
                    'username' => 'root',
                    'password' => 'Paradise@098',
                    'charset' => 'utf8mb4',
                    'collation' => 'utf8mb4_unicode_ci',
                    'prefix' => '',
                    'strict' => true,
                    'engine' => null,
                ]);

                Session::put('details', $dbConfig['data']);

                DB::purge('mysql');
                DB::reconnect('mysql');

                Config::set('database.default', 'mysql');
                try {
                    DB::connection()->getPdo();
                } catch (\Exception $e) {
                    if ($request->expectsJson()) {
                        return response()->json(['error' => 'Website is not active'], 500);
                    } else {
                        return response()->view('errors.inactive');
                    }
                    // return response()->json(
                    //     'Database Authentication Failed',
                    //     500
                    // );
                }
            } else {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Website is not active'], 500);
                } else {
                    return response()->view('errors.inactive');
                }

                // return response()->json(
                //     'Database Authentication Failed',
                //     500
                // );
            }
        }

        return $next($request);
    }
}
