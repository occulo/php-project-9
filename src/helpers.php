<?php

function truncate(?string $str, int $length = 200): string
{
    $str = $str ?? '';
    if (mb_strlen($str) <= $length) {
        return $str;
    }
    return sprintf("%s...", mb_substr($str, 0, $length));
}
