<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RouteMobileSMSController;
use App\Http\Controllers\SmsCallbackController;

Route::prefix('route-mobile')
    ->controller(RouteMobileSMSController::class)
    ->group(function () {
        Route::post('/bulk-sms-bd/send', 'sendBulkSmsBd');
    });

Route::prefix('callback')
    ->as('callback.')
    ->controller(SmsCallbackController::class)
    ->group(function () {
        Route::post('/route-mobile', 'routeMobile')->name('route-mobile');
    });


use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

Route::post('file-test', function (Request $request) {
    $file = $request->file('file');

    // dd($file);

    $base64 = 'data:' . $file->getMimeType() . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
    // dd($base64);

    $res_file = base64ToUploadedFile($base64);

    return response()->json([
        'original' => [
            'name' => $file->getClientOriginalName(),
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
        ],
        'converted' => [
            'name' => $res_file->getClientOriginalName(),
            'mime' => $res_file->getMimeType(),
            'size' => $res_file->getSize(),
        ],
    ]);
});


function base64ToUploadedFile(string $base64): UploadedFile
{
    [$meta, $content] = explode(',', $base64);

    preg_match('/data:(.*?);base64/', $meta, $matches);
    $mime = $matches[1];

    $extension = match ($mime) {
        'application/pdf' => 'pdf',
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        default => throw new \Exception('Unsupported file type'),
    };

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
