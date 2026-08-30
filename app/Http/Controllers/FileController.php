<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FileController extends Controller
{
    /**
     * Serve files from the public storage disk without relying on the
     * public/storage symlink (which does not exist on InfinityFree).
     *
     * Usage: /files/{path}
     */
    public function show(Request $request, string $path): StreamedResponse
    {
        $path = trim($path, '/');

        // Reject any path traversal (..) or absolute paths outright.
        if ($path === '' || str_contains($path, '..') || str_starts_with($path, '/')) {
            throw new NotFoundHttpException('File not found.');
        }

        $disk = Storage::disk('public');

        // Any storage error (bad path, I/O, etc.) → treat as not found.
        try {
            if (!$disk->exists($path)) {
                throw new NotFoundHttpException('File not found.');
            }
            $mime = $disk->mimeType($path) ?: 'application/octet-stream';
        } catch (NotFoundHttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new NotFoundHttpException('File not found.');
        }

        $urlName = basename($path);

        return $disk->response($path, $urlName, [
            'Content-Type' => $mime,
        ]);
    }
}
