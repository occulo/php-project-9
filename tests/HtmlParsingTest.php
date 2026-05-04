<?php

namespace Hexpet\Code\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class HtmlParsingTest extends TestCase
{
    private const FIXTURES_DIR = __DIR__ . '/fixtures';

    public function testHtmlParsing(): void
    {
        $html = file_get_contents(self::FIXTURES_DIR . '/page.html');

        $crawler = new Crawler($html);
        $title = $crawler->filter('title')->text();
        $desc = $crawler->filter('meta[name="description"]')->attr('content');
        $h1 = $crawler->filter('h1')->text();

        $this->assertSame('Document', $title);
        $this->assertSame('desc', $desc);
        $this->assertSame('Hello World', $h1);
    }
}
