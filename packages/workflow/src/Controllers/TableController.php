<?php

declare(strict_types=1);

namespace Laravolt\Workflow\Controllers;

use Illuminate\Routing\Controller;

class TableController extends Controller
{
    public function index()
    {
        $table = request('namespace');
        if (class_exists($table)) {
            $table = new $table(null);
        }

        return $table->view('workflow::table.index');
    }
}
