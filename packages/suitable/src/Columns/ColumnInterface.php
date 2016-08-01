<?php
namespace Laravolt\Suitable\Columns;

interface ColumnInterface
{
    public function header();

    public function cell($cell);
}
