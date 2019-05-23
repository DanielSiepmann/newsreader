<?php

namespace DanielSiepmann\Newsreader\Domain\Reader;

/*
 * Copyright (C) 2019 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

use DanielSiepmann\Newsreader\Domain\Collection\News as NewsCollection;
use DanielSiepmann\Newsreader\Domain\News;
use DanielSiepmann\Newsreader\Domain\Xml\XsdProvider;

/**
 *
 */
class Rss implements Reader
{
    /**
     * @throws \InvalidArgumentException If $input is invalid.
     */
    public function getEntries($input): NewsCollection
    {
        $this->validate($input);

        return new NewsCollection(... $this->createNewsEntries($input));
    }

    private function createNewsEntries(string $input)
    {
        $entries = [];

        $xml = new \DOMDocument();
        $xml->loadXML($input);

        foreach ($xml->getElementsByTagName('item') as $item) {
            try {
                $entries[] = $this->createNewsFromItem($item);
            } catch (\InvalidArgumentException $e) {
            }
        }

        return $entries;
    }

    private function createNewsFromItem(\DomNode $item): News
    {
        $information = [];

        foreach ($item->childNodes as $node) {
            $information[$node->nodeName] = $node->textContent;
        }

        if ($information === []) {
            throw new \InvalidArgumentException('Item did not provide any information.', 1558608553);
        }

        $information['pubDate'] = new \DateTimeImmutable($information['pubDate']);

        return new News(
            $information['title'] ?? '',
            $information['link'] ?? '',
            $information['content'] ?? '',
            $information['pubDate'],
            $information['description'] ?? ''
        );
    }

    /**
     * @throws \InvalidArgumentException If $input is invalid.
     */
    private function validate($input)
    {
        $this->validateType($input);
        $this->validateRssFeed($input);
    }

    /**
     * @throws \InvalidArgumentException
     */
    private function validateType($input)
    {
        if (is_string($input) === false) {
            throw new \InvalidArgumentException('Given input was not string.', 1558601653);
        }
        if (trim($input) === '') {
            throw new \InvalidArgumentException('Given input did not contain XML.', 1558601653);
        }
    }

    private function validateRssFeed(string $input)
    {
        $xsdProvider = new XsdProvider();

        $xml = new \DOMDocument();
        try {
            $xml->loadXML($input);
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Given input did not contain valid XML.', 1558601653);
        }

        libxml_use_internal_errors(true);
        if ($xml->schemaValidate($xsdProvider->get('rss', '2.0')) === false) {
            throw new \InvalidArgumentException(
                'Given input did not contain valid RSS:' . PHP_EOL . $this->getXmlErrors(),
                1558601653
            );
        }
    }

    private function getXmlErrors(): string
    {
        $errors = [];

        foreach (libxml_get_errors() as $error) {
            $errors[] = 'Line: ' . $error->line . ' ' . $error->message;
        }

        return implode(PHP_EOL, $errors);
    }
}
