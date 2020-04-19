<?php

namespace App\Http\Middleware;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class CompressContent
{
    public function handle($request, $next)
    {
        $response = $next($request);

        if ($response instanceof JsonResponse) {
            $compressedResponse = new Response();
            $compressed = $this->compress($response->getContent());
            $compressedResponse->setContent($compressed);
            $compressedResponse->withHeaders($this->headers());
            $compressedResponse->header('Content-Length', strlen($compressed));

            $this->setZlib();

            return $compressedResponse;
        }

        return $response;
    }

    private function setZlib(): void
    {
        ini_set('zlib.output_compression', 'Off');
    }

    private function headers()
    {
        return [
            'Content-Type' => 'application/x-download',
            'Content-Encoding'=> 'gzip',
            'Cache-Control' =>  'no-cache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
        ];
    }

    private function compress($data)
    {
        return gzencode($data);
    }
}
