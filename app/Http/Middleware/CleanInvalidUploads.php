<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CleanInvalidUploads
{
    /**
     * Geçersiz dosya yüklemelerini temizle
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Dosya yüklemesi varsa kontrol et - tüm hataları yakala
        try {
            $keysToRemove = [];

            foreach ($request->files->all() as $key => $files) {
                try {
                    if (is_array($files)) {
                        $validFiles = [];
                        foreach ($files as $file) {
                            try {
                                if ($file && $file->isValid() && file_exists($file->getPathname())) {
                                    $validFiles[] = $file;
                                }
                            } catch (\Throwable $e) {
                                continue;
                            }
                        }
                        if (empty($validFiles)) {
                            $keysToRemove[] = $key;
                        } else {
                            $request->files->set($key, $validFiles);
                        }
                    } elseif ($files) {
                        try {
                            if (!$files->isValid() || !file_exists($files->getPathname())) {
                                $keysToRemove[] = $key;
                            }
                        } catch (\Throwable $e) {
                            $keysToRemove[] = $key;
                        }
                    }
                } catch (\Throwable $e) {
                    $keysToRemove[] = $key;
                }
            }

            foreach ($keysToRemove as $key) {
                $request->files->remove($key);
            }
        } catch (\Throwable $e) {
            // Herhangi bir hata olursa tüm dosyaları temizle
            foreach ($request->files->keys() as $key) {
                $request->files->remove($key);
            }
        }

        return $next($request);
    }
}
