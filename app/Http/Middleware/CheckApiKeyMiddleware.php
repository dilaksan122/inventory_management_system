<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use App\Models\SystemSettings;
use App\Traits\ApiReturnFormatTrait;
use Closure;
use Illuminate\Http\Request;

class CheckApiKeyMiddleware
{
    use ApiReturnFormatTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if($request->hasHeader('Api-Key')):
            $keys = SystemSettings::where('settings_key','api_key')->first()->settings_value;
            if($request->header('Api-Key',$keys)):
                return $next($request);
            endif;
        endif;
        return $this->responseWithError(__('API key invalid'), [], 403);
    }
}
