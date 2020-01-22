<?php

namespace Laravolt\Media\Controllers;

use Illuminate\Routing\Controller;
use Laravolt\Platform\Http\MediaHandler\RedactorMediaHandler;
use Laravolt\Platform\Http\MediaHandler\SingleFileUploaderMediaHandler;
use Laravolt\Platform\Http\MediaHandler\UploaderMediaHandler;

class ApiController extends Controller
{
    public function store()
    {
        switch (request('handler')) {
            case 'single-uploader':
                $handler = new SingleFileUploaderMediaHandler();
                break;
            case 'uploader':
                $handler = new UploaderMediaHandler();
                break;
            case 'redactor':
            default:
                $handler = new RedactorMediaHandler();
                break;
        }

        return $handler();
    }
}
