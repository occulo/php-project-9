<?php

namespace Hexlet\Code;

class UrlNormalizer
{
    public static function normalize(string $url): string
    {
        $parsedUrl = parse_url(mb_strtolower($url));
        if (!isset($parsedUrl['scheme'], $parsedUrl['host'])) {
            throw new \Exception("URL is invalid");
        }
        $normalizedUrl = sprintf("%s://%s", $parsedUrl['scheme'], $parsedUrl['host']);
        if (isset($parsedUrl['port'])) {
            return "{$normalizedUrl}:{$parsedUrl['port']}";
        }
        return $normalizedUrl;
    }
}
