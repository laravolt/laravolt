<?php

namespace Laravolt\Suitable\Columns;

interface ColumnInterface
{
    public function header();

    public function headerAttributes();

    public function cell($cell, $collection, $loop);

    public function cellAttributes($cell);

    public function sortable();

    public function searchable();

    public function isSearchable();
}
