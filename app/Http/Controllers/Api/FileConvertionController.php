<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileToBase64Request;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class FileConvertionController extends Controller
{
    public function convertFileToBase64(FileToBase64Request $request)
    {
        $file = $request->file('document');

        $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

        $res_file = $this->base64ToUploadedFile($base64);

        return response()->json([
            'original' => [
                'name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ],
            'converted' => [
                'name' => $res_file?->getClientOriginalName(),
                'mime' => $res_file?->getMimeType(),
                'size' => $res_file?->getSize(),
            ],
        ]);
    }

    private function base64ToUploadedFile(string $base64): UploadedFile|null
    {
        if (! preg_match('/^data:(application\/pdf|image\/(jpeg|jpg|png));base64,/', $base64)) {
            return null;
        }
        [$meta, $content] = explode(',', $base64, 2);

        if (! preg_match('/^data:(.*?);base64$/', $meta, $matches)) {
            return null;
        }
        $mime = $matches[1];

        if (base64_decode($content, true) === false) {
            return null;
        }

        $extension = match ($mime) {
            'application/pdf' => 'pdf',
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            default => throw new \Exception('Unsupported file type'),
        };

        if (! $extension) {
            return null;
        }

        $fileName = Str::uuid() . '.' . $extension;
        $filePath = sys_get_temp_dir() . '/' . $fileName;

        file_put_contents($filePath, base64_decode($content));

        return new UploadedFile(
            $filePath,
            $fileName,
            $mime,
            null,
            true
        );
    }
}
