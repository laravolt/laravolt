<?php

/*
 * This file is part of the monolog-parser package.
 *
 * (c) Robert Gruendler <r.gruendler@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Laravolt\Epilog\MonologParser\Parser;

/**
 * Interface LogParserInterface.
 */
interface LogParserInterface
{
    /**
     * @param string $log
     */
    public function parse($log);
}
