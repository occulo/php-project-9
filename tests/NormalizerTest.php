<?php

namespace Hexpet\Code\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class NormalizerTest extends TestCase
{
    private const HOST = "example.com";

    public static function urlProvider(): array
    {
        $schemes = ['http', 'https'];
        $path = '/path';
        $query = '?q=1';

        $data = [];
        foreach ($schemes as $scheme) {
            $baseUrl = sprintf("%s://%s", $scheme, self::HOST);
            $data["{$scheme} already normalized"] = [$baseUrl, $baseUrl];
            $data["{$scheme} with path"] = ["{$baseUrl}{$path}", $baseUrl];
            $data["{$scheme} with query"] = ["{$baseUrl}{$query}", $baseUrl];
            $data["{$scheme} with path and query"] = ["{$baseUrl}{$path}{$query}", $baseUrl];
        }
        return $data;
    }

    #[DataProvider('urlProvider')]
    public function testUrlNormalization(string $url, string $expected): void
    {
        $parsedUrl = parse_url($url);
        $normalizedUrl = sprintf("%s://%s", $parsedUrl['scheme'], $parsedUrl['host']);
        $this->assertSame($expected, $normalizedUrl);
    }
}
