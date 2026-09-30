<?php

/**
 * Split a SQL dump into executable statements.
 *
 * Strips "-- " and "#" line comments, and plain block comments. MySQL
 * executable comments (opened with "!") stay intact, including any semicolon
 * inside them. Semicolons inside quoted strings or identifiers are not splits.
 *
 * install/install.sql is stored with LF. Splitting that file on ";\n" and
 * dropping any chunk that starts with "--" skips the CREATE TABLE that follows
 * each phpMyAdmin comment header.
 *
 * @return string[]
 */
function splitSqlStatements(string $sql): array
{
    $statements = [];
    $current = '';
    $length = strlen($sql);
    $quote = null;

    for ($i = 0; $i < $length; $i++) {
        $char = $sql[$i];
        $next = $i + 1 < $length ? $sql[$i + 1] : '';

        if ($quote !== null) {
            $current .= $char;
            if ($char === '\\' && $quote !== '`') {
                $current .= $next;
                $i++;
            } elseif ($char === $quote) {
                // MySQL escapes a quote by doubling it: '' inside a string.
                if ($next === $quote) {
                    $current .= $next;
                    $i++;
                } else {
                    $quote = null;
                }
            }
            continue;
        }

        if ($char === "'" || $char === '"' || $char === '`') {
            $quote = $char;
            $current .= $char;
            continue;
        }

        if (($char === '-' && $next === '-' && ($i + 2 >= $length || ctype_space($sql[$i + 2]))) || $char === '#') {
            $eol = strpos($sql, "\n", $i);
            $i = $eol === false ? $length : $eol;
            $current .= "\n";
            continue;
        }

        if ($char === '/' && $next === '*') {
            $executable = ($sql[$i + 2] ?? '') === '!';
            $end = strpos($sql, '*/', $i + 2);
            if ($executable) {
                $current .= $end === false ? substr($sql, $i) : substr($sql, $i, $end + 2 - $i);
            } else {
                $current .= ' ';
            }
            $i = $end === false ? $length : $end + 1;
            continue;
        }

        if ($char === ';') {
            $statement = trim($current);
            if ($statement !== '') {
                $statements[] = $statement;
            }
            $current = '';
            continue;
        }

        $current .= $char;
    }

    $statement = trim($current);
    if ($statement !== '') {
        $statements[] = $statement;
    }

    return $statements;
}
