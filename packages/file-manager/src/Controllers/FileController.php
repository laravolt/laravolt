<?php

namespace Laravolt\FileManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;

class FileController extends Controller
{
    public function show(Request $request)
    {
        $hash = $request->get(config('laravolt.file-manager.query_string'));
        $file = app('laravolt.file-manager')->openFile($hash)->getFilePath();

        return response()->download($file);
    }

    public function destroy($key)
    {
        $file = app('laravolt.file-manager')->openFile($key)->getFilePath();
        if (file_exists($file)) {
            $filename = pathinfo($file)['basename'] ?? $file;
            File::delete($file);

            return redirect()->back()->withSuccess(sprintf('File %s berhasil dihapus', $filename));
        }

        return redirect()->back()->withError('File tidak ditemukan');
    }
}
