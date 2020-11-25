<?php

namespace Laravolt\FileManager;

use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FileManager
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    private $baseDirectory;

    private $exclude = ['.DS_Store', '.gitignore'];

    private $key;

    private $disk;

    private $encoder;

    private $file;

    /**
     * FileManager constructor.
     */
    public function __construct()
    {
        $this->key = config('laravolt.file-manager.query_string');
    }

    public function openDisk($disk)
    {
        $this->disk = $disk;
        $this->filesystem = Storage::disk($disk);
        $this->baseDirectory = config("filesystems.disks.$disk.root");

        return $this;
    }

    public function openFile($hash)
    {
        [$disk, $file] = explode("::", $this->decode($hash));
        $file = config("filesystems.disks.{$disk}.root") . DIRECTORY_SEPARATOR . $file;

        if (file_exists($file)) {
            $this->file = $file;
        }

        return $this;
    }

    public function getFilePath()
    {
        return $this->file;
    }

    public function render()
    {
        $path = false;
        if ($key = request()->get($this->key)) {
            $path = $this->decode($key);
        }

        $items = $this->allFiles($path);

        $files = collect($items)->sort(function ($a, $b) {
            if ($a['type'] == 'dir') {
                if ($b['type'] == 'dir') {
                    return $a['modified'] < $b['modified'];
                } else {
                    return -1;
                }
            } else {
                if ($b['type'] == 'dir') {
                    return 1;
                } else {
                    return $a['modified'] < $b['modified'];
                }
            }
        });

        $isRoot = ! $path;
        $parentUrl = url()->current();
        $parentPath = dirname($path);

        if ($parentPath !== '.') {
            $parentUrl .= "?{$this->key}=" . $this->encode($parentPath);
        }

        return view('file-manager::index', compact('files', 'isRoot', 'parentUrl'));
    }

    public function getInfo($file)
    {
        $filePath = $this->filePath($file);

        $fileCount = $key = null;
        if (File::isFile($filePath)) {
            $key = $this->encode($this->disk . '::' . $file);
            $sizeInByte = File::size($filePath);
            $fileCount = 1;
            $type = 'file';
            $extension = File::extension($filePath);
            $label = File::name($file) . '.' . $extension;
            $permalink = route('file-manager::file.download',
                [$this->key => $key]);
        } else {
            $key = $this->encode($file);
            $folderInfo = $this->folderInfo($filePath);
            $sizeInByte = $folderInfo[0];
            $fileCount = $folderInfo[1];
            $extension = $type = 'dir';
            $label = File::basename($file);
            $permalink = url()->current() . "?{$this->key}=" . $key;
        }

        return [
            'key' => $key,
            'name' => $label,
            'size' => $sizeInByte,
            'size_for_human' => $this->filesizeForHuman($sizeInByte),
            'class' => $this->getCssClass($extension),
            'type' => $type,
            'permalink' => $permalink,
            'path' => $filePath,
            'modified' => Carbon::createFromTimestamp($this->filesystem->lastModified($file)),
            'modified_formatted' => Carbon::createFromTimestamp($this->filesystem->lastModified($file), auth()->user()->timezone)
                ->isoFormat('LLL'),
            'file_count' => $fileCount,
        ];
    }

    public function filePath($path)
    {
        return $this->baseDirectory . DIRECTORY_SEPARATOR . $path;
    }

    protected function filesizeForHuman($bytes, $decimals = 0)
    {
        $size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . ' ' . @$size[$factor];
    }

    protected function folderInfo($dir)
    {
        $count_size = 0;
        $count = 0;
        $dir_array = scandir($dir);
        foreach ($dir_array as $key => $filename) {
            if ($filename != ".." && $filename != ".") {
                if (is_dir($dir . "/" . $filename)) {
                    $new_foldersize = $this->folderInfo($dir . "/" . $filename);
                    $count_size = $count_size + $new_foldersize[0];
                    $count = $count + $new_foldersize[1];
                } else {
                    if (is_file($dir . "/" . $filename)) {
                        $count_size = $count_size + filesize($dir . "/" . $filename);
                        $count++;
                    }
                }
            }
        }

        return [$count_size, $count];
    }

    protected function getCssClass($extension)
    {
        $types = [
            'dir' => 'icon folder',
            'zip' => 'icon file archive outline',
            'rar' => 'icon file archive outline',
            'txt' => 'icon file text outline',
            'doc' => 'icon file word outline',
            'docx' => 'icon file word outline',
            'xls' => 'icon file excel outline',
            'xlsx' => 'icon file excel outline',
            'pdf' => 'icon file pdf outline',
        ];

        return Arr::get($types, strtolower($extension), 'icon file outline');
    }

    public function encode($path)
    {
        return Crypt::encryptString(bin2hex($path));
    }

    public function decode($hash)
    {
        return hex2bin(Crypt::decryptString($hash));
    }

    public function allFiles(string $path = null)
    {
        $files = $this->filesystem->files($path);
        $directories = $this->filesystem->directories($path);
        $items = [];

        foreach ($directories as $item) {
            $items[] = $this->getInfo($item);
        }

        foreach ($files as $item) {
            if (! in_array(basename($item), $this->exclude)) {
                $items[] = $this->getInfo($item);
            }
        }

        return $items;
    }
}
