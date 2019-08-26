<?php

namespace Laravolt\Media;

# ======================================================================== #
#
#  Title      [PHP] FileUploader
#  Author:    innostudio.de
#  Website:   https://innostudio.de/fileuploader/
#  Version:   2.2
#  License:   https://innostudio.de/fileuploader/documentation/#license
#  Date:      01-Apr-2019
#  Purpose:   Validate, Remove, Upload, Sort files and Resize images on server.
#  Information: Don't forget to check the options memory_limit, file_uploads, upload_max_filesize, max_file_uploads and post_max_size in the php.ini
#
# ======================================================================== #

use Spatie\MediaLibrary\Models\Media;

class FileUploader
{
    private $default_options = [
        'limit' => null,
        'maxSize' => null,
        'fileMaxSize' => null,
        'extensions' => null,
        'disallowedExtensions' => ['htaccess', 'php', 'php3', 'php4', 'php5', 'phtml'],
        'required' => false,
        'uploadDir' => 'uploads/',
        'title' => ['auto', 12],
        'replace' => false,
        'editor' => null,
        'listInput' => true,
        'files' => [],
        'move_uploaded_file' => null,
        'validate_file' => null,
    ];

    private $field = null;

    protected $options = null;

    public static $S3;

    /**
     * __construct method
     *
     * @public
     * @param $name  {$_FILES key}
     * @param $options  {null, Array}
     */
    public function __construct($name, $options = null)
    {
        $this->default_options['move_uploaded_file'] = function ($tmp, $dest, $item) {
            return move_uploaded_file($tmp, $dest);
        };

        return $this->initialize($name, $options);
    }

    /**
     * initialize method
     * initialize the plugin
     *
     * @private
     * @param $inputName  {String} Input name
     * @param $options  {null, Array}
     */
    private function initialize($inputName, $options)
    {
        $name = is_array($inputName) ? end($inputName) : $inputName;
        $_FilesName = is_array($inputName) ? $inputName[0] : $inputName;

        // merge options
        $this->options = $this->default_options;
        if ($options) {
            $this->options = array_merge($this->options, $options);
        }
        if (!is_array($this->options['files'])) {
            $this->options['files'] = [];
        }

        // create field array
        $this->field = [
            'name' => $name,
            'input' => null,
            'listInput' => $this->readListInput($name),
        ];

        if (isset($_FILES[$_FilesName])) {
            // set field input
            $this->field['input'] = $_FILES[$_FilesName];
            if (is_array($inputName)) {
                $arr = [];

                foreach ($this->field['input'] as $k => $v) {
                    $arr[$k] = $v[$inputName[1]];
                }

                $this->field['input'] = $arr;
            }

            // tranform an no-multiple input to multiple
            // made only to simplify the next uploading steps
            if (!is_array($this->field['input']['name'])) {
                $this->field['input'] = array_merge($this->field['input'], [
                    "name" => [$this->field['input']['name']],
                    "tmp_name" => [$this->field['input']['tmp_name']],
                    "type" => [$this->field['input']['type']],
                    "error" => [$this->field['input']['error']],
                    "size" => [$this->field['input']['size']],
                ]);
            }

            // remove empty filenames
            // only for addMore option
            foreach ($this->field['input']['name'] as $key => $value) {
                if (empty($value)) {
                    unset($this->field['input']['name'][$key]);
                    unset($this->field['input']['type'][$key]);
                    unset($this->field['input']['tmp_name'][$key]);
                    unset($this->field['input']['error'][$key]);
                    unset($this->field['input']['size'][$key]);
                }
            }

            // set field length (files count)
            $this->field['count'] = count($this->field['input']['name']);

            return true;
        } else {
            return false;
        }
    }

    public static function handle($key, $mediaCollection = 'media', $user = null)
    {
        if (!$user) {
            $user = auth()->user();
        }

        // Clear image field if user remove existing image
        $fileRemoved = request("uploader.$key") === '[]';
        if ($fileRemoved) {
            $user->clearMediaCollection($mediaCollection);

            return [$key => null];
        }

        $param = array_first(json_decode(array_get(request()->get("uploader"), $key), true));
        $media = null;
        if (($path = array_get($param, 'file')) !== null) {
            $mediaId = array_last(explode('/', dirname(parse_url($path)['path'])));
            $media = Media::find($mediaId);
        }

        $editor = $param['editor'] ?? [];

        $uploadedFile = request()->file($key);
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

                return [$key => $media->getFullUrl()];
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
            $media = $user->addMediaFromRequest($key)->toMediaCollection($mediaCollection);
        } else {
            $media = $user->addMedia($destinationFile)->toMediaCollection($mediaCollection);
        }

        return [$key => $media->getFullUrl()];
    }

    /**
     * getOptions method
     * Returns the options object
     *
     * @public
     * @return {Array}
     */
    public function getOptions()
    {
        return array_filter($this->options, function ($var) {
            return gettype($var) != "object";
        });
    }

    /**
     * upload method
     * Call the uploadFiles method
     *
     * @public
     * @return {Array}
     */
    public function upload()
    {
        return $this->uploadFiles();
    }

    /**
     * getFileList method
     * Get the list of the preloaded and uploaded files
     *
     * @public
     * @param @customKey {null, String} File attrbite that should be in the list
     * @return {null, Array}
     */
    public function getFileList($customKey = null)
    {
        $result = [];

        if ($customKey != null) {
            $result = [];
            foreach ($this->options['files'] as $key => $value) {
                $attribute = $this->getFileAttribute($value, $customKey);
                $result[] = $attribute ? $attribute : $value['file'];
            }
        } else {
            $result = $this->options['files'];
        }

        return $result;
    }

    /**
     * getUploadedFiles method
     * Get a list with all uploaded files
     *
     * @public
     * @return {Array}
     */
    public function getUploadedFiles()
    {
        $result = [];

        foreach ($this->getFileList() as $key => $item) {
            if (isset($item['uploaded'])) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * getPreloadedFiles method
     * Get a list with all preloaded files
     *
     * @public
     * @return {Array}
     */
    public function getPreloadedFiles()
    {
        $result = [];

        foreach ($this->getFileList() as $key => $item) {
            if (!isset($item['uploaded'])) {
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * getRemovedFiles method
     * Get removed files as array
     *
     * @public
     * @param $customKey  {String} The file attribute which is also defined in listInput element
     * @return {Array}
     */
    public function getRemovedFiles($customKey = 'file')
    {
        $removedFiles = [];

        if (is_array($this->field['listInput']['list']) && is_array($this->options['files'])) {
            foreach ($this->options['files'] as $key => $value) {
                if (!in_array($this->getFileAttribute($value, $customKey),
                        $this->field['listInput']['list']) && (!isset($value['uploaded']) || !$value['uploaded'])) {
                    $removedFiles[] = $value;
                    unset($this->options['files'][$key]);
                }
            }
        }

        if (is_array($this->options['files'])) {
            $this->options['files'] = array_values($this->options['files']);
        }

        return $removedFiles;
    }

    /**
     * getListInput method
     * Get the listInput value as null or array
     *
     * @public
     * @return {null, Array}
     */
    public function getListInput()
    {
        return $this->field['listInput'];
    }

    /**
     * getFileAttribute method
     * Get the file attribute
     *
     * @private
     * @param $item  {Array} Item
     * @return
     */
    private function getFileAttribute($item, $attribute)
    {
        $result = null;

        if (isset($item['data'][$attribute])) {
            $result = $item['data'][$attribute];
        }
        if (isset($item[$attribute])) {
            $result = $item[$attribute];
        }

        return $result;
    }

    /**
     * readListInput method
     * Get value from the listInput
     *
     * @private
     * @param $name  {String} FileUploader $_FILES name
     * @return {null, Array}
     */
    private function readListInput($name = null)
    {
        $inputName = 'fileuploader-list-'.($name ? $name : $this->field['name']);
        if (is_string($this->options['listInput'])) {
            $inputName = $this->options['listInput'];
        }

        if (isset($_POST[$inputName]) && $this->isJSON($_POST[$inputName])) {
            $list = [
                'list' => [],
                'values' => json_decode($_POST[$inputName], true),
            ];

            foreach ($list['values'] as $key => $value) {
                $list['list'][] = $value['file'];
            }

            return $list;
        }

        return null;
    }

    /**
     * validation method
     * Check ini settings, field and files
     *
     * @private
     * @param $item  {Array} Item
     * @return {boolean, String}
     */
    private function validate($item = null)
    {
        if ($item == null) {
            // check ini settings and some generally options
            $ini = [
                (boolean) ini_get('file_uploads'),
                (int) ini_get('upload_max_filesize'),
                (int) ini_get('post_max_size'),
                (int) ini_get('max_file_uploads'),
                (int) ini_get('memory_limit'),
            ];

            if (!$ini[0]) {
                return $this->codeToMessage('file_uploads');
            }
            if ($this->options['required'] && strtolower($_SERVER['REQUEST_METHOD']) == "post" && $this->field['count'] + count($this->options['files']) == 0) {
                return $this->codeToMessage('required_and_no_file');
            }
            if (($this->options['limit'] && $this->field['count'] + count($this->options['files']) > $this->options['limit']) || ($ini[3] != 0 && ($this->field['count']) > $ini[3])) {
                return $this->codeToMessage('max_number_of_files');
            }
            if (!file_exists($this->options['uploadDir']) || !is_writable($this->options['uploadDir'])) {
                return $this->codeToMessage('invalid_folder_path');
            }

            $total_size = 0;
            foreach ($this->field['input']['size'] as $key => $value) {
                $total_size += $value;
            }
            $total_size = $total_size / 1000000;
            if ($ini[2] != 0 && $total_size > $ini[2]) {
                return $this->codeToMessage('post_max_size');
            }
            if ($this->options['maxSize'] && $total_size > $this->options['maxSize']) {
                return $this->codeToMessage('max_files_size');
            }
        } else {
            // check file
            if ($item['error'] > 0) {
                return $this->codeToMessage($item['error'], $item);
            }
            if (is_array($this->options['disallowedExtensions']) && (in_array(strtolower($item['extension']),
                        $this->options['disallowedExtensions']) || preg_grep('/('.$item['format'].'\/\*|'.preg_quote($item['type'],
                            '/').')/', $this->options['disallowedExtensions']))) {
                return $this->codeToMessage('accepted_file_types', $item);
            }
            if (is_array($this->options['extensions']) && !in_array(strtolower($item['extension']),
                    $this->options['extensions']) && !preg_grep('/('.$item['format'].'\/\*|'.preg_quote($item['type'],
                        '/').')/', $this->options['extensions'])) {
                return $this->codeToMessage('accepted_file_types', $item);
            }
            if ($this->options['fileMaxSize'] && $item['size'] / 1000000 > $this->options['fileMaxSize']) {
                return $this->codeToMessage('max_file_size', $item);
            }
            if ($this->options['maxSize'] && $item['size'] / 1000000 > $this->options['maxSize']) {
                return $this->codeToMessage('max_file_size', $item);
            }
            $custom_validation = is_callable($this->options['validate_file']) ? $this->options['validate_file']($item,
                $this->options) : true;
            if ($custom_validation != true) {
                return $custom_validation;
            }
        }

        return true;
    }

    /**
     * resize method
     * Resize, crop and rotate images
     *
     * @public
     * @static
     * @param $filename  {String} file source
     * @param $width  {Number} new width
     * @param $height  {Number} new height
     * @param $destination  {String} file destination
     * @param $crop  {boolean, Array} crop property
     * @param $quality  {Number} quality of destination
     * @param $rotation  {Number} rotation degrees
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

    /**
     * uploadFiles method
     * Process and upload the files
     *
     * @private
     * @return {null, Array}
     */
    private function uploadFiles()
    {
        $data = [
            "hasWarnings" => false,
            "isSuccess" => false,
            "warnings" => [],
            "files" => [],
        ];
        $listInput = $this->field['listInput'];
        $uploadDir = str_replace(getcwd().'/', '', $this->options['uploadDir']);
        $chunk = isset($_POST['_chunkedd']) && count($this->field['input']['name']) == 1 ? json_decode($_POST['_chunkedd'],
            true) : false;

        if ($this->field['input']) {
            // validate ini settings and some generally options
            $validate = $this->validate();
            $data['isSuccess'] = true;

            if ($validate === true) {
                // process the files
                $count = count($this->field['input']['name']);
                for ($i = 0; $i < $count; $i++) {
                    $file = [
                        'name' => $this->field['input']['name'][$i],
                        'tmp_name' => $this->field['input']['tmp_name'][$i],
                        'type' => $this->field['input']['type'][$i],
                        'error' => $this->field['input']['error'][$i],
                        'size' => $this->field['input']['size'][$i],
                    ];

                    // chunk
                    if ($chunk) {
                        if (isset($chunk['isFirst'])) {
                            $chunk['temp_name'] = $this->random_string(6).time();
                        }

                        $tmp_name = $uploadDir.'.unconfirmed_'.self::filterFilename($chunk['temp_name']);
                        if (!isset($chunk['isFirst']) && !file_exists($tmp_name)) {
                            continue;
                        }
                        $sp = fopen($file['tmp_name'], 'rb');
                        $op = fopen($tmp_name, isset($chunk['isFirst']) ? 'wb' : 'ab');
                        while (!feof($sp)) {
                            $buffer = fread($sp, 512);
                            fwrite($op, $buffer);
                        }

                        // close handles
                        fclose($op);
                        fclose($sp);

                        if (isset($chunk['isLast'])) {
                            $file['tmp_name'] = $tmp_name;
                            $file['name'] = $chunk['name'];
                            $file['type'] = $chunk['type'];
                            $file['size'] = $chunk['size'];
                        } else {
                            echo json_encode([
                                'fileuploader' => [
                                    'temp_name' => $chunk['temp_name'],
                                ],
                            ]);
                            exit;
                        }
                    }

                    $metas = [];
                    $metas['tmp_name'] = $file['tmp_name'];
                    $metas['extension'] = strtolower(substr(strrchr($file['name'], "."), 1));
                    $metas['type'] = $file['type'];
                    $metas['format'] = strtok($file['type'], '/');
                    $metas['name'] = $metas['old_name'] = $file['name'];
                    $metas['title'] = $metas['old_title'] = substr($metas['old_name'], 0,
                        (strlen($metas['extension']) > 0 ? -(strlen($metas['extension']) + 1) : strlen($metas['old_name'])));
                    $metas['size'] = $file['size'];
                    $metas['size2'] = $this->formatSize($file['size']);
                    $metas['date'] = date('r');
                    $metas['error'] = $file['error'];
                    $metas['chunked'] = $chunk;

                    // validate file
                    $validateFile = $this->validate(array_diff_key($metas, array_flip(['tmp_name', 'chunked'])));

                    // check if file is in listInput
                    $listInputName = '0:/'.$metas['old_name'];
                    $fileInList = $listInput === null || in_array($listInputName, $listInput['list']);

                    // add file to memory
                    if ($validateFile === true) {
                        if ($fileInList) {
                            $fileListIndex = 0;

                            if ($listInput) {
                                $fileListIndex = array_search($listInputName, $listInput['list']);
                                $metas['listProps'] = $listInput['values'][$fileListIndex];
                                unset($listInput['list'][$fileListIndex]);
                                unset($listInput['values'][$fileListIndex]);
                            }

                            $metas['name'] = $this->generateFileName($this->options['title'],
                                array_diff_key($metas, array_flip(['tmp_name', 'error', 'chunked'])));
                            $metas['title'] = substr($metas['name'], 0,
                                (strlen($metas['extension']) > 0 ? -(strlen($metas['extension']) + 1) : strlen($metas['name'])));
                            $metas['file'] = $uploadDir.$metas['name'];
                            $metas['replaced'] = file_exists($metas['file']);

                            ksort($metas);
                            $data['files'][] = $metas;
                        }
                    } else {
                        if ($metas['chunked'] && file_exists($metas['tmp_name'])) {
                            unlink($metas['tmp_name']);
                        }
                        if (!$fileInList) {
                            continue;
                        }

                        $data['isSuccess'] = false;
                        $data['hasWarnings'] = true;
                        $data['warnings'][] = $validateFile;
                        $data['files'] = [];

                        break;
                    }
                }

                // upload the files
                if (!$data['hasWarnings']) {
                    foreach ($data['files'] as $key => $file) {
                        if ($file['chunked'] ? rename($file['tmp_name'],
                            $file['file']) : $this->options['move_uploaded_file']($file['tmp_name'], $file['file'],
                            $file)) {
                            unset($data['files'][$key]['chunked']);
                            unset($data['files'][$key]['error']);
                            unset($data['files'][$key]['tmp_name']);
                            $data['files'][$key]['uploaded'] = true;

                            $this->options['files'][] = $data['files'][$key];
                        } else {
                            unset($data['files'][$key]);
                        }
                    }
                }
            } else {
                $data['isSuccess'] = false;
                $data['hasWarnings'] = true;
                $data['warnings'][] = $validate;
            }
        } else {
            $lastPHPError = error_get_last();
            if ($lastPHPError && $lastPHPError['type'] == E_WARNING && $lastPHPError['line'] == 0) {
                $errorMessage = null;

                if (strpos($lastPHPError['message'], "POST Content-Length") !== false) {
                    $errorMessage = $this->codeToMessage(UPLOAD_ERR_INI_SIZE);
                }
                if (strpos($lastPHPError['message'], "Maximum number of allowable file uploads") !== false) {
                    $errorMessage = $this->codeToMessage('max_number_of_files');
                }

                if ($errorMessage != null) {
                    $data['isSuccess'] = false;
                    $data['hasWarnings'] = true;
                    $data['warnings'][] = $errorMessage;
                }
            }

            if ($this->options['required'] && strtolower($_SERVER['REQUEST_METHOD']) == "post") {
                $data['hasWarnings'] = true;
                $data['warnings'][] = $this->codeToMessage('required_and_no_file');
            }
        }

        // add listProp attribute to the files
        if ($listInput) {
            foreach ($this->getFileList() as $key => $item) {
                if (!isset($item['listProps'])) {
                    $fileListIndex = array_search($item['file'], $listInput['list']);

                    if ($fileListIndex !== false) {
                        $this->options['files'][$key]['listProps'] = $listInput['values'][$fileListIndex];
                    }
                }

                if (isset($item['listProps'])) {
                    unset($this->options['files'][$key]['listProps']['file']);

                    if (empty($this->options['files'][$key]['listProps'])) {
                        unset($this->options['files'][$key]['listProps']);
                    }
                }
            }
        }

        $data['files'] = $this->getUploadedFiles();

        // call file editor
        $this->editFiles();

        // call file sorter
        $this->sortFiles();

        $data['files'] = $this->getUploadedFiles();

        return $data;
    }

    /**
     * editFiles method
     * Edit all files that have an editor from Front-End
     *
     * @private
     * @return void
     */
    protected function editFiles()
    {
        if ($this->options['editor'] === false) {
            return;
        }

        foreach ($this->getFileList() as $key => $item) {
            $file = !isset($item['relative_file']) ? $item['file'] : $item['relative_file'];

            // add editor to files
            if (isset($item['listProps']) && isset($item['listProps']['editor'])) {
                $item['editor'] = $item['listProps']['editor'];
            }
            if (isset($item['uploaded']) && isset($_POST['_editorr']) && $this->isJSON($_POST['_editorr']) && count($this->field['input']['name']) == 1) {
                $item['editor'] = json_decode($_POST['_editorr'], true);
            }

            // edit file
            if (($this->options['editor'] != null || isset($item['editor']) && file_exists($file) && strpos($item['type'],
                    'image/') === 0)) {
                $width = isset($this->options['editor']['maxWidth']) ? $this->options['editor']['maxWidth'] : null;
                $height = isset($this->options['editor']['maxHeight']) ? $this->options['editor']['maxHeight'] : null;
                $quality = isset($this->options['editor']['quality']) ? $this->options['editor']['quality'] : 90;
                $rotation = isset($item['editor']['rotation']) ? $item['editor']['rotation'] : 0;
                $crop = isset($this->options['editor']['crop']) ? $this->options['editor']['crop'] : false;
                $crop = isset($item['editor']['crop']) ? $item['editor']['crop'] : $crop;

                // edit
                $this->options['files'][$key]['editor'] = self::resize($file, $width, $height, null, $crop, $quality,
                    $rotation);
                $this->options['files'][$key]['size'] = filesize($file);
                if (isset($this->options['files'][$key]['size2'])) {
                    $this->options['files'][$key]['size2'] = $this->formatSize($this->options['files'][$key]['size']);
                }
            }
        }
    }

    /**
     * sortFiles method
     * Sort all files that have an index from Front-End
     *
     * @private
     * @return void
     */
    private function sortFiles()
    {
        foreach ($this->options['files'] as $key => $item) {
            if (isset($item['listProps']) && isset($item['listProps']['index'])) {
                $this->options['files'][$key]['index'] = $item['listProps']['index'];
            }
        }

        $freeIndex = count($this->options['files']);
        if (isset($this->options['files'][0]['index'])) {
            usort($this->options['files'], function ($a, $b) {
                global $freeIndex;

                if (!isset($a['index'])) {
                    $a['index'] = $freeIndex;
                    $freeIndex++;
                }

                if (!isset($b['index'])) {
                    $b['index'] = $freeIndex;
                    $freeIndex++;
                }

                return $a['index'] - $b['index'];
            });
        }
    }

    /**
     * generateFileName method
     * Generated a new file name
     *
     * @private
     * @param $conf  {null, String, Array} FileUploader title option
     * @param $item  {Array} Item
     * @param $skip_replace_check  {boolean} Used only for recursive auto generating file name to exclude replacements
     * @return {String}
     */
    private function generateFilename($conf, $item, $skip_replace_check = false)
    {
        if (is_callable($conf)) {
            $conf = $conf($item);
        }

        $conf = !is_array($conf) ? [$conf] : $conf;
        $type = $conf[0];
        $length = isset($conf[1]) ? max(1, (int) $conf[1]) : 12;
        $forceExtension = isset($conf[2]) && $conf[2] == true;
        $random_string = $this->random_string($length);
        $extension = !empty($item['extension']) ? '.'.$item['extension'] : '';
        $string = '';

        switch ($type) {
            case null:
            case "auto":
                $string = $random_string;
                break;
            case "name":
                $string = $item['title'];
                break;
            default:
                $string = $type;
                $string_extension = substr(strrchr($string, "."), 1);

                $string = str_replace("{random}", $random_string, $string);
                $string = str_replace("{file_name}", $item['title'], $string);
                $string = str_replace("{file_size}", $item['size'], $string);
                $string = str_replace("{timestamp}", time(), $string);
                $string = str_replace("{date}", date('Y-n-d_H-i-s'), $string);
                $string = str_replace("{extension}", $item['extension'], $string);
                $string = str_replace("{format}", $item['format'], $string);
                $string = str_replace("{index}", isset($item['listProps']['index']) ? $item['listProps']['index'] : 0,
                    $string);

                if ($forceExtension && !empty($string_extension)) {
                    if ($string_extension != "{extension}") {
                        $type = substr($string, 0, -(strlen($string_extension) + 1));
                        $extension = $item['extension'] = $string_extension;
                    } else {
                        $type = substr($string, 0, -(strlen($item['extension']) + 1));
                        $extension = '';
                    }
                }
        }

        if ($extension && !preg_match('/'.$extension.'$/', $string)) {
            $string .= $extension;
        }

        // generate another filename if a file with the same name already exists
        // only when replace options is true
        if (!$this->options['replace'] && !$skip_replace_check) {
            $title = $item['title'];
            $i = 1;

            while (file_exists($this->options['uploadDir'].$string)) {
                $item['title'] = $title." ({$i})";
                $conf[0] = $type == "auto" || $type == "name" || strpos($string,
                    "{random}") !== false ? $type : $type." ({$i})";
                $string = $this->generateFileName($conf, $item, true);
                $i++;
            }
        }

        return self::filterFilename($string);
    }

    /**
     * generateInput method
     * Generate a string with HTML input
     *
     * @public
     * @return {String}
     */
    public function generateInput()
    {
        $attributes = [];

        // process options
        foreach (array_merge(['name' => $this->field['name']], $this->options) as $key => $value) {
            if ($value) {
                switch ($key) {
                    case 'limit':
                    case 'maxSize':
                    case 'fileMaxSize':
                        $attributes['data-fileuploader-'.$key] = $value;
                        break;
                    case 'listInput':
                        $attributes['data-fileuploader-'.$key] = is_bool($value) ? var_export($value, true) : $value;
                        break;
                    case 'extensions':
                        $attributes['data-fileuploader-'.$key] = implode(',', $value);
                        break;
                    case 'name':
                        $attributes[$key] = $value;
                        break;
                    case 'required':
                        $attributes[$key] = '';
                        break;
                    case 'files':
                        $value = array_values($value);
                        $attributes['data-fileuploader-'.$key] = json_encode($value);
                        break;
                }
            }
        }

        // generate input attributes
        $dataAttributes = array_map(function ($value, $key) {
            return $key."='".(str_replace("'", '"', $value))."'";
        }, array_values($attributes), array_keys($attributes));

        return '<input type="file"'.implode(' ', $dataAttributes).'>';
    }

    /**
     * clean_chunked_files method
     * Remove chunked files from directory
     *
     * @public
     * @static
     * @param $directory  {String} Directory scan
     * @param $time  {String} Time difference
     * @return {String}
     */
    public static function clean_chunked_files($directory, $time = '-1 hour')
    {
        if (!is_dir($directory)) {
            return;
        }

        $dir = scandir($directory);
        $files = array_diff($dir, ['.', '..']);
        foreach ($files as $key => $name) {
            $file = $directory.$name;
            if (strpos($name, '.unconfirmed_') === 0 && filemtime($file) < strtotime($time)) {
                unlink($file);
            }
        }
    }

    /**
     * codeToMessage method
     * Translate a warning code into text
     *
     * @private
     * @param $code  {Number, String}
     * @param $file  {null, Array}
     * @return {String}
     */
    private function codeToMessage($code, $file = null)
    {
        $message = null;

        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;
            case 'accepted_file_types':
                $message = "File type is not allowed for ".$file['old_name'];
                break;
            case 'file_uploads':
                $message = "File uploading option in disabled in php.ini";
                break;
            case 'max_file_size':
                $message = $file['old_name']." is too large";
                break;
            case 'max_files_size':
                $message = "Files are too big";
                break;
            case 'max_number_of_files':
                $message = "Maximum number of files is exceeded";
                break;
            case 'required_and_no_file':
                $message = "No file was choosed. Please select one";
                break;
            case 'invalid_folder_path':
                $message = "Upload folder doesn't exist or is not writable";
                break;
            default:
                $message = "Unknown upload error";
                break;
        }

        return $message;
    }

    /**
     * formatSize method
     * Cover bytes to readable file size format
     *
     * @private
     * @param $bytes  {Number}
     * @return {Number}
     */
    private function formatSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2).' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2).' MB';
        } elseif ($bytes > 0) {
            $bytes = number_format($bytes / 1024, 2).' KB';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }

    /**
     * isJson method
     * Check if string is a valid json
     *
     * @private
     * @param $string  {String}
     * @return {boolean}
     */
    private function isJson($string)
    {
        json_decode($string);

        return (json_last_error() == JSON_ERROR_NONE);
    }

    /**
     * random_string method
     * Generate a random string
     *
     * @public
     * @param $length  {Number} Number of characters
     * @return {String}
     */
    private function random_string($length = 12)
    {
        return substr(str_shuffle("_0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }

    /**
     * filterFilename method
     * Remove invalid characters from filename
     *
     * @public
     * @static
     * @param $filename  {String}
     * @return {String}
     */
    public static function filterFilename($filename)
    {
        $delimiter = '_';
        $invalidCharacters = array_merge(array_map('chr', range(0, 31)),
            ["<", ">", ":", '"', "/", "\\", "|", "?", "*"]);

        $filename = str_replace($invalidCharacters, $delimiter, $filename);
        $filename = preg_replace('/('.preg_quote($delimiter, '/').'){2,}/', '$1', $filename);

        return $filename;
    }

    /**
     * mime_content_type method
     * Get the mime_content_type of a file
     *
     * @public
     * @static
     * @param $file  {String} File location
     * @param $nativeFunction  {Boolean} Use file name to lookup
     * @return {String}
     */
    public static function mime_content_type($file, $nativeFunction = false)
    {
        if (function_exists('mime_content_type') && $nativeFunction) {
            return mime_content_type($file);
        } else {
            $mime_types = [
                'txt' => 'text/plain',
                'htm' => 'text/html',
                'html' => 'text/html',
                'php' => 'text/html',
                'css' => 'text/css',
                'js' => 'application/javascript',
                'json' => 'application/json',
                'xml' => 'application/xml',
                'swf' => 'application/x-shockwave-flash',
                'flv' => 'video/x-flv',

                // images
                'png' => 'image/png',
                'jpe' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'bmp' => 'image/bmp',
                'ico' => 'image/vnd.microsoft.icon',
                'tiff' => 'image/tiff',
                'tif' => 'image/tiff',
                'svg' => 'image/svg+xml',
                'svgz' => 'image/svg+xml',

                // archives
                'zip' => 'application/zip',
                'rar' => 'application/x-rar-compressed',
                'exe' => 'application/x-msdownload',
                'msi' => 'application/x-msdownload',
                'cab' => 'application/vnd.ms-cab-compressed',

                // audio/video
                'mp3' => 'audio/mpeg',
                'mp4' => 'video/mp4',
                'webM' => 'video/webm',
                'qt' => 'video/quicktime',
                'mov' => 'video/quicktime',

                // adobe
                'pdf' => 'application/pdf',
                'psd' => 'image/vnd.adobe.photoshop',
                'ai' => 'application/postscript',
                'eps' => 'application/postscript',
                'ps' => 'application/postscript',

                // ms office
                'doc' => 'application/msword',
                'rtf' => 'application/rtf',
                'xls' => 'application/vnd.ms-excel',
                'ppt' => 'application/vnd.ms-powerpoint',

                // open office
                'odt' => 'application/vnd.oasis.opendocument.text',
                'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
            ];
            $ext = strtolower(substr(strrchr($file, "."), 1));

            if (array_key_exists($ext, $mime_types)) {
                return $mime_types[$ext];
            } elseif (function_exists('finfo_open') && is_file($file)) {
                $finfo = finfo_open(FILEINFO_MIME);
                $mimetype = finfo_file($finfo, $file);
                finfo_close($finfo);

                return $mimetype;
            } else {
                return 'application/octet-stream';
            }
        }
    }
}
