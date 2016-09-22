<?php
namespace Laravolt\Suitable\Columns;

interface ColumnInterface
{
    public function header();

    public function headerAttributes();

    public function cell($cell);
}
