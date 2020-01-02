<?php

declare(strict_types=1);

namespace Laravolt\Camunda\Controllers;

use Illuminate\Routing\Controller;

class TableController extends Controller
{
    public function index()
    {
        $table = request('namespace');
        if (class_exists($table)) {
            $table = new $table(null);
        }

        return $table->view('camunda::table.index');
    }
}
