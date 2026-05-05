<?php

namespace Hexlet\Code;

use Symfony\Component\DomCrawler\Crawler;

class HtmlParser
{
    private Crawler $crawler;

    public function __construct(string $html)
    {
        $this->crawler = new Crawler($html);
    }

    public function getElement(string $selector): ?string
    {
        $node = $this->crawler->filter($selector);
        if (!$node->count()) {
            return null;
        }
        return $node->text();
    }

    public function getMetaByName(string $name): ?string
    {
        $node = $this->crawler->filter("meta[name=\"{$name}\"]");
        if (!$node->count()) {
            return null;
        }
        return $node->attr('content');
    }
}
