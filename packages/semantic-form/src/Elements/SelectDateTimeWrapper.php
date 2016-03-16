<?php namespace Laravolt\SemanticForm\Elements;

use Carbon\Carbon;

class SelectDateTimeWrapper extends SelectDateWrapper
{
    protected $format = 'Y-m-d H:i:s';
}
