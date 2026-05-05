<?php

namespace Hexpet\Code\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Hexlet\Code\UrlNormalizer;

class NormalizerTest extends TestCase
{
    private const HOST = 'example.com';

    public static function urlProvider(): array
    {
        $schemes = ['http', 'https'];
        $port = '8080';
        $suffix = '/path?q=1#fragment';

        $data = [];
        foreach ($schemes as $scheme) {
            $baseUrl = sprintf("%s://%s", $scheme, self::HOST);
            $data["{$scheme} already normalized"] = [$baseUrl, $baseUrl];
            $data["{$scheme} with port"] = ["{$baseUrl}:{$port}", "{$baseUrl}:{$port}"];
            $data["{$scheme} with suffix"] = ["{$baseUrl}{$suffix}", $baseUrl];
        }
        return $data;
    }

    #[DataProvider('urlProvider')]
    public function testUrlNormalization(string $url, string $expected): void
    {
        $normalizedUrl = UrlNormalizer::normalize($url);
        $this->assertSame($expected, $normalizedUrl);
    }
}
