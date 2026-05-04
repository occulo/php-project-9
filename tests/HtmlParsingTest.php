<?php

namespace Hexpet\Code\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class HtmlParsingTest extends TestCase
{
    private const FIXTURES_DIR = __DIR__ . '/fixtures';

    public function testHtmlParsing(): void
    {
        $filepath = self::FIXTURES_DIR . '/page.html';
        $html = file_get_contents($filepath);
        $this->assertNotFalse($html);

        $crawler = new Crawler($html);
        $title = $crawler->filter('title')->text();
        $desc = $crawler->filter('meta[name="description"]')->attr('content');
        $h1 = $crawler->filter('h1')->text();

        $this->assertSame('Document', $title);
        $this->assertSame('desc', $desc);
        $this->assertSame('Hello World', $h1);
    }
}
