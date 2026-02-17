<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FileToBase64Request;
use App\Http\Requests\CreatePDFFromFilesRequest;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use setasign\Fpdi\Fpdi;

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

    public function createPdfFromFiles(CreatePDFFromFilesRequest $request)
    {
        $files = array_merge($request['files'], ['https://picsum.photos/id/237/200/300', 'https://picsum.photos/seed/picsum/200/300', 'https://pdfobject.com/pdf/sample.pdf']);;

        $outputPath = storage_path('app/public/merged.pdf');

        $this->mergeMultipleFilesToSinglePDF($files, $outputPath);

        return response()->download($outputPath);
    }

    private function mergeMultipleFilesToSinglePDF(array $files, string $outputPath): string
    {
        $pdf = new Fpdi();

        $pageWidth = 210;
        $pageHeight = 297;

        foreach ($files as $file) {

            $pdf->AddPage('P', [$pageWidth, $pageHeight]);

            if ($file instanceof UploadedFile) {
                $extension = strtolower($file->getClientOriginalExtension());
                $filePath = $file->getRealPath();
            } elseif (filter_var($file, FILTER_VALIDATE_URL)) {
                $extension = pathinfo(parse_url($file, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
                $tempFile = tempnam(sys_get_temp_dir(), 'pdfimg_') . '.' . $extension;
                file_put_contents($tempFile, file_get_contents($file));
                $filePath = $tempFile;
            }else {
                $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $filePath = $file;
            }

            if ($extension === 'pdf') {

                $pageCount = $pdf->setSourceFile($filePath);

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $template = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($template);

                    $scaleX = $pageWidth / $size['width'];
                    $scaleY = $pageHeight / $size['height'];
                    $scale = min($scaleX, $scaleY);

                    $w = $size['width'] * $scale;
                    $h = $size['height'] * $scale;

                    $x = ($pageWidth - $w) / 2;
                    $y = ($pageHeight - $h) / 2;

                    $pdf->useTemplate($template, $x, $y, $w, $h);
                }

            } elseif (in_array($extension, ['jpg', 'jpeg', 'png'])) {

                list($width, $height) = getimagesize($filePath);

                $scaleX = $pageWidth / $width;
                $scaleY = $pageHeight / $height;
                $scale = min($scaleX, $scaleY);

                $w = $width * $scale;
                $h = $height * $scale;

                $x = ($pageWidth - $w) / 2;
                $y = ($pageHeight - $h) / 2;

                $pdf->Image($filePath, $x, $y, $w, $h, strtoupper($extension));
            }
        }

        $pdf->Output('F', $outputPath);

        return $outputPath;
    }
}
