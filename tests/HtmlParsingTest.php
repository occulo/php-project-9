<?php

namespace Hexpet\Code\Tests;

use PHPUnit\Framework\TestCase;
use Hexlet\Code\HtmlParser;

class HtmlParsingTest extends TestCase
{
    private const FIXTURES_DIR = __DIR__ . '/fixtures';
    private string $html;
    private string $emptyHtml;

    public function setUp(): void
    {
        $filepath = self::FIXTURES_DIR . '/page.html';
        $html = file_get_contents($filepath);

        $this->assertNotFalse($html);

        $this->html = $html;
        $this->emptyHtml = '<html><body></body></html>';
    }

    public function testGetElement(): void
    {
        $htmlParser = new HtmlParser($this->html);
        $h1 = $htmlParser->getElement('h1');
        $title = $htmlParser->getElement('title');

        $this->assertSame('Hello World', $h1);
        $this->assertSame('Document', $title);
    }

    public function testGetElementReturnsNullWhenMissing(): void
    {
        $htmlParser = new HtmlParser($this->emptyHtml);
        $title = $htmlParser->getElement('title');

        $this->assertNull($title);
    }

    public function testGetMetaByName(): void
    {
        $htmlParser = new HtmlParser($this->html);
        $description = $htmlParser->getMetaByName('description');

        $this->assertSame('desc', $description);
    }

    public function testGetMetaByNameReturnsNullWhenMissing(): void
    {
        $htmlParser = new HtmlParser($this->emptyHtml);
        $description = $htmlParser->getMetaByName('description');

        $this->assertNull($description);
    }
}
