<?php

namespace Laravolt\Platform\Services;

use Spatie\MediaLibrary\Models\Media;
use Illuminate\Support\Arr;

class FileUploader
{
    public static function handle($key, $mediaCollection = 'default', $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        // Remove deleted files
        $undeleted = collect(json_decode(request("uploader.$key"), true))->flatten();
        $user->getMedia($mediaCollection)->filter(function ($item) use ($undeleted) {
            return $undeleted->search($item->getFullUrl()) === false;
        })->each->delete();

        $param = Arr::first(json_decode(Arr::get(request()->get("uploader"), $key), true));
        $media = null;
        if (($path = Arr::get($param, 'file')) !== null) {
            $mediaId = Arr::last(explode('/', dirname(parse_url($path)['path'])));
            $media = Media::find($mediaId);
        }

        $editor = $param['editor'] ?? [];

        $data = [];
        $files = (array) request()->file($key);

        foreach ($files as $i => $uploadedFile) {
            if (!$uploadedFile) {
                // Image cropped/rotated, but not re-uploaded
                if ($media && (isset($editor['crop']) || isset($editor['rotation']))) {
                    $originalFile = $media->getPath();
                    $destinationFile = storage_path(
                        "tmp/".$media->file_name
                    );

                    $saved = static::resize(
                        $originalFile,
                        null,
                        null,
                        $destinationFile,
                        (isset($editor['crop']) ? $editor['crop'] : null),
                        100,
                        (isset($editor['rotation']) ?
                            $editor['rotation'] : null)
                    );

                    if ($saved) {
                        $media = $user->addMedia($destinationFile)->toMediaCollection($mediaCollection);
                    }

                    return [$key => $media->gerUrl()];
                }

                return [];
            }

            $originalFile = $uploadedFile->getPathname();
            $destinationFile = storage_path(
                "tmp/".$uploadedFile->getFilename().".".$uploadedFile
                    ->getClientOriginalExtension()
            );

            $saved = static::resize(
                $originalFile,
                null,
                null,
                $destinationFile,
                (isset($editor['crop']) ? $editor['crop'] : null),
                100,
                (isset($editor['rotation']) ?
                    $editor['rotation'] : null)
            );

            if (!$saved) {
                $media = $user->addMediaFromRequest("$key.$i")->toMediaCollection($mediaCollection);
            } else {
                $media = $user->addMedia($destinationFile)->toMediaCollection($mediaCollection);
            }

            $data[] = [$key => $media->getUrl()];
        }

        return $data;
    }

    /**
     * resize method
     * Resize, crop and rotate images
     *
     * @public
     * @static
     * @param $filename {String} file source
     * @param $width {Number} new width
     * @param $height {Number} new height
     * @param $destination {String} file destination
     * @param $crop {boolean, Array} crop property
     * @param $quality {Number} quality of destination
     * @param $rotation {Number} rotation degrees
     * @return {boolean} resizing was successful
     */
    public static function resize(
        $filename,
        $width = null,
        $height = null,
        $destination = null,
        $crop = false,
        $quality = 90,
        $rotation = 0
    ) {
        if (!is_file($filename) || !is_readable($filename)) {
            return false;
        }

        $source = null;
        $destination = !$destination ? $filename : $destination;
        if (file_exists($destination) && !is_writable($destination)) {
            return false;
        }
        $imageInfo = @getimagesize($filename);

        if (!$imageInfo) {
            return false;
        }
        $exif = function_exists('exif_read_data') ? @exif_read_data($filename) : [];

        // detect actions
        $hasRotation = $rotation || isset($exif['Orientation']);
        $hasCrop = is_array($crop) || $crop == true;
        $hasResizing = $width || $height;

        if (!$hasRotation && !$hasCrop && !$hasResizing) {
            return;
        }

        // store image information
        list ($imageWidth, $imageHeight, $imageType) = $imageInfo;
        $imageRatio = $imageWidth / $imageHeight;

        // create GD image
        switch ($imageType) {
            case IMAGETYPE_GIF:
                $source = imagecreatefromgif($filename);
                break;
            case IMAGETYPE_JPEG:
                $source = imagecreatefromjpeg($filename);
                break;
            case IMAGETYPE_PNG:
                $source = imagecreatefrompng($filename);
                break;
            default:
                return false;
        }

        // rotation
        if ($hasRotation) {
            $cacheWidth = $imageWidth;
            $cacheHeight = $imageHeight;

            // exif rotation
            if (!empty($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 3:
                        $source = imagerotate($source, 180, 0);
                        break;
                    case 6:
                        $imageWidth = $cacheHeight;
                        $imageHeight = $cacheWidth;

                        $source = imagerotate($source, -90, 0);
                        break;
                    case 8:
                        $imageWidth = $cacheHeight;
                        $imageHeight = $cacheWidth;

                        $source = imagerotate($source, 90, 0);
                        break;
                }

                $cacheWidth = $imageWidth;
                $cacheHeight = $imageHeight;
            }

            // param rotation
            if ($rotation == 90 || $rotation == 270) {
                $imageWidth = $cacheHeight;
                $imageHeight = $cacheWidth;
            }
            $rotation = $rotation * -1;
            $source = imagerotate($source, $rotation, 0);
        }

        // crop
        $crop = array_merge([
            'left' => 0,
            'top' => 0,
            'width' => $imageWidth,
            'height' => $imageHeight,
            '_paramCrop' => $crop,
        ], is_array($crop) ? $crop : []);
        if (is_array($crop['_paramCrop'])) {
            $crop['left'] = round($crop['_paramCrop']['left']);
            $crop['top'] = round($crop['_paramCrop']['top']);
            $crop['width'] = round($crop['_paramCrop']['width']);
            $crop['height'] = round($crop['_paramCrop']['height']);
        }

        // set default $width and $height
        $width = !$width ? $crop['width'] : $width;
        $height = !$height ? $crop['height'] : $height;
        $ratio = $width / $height;

        // resize
        if ($crop['_paramCrop'] === true) {
            if ($imageRatio >= $ratio) {
                $crop['newWidth'] = $crop['width'] / ($crop['height'] / $height);
                $crop['newHeight'] = $height;
            } else {
                $crop['newHeight'] = $crop['height'] / ($crop['width'] / $width);
                $crop['newWidth'] = $width;
            }

            $crop['left'] = 0 - ($crop['newWidth'] - $width) / 2;
            $crop['top'] = 0 - ($crop['newHeight'] - $height) / 2;

            if ($crop['width'] < $width || $crop['height'] < $height) {
                $crop['left'] = $crop['width'] < $width ? $width / 2 - $crop['width'] / 2 : 0;
                $crop['top'] = $crop['height'] < $height ? $height / 2 - $crop['height'] / 2 : 0;
                $crop['newWidth'] = $crop['width'];
                $crop['newHeight'] = $crop['height'];
            }
        } elseif ($crop['width'] < $width && $crop['height'] < $height) {
            $width = $crop['width'];
            $height = $crop['height'];
        } else {
            $newRatio = $crop['width'] / $crop['height'];

            if ($ratio > $newRatio) {
                $width = $height * $newRatio;
            } else {
                $height = $width / $newRatio;
            }
        }

        // save
        $dest = null;
        $destExt = strtolower(substr($destination, strrpos($destination, '.') + 1));

        if (pathinfo($destination, PATHINFO_EXTENSION)) {
            if (in_array($destExt, ['gif', 'jpg', 'jpeg', 'png'])) {
                if ($destExt == 'gif') {
                    $imageType = IMAGETYPE_GIF;
                }
                if ($destExt == 'jpg' || $destExt == 'jpeg') {
                    $imageType = IMAGETYPE_JPEG;
                }
                if ($destExt == 'png') {
                    $imageType = IMAGETYPE_PNG;
                }
            }
        } else {
            $imageType = IMAGETYPE_JPEG;
            $destination .= '.jpg';
        }
        switch ($imageType) {
            case IMAGETYPE_GIF:
                $dest = imagecreatetruecolor($width, $height);
                $background = imagecolorallocatealpha($dest, 255, 255, 255, 1);
                imagecolortransparent($dest, $background);
                imagefill($dest, 0, 0, $background);
                imagesavealpha($dest, true);
                break;
            case IMAGETYPE_JPEG:
                $dest = imagecreatetruecolor($width, $height);
                $background = imagecolorallocate($dest, 255, 255, 255);
                imagefilledrectangle($dest, 0, 0, $width, $height, $background);
                break;
            case IMAGETYPE_PNG:
                if (!imageistruecolor($source)) {
                    $dest = imagecreate($width, $height);
                    $background = imagecolorallocatealpha($dest, 255, 255, 255, 1);
                    imagecolortransparent($dest, $background);
                    imagefill($dest, 0, 0, $background);
                } else {
                    $dest = imagecreatetruecolor($width, $height);
                }
                imagealphablending($dest, false);
                imagesavealpha($dest, true);
                break;
            default:
                return false;
        }

        imageinterlace($dest, true);

        imagecopyresampled(
            $dest,
            $source,
            isset($crop['newWidth']) ? $crop['left'] : 0,
            isset($crop['newHeight']) ? $crop['top'] : 0,
            !isset($crop['newWidth']) ? $crop['left'] : 0,
            !isset($crop['newHeight']) ? $crop['top'] : 0,
            isset($crop['newWidth']) ? $crop['newWidth'] : $width,
            isset($crop['newHeight']) ? $crop['newHeight'] : $height,
            $crop['width'],
            $crop['height']
        );

        switch ($imageType) {
            case IMAGETYPE_GIF:
                imagegif($dest, $destination);
                break;
            case IMAGETYPE_JPEG:
                imagejpeg($dest, $destination, $quality);
                break;
            case IMAGETYPE_PNG:
                imagepng($dest, $destination, 10 - $quality / 10);
                break;
        }

        imagedestroy($source);
        imagedestroy($dest);
        clearstatcache(true, $destination);

        return [
            'width' => $crop['width'],
            'height' => $crop['height'],
            'ratio' => $crop['width'] / $crop['height'],
            'type' => $destExt,
        ];
    }
}
