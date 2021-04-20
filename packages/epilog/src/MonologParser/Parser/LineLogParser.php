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
 * Class LineLogParser.
 */
class LineLogParser implements LogParserInterface
{
    protected $pattern = '/\[(?P<date>.*)\] (?P<logger>\w+).(?P<level>\w+): (?P<message>.*[^ ]+) (?P<context>[^ ]+) (?P<extra>[^ ]+)/';

    /**
     * Constructor.
     *
     * @param string $pattern
     */
    public function __construct($pattern = null)
    {
        $this->pattern = ($pattern) ?: $this->pattern;
    }

    /**
     * {@inheritdoc}
     */
    public function parse($log)
    {
        if (!is_string($log) || strlen($log) === 0) {
            return [];
        }

        preg_match($this->pattern, $log, $data);

        if (!isset($data['date'])) {
            return [];
        }

        return [
            'date' => \DateTime::createFromFormat('Y-m-d H:i:s', $data['date']),
            'logger' => $data['logger'],
            'level' => strtolower($data['level']),
            'message' => $data['message'],
            'raw' => $log,
            'context' => json_decode($data['context'], true),
            'extra' => json_decode($data['extra'], true),
        ];
    }
}
