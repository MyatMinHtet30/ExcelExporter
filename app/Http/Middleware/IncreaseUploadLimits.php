<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IncreaseUploadLimits
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Increase PHP limits for large uploads including HEIC files
        ini_set('upload_max_filesize', '100M');
        ini_set('post_max_size', '5000M');
        ini_set('max_file_uploads', '200');
        ini_set('max_execution_time', '1800');
        ini_set('max_input_time', '1800');
        ini_set('memory_limit', '2048M');
        ini_set('max_input_vars', '20000');

        return $next($request);
    }
}