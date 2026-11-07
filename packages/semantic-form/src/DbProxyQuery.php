<?php

declare(strict_types=1);

namespace Laravolt\SemanticForm;

use InvalidArgumentException;

/**
 * Turns a dropdownDB() query template into SQL with bound parameters.
 *
 * Templates are written for sprintf(): the search term goes where %s (or
 * %1$s) is, and %% stands for a literal percent sign. A placeholder inside a
 * quoted literal makes the whole literal one parameter, so
 * "where name like '%%%s%%'" with "ab" becomes "where name like ?" bound to
 * "%ab%". A bare placeholder becomes "?" bound to the term. The term is never
 * part of the SQL text.
 */
final class DbProxyQuery
{
    private const PLACEHOLDER = "\0";

    /**
     * @return array{0: string, 1: list<string>}
     */
    public static function bind(string $template, string $term): array
    {
        $sql = '';
        $bindings = [];
        $quote = null;
        $raw = '';
        $value = '';
        $bound = false;

        $chars = self::decode($template);
        $count = count($chars);

        for ($i = 0; $i < $count; $i++) {
            $char = $chars[$i];

            if ($quote === null) {
                if ($char === self::PLACEHOLDER) {
                    $sql .= '?';
                    $bindings[] = $term;
                } elseif ($char === "'" || $char === '"') {
                    $quote = $char;
                    $raw = $char;
                    $value = '';
                    $bound = false;
                } else {
                    $sql .= $char;
                }

                continue;
            }

            if ($char === self::PLACEHOLDER) {
                $value .= $term;
                $bound = true;

                continue;
            }

            if ($char === $quote) {
                if (($chars[$i + 1] ?? null) === $quote) {
                    // A doubled quote is an escaped quote inside the literal.
                    $raw .= $char.$char;
                    $value .= $char;
                    $i++;

                    continue;
                }

                if ($bound) {
                    $sql .= '?';
                    $bindings[] = $value;
                } else {
                    $sql .= $raw.$char;
                }

                $quote = null;

                continue;
            }

            $raw .= $char;
            $value .= $char;
        }

        if ($quote !== null) {
            throw new InvalidArgumentException('Unterminated string literal in dropdown query.');
        }

        return [$sql, $bindings];
    }

    /**
     * Applies sprintf's escapes: %% becomes %, and %s or %1$s becomes a placeholder.
     *
     * @return list<string>
     */
    private static function decode(string $template): array
    {
        $chars = [];
        $length = strlen($template);

        for ($i = 0; $i < $length; $i++) {
            $char = $template[$i];

            if ($char !== '%') {
                $chars[] = $char;

                continue;
            }

            if (substr($template, $i, 2) === '%%') {
                $chars[] = '%';
                $i++;
            } elseif (substr($template, $i, 2) === '%s') {
                $chars[] = self::PLACEHOLDER;
                $i++;
            } elseif (substr($template, $i, 4) === '%1$s') {
                $chars[] = self::PLACEHOLDER;
                $i += 3;
            } else {
                throw new InvalidArgumentException('Dropdown queries only support the %s and %1$s placeholders.');
            }
        }

        return $chars;
    }
}
