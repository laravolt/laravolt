<?php

namespace Laravolt\Media\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VideoStreamController extends \Illuminate\Routing\Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Media $media, Request $request)
    {
        if ($media->disk === 's3') {
            return $this->streamFromS3($media, $request);
        }

        $path = $media->getPathRelativeToRoot();

        if (! file_exists($path)) {
            abort(404);
        }

        $size = filesize($path);
        $start = 0;
        $end = $size - 1;

        $headers = [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ];

        if ($request->hasHeader('Range')) {
            preg_match('/bytes=(\d+)-(\d*)/', $request->header('Range'), $matches);
            if (count($matches) >= 2) {
                $start = intval($matches[1]);
                if (isset($matches[2]) && $matches[2] !== '') {
                    $end = intval($matches[2]);
                }
                $length = $end - $start + 1;
                $headers['Content-Range'] = "bytes $start-$end/$size";
                $headers['Content-Length'] = $length;

                $responseCode = 206;
            } else {
                // Invalid range header, fall back to normal response
                $headers['Content-Length'] = $size;
                $responseCode = 200;
            }
        } else {
            $headers['Content-Length'] = $size;
            $responseCode = 200;
        }

        return new StreamedResponse(function () use ($path, $start, $end) {
            $fp = fopen($path, 'rb');
            fseek($fp, $start);
            $bytesToRead = $end - $start + 1;
            $buffer = 1024 * 8;
            while ($bytesToRead > 0 && ! feof($fp)) {
                $readLength = ($bytesToRead > $buffer) ? $buffer : $bytesToRead;
                echo fread($fp, $readLength);
                flush();
                $bytesToRead -= $readLength;
            }
            fclose($fp);
        }, $responseCode, $headers);

    }

    protected function streamFromS3(Media $media, Request $request)
    {
        $disk = Storage::disk('s3');
        $path = $media->getPath();

        if (! $disk->exists($path)) {
            abort(404);
        }

        $size = $disk->size($path);
        $start = 0;
        $end = $size - 1;

        $headers = [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ];

        if ($request->hasHeader('Range')) {
            preg_match('/bytes=(\d+)-(\d*)/', $request->header('Range'), $matches);
            if (count($matches) >= 2) {
                $start = intval($matches[1]);
                if (isset($matches[2]) && $matches[2] !== '') {
                    $end = intval($matches[2]);
                }
                $length = $end - $start + 1;
                $headers['Content-Range'] = "bytes $start-$end/$size";
                $headers['Content-Length'] = $length;
                $responseCode = 206;
            } else {
                // Invalid range header, fall back to normal response
                $headers['Content-Length'] = $size;
                $responseCode = 200;
            }
        } else {
            $headers['Content-Length'] = $size;
            $responseCode = 200;
        }

        return new StreamedResponse(function () use ($disk, $path, $start, $end) {
            $stream = $disk->readStream($path);
            fseek($stream, $start);
            $bytesToRead = $end - $start + 1;
            $buffer = 1024 * 8;
            while ($bytesToRead > 0 && ! feof($stream)) {
                $readLength = ($bytesToRead > $buffer) ? $buffer : $bytesToRead;
                echo fread($stream, $readLength);
                flush();
                $bytesToRead -= $readLength;
            }
            fclose($stream);
        }, $responseCode, $headers);
    }
}
