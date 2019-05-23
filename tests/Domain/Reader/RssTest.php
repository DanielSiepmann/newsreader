<?php

namespace DanielSiepmann\Tests\Domain\Reader;

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

use DanielSiepmann\Newsreader\Domain\Collection\Collection;
use DanielSiepmann\Newsreader\Domain\Reader\Rss;
use PHPUnit\Framework\TestCase;

class RssTest extends TestCase
{
    private $subject;

    public function setUp(): void
    {
        $this->subject = new Rss();
    }

    /**
     * @test
     */
    public function nonStringInputThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->getEntries([]);
    }

    /**
     * @test
     */
    public function emptyInputThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->getEntries('');
    }

    /**
     * @test
     */
    public function invalidXmlThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->getEntries($this->getInvalidXml());
    }

    /**
     * @test
     */
    public function invalidRssThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->subject->getEntries($this->getInvalidRss());
    }

    /**
     * @test
     */
    public function validEmptyInputResultsinEmptyNewsCollection()
    {
        $input = implode(PHP_EOL, [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<rss version="2.0">',
                '<channel>',
                    '<title>Official typo3.org news</title>',
                    '<link>https://www.typo3.org/</link>',
                    '<description>test</description>',
                    '<language>en-gb</language>',
                    '<copyright>TYPO3 News</copyright>',
                    '<pubDate>Thu, 23 May 2019 07:09:18 +0200</pubDate>',
                    '<lastBuildDate>Thu, 23 May 2019 07:09:18 +0200</lastBuildDate>',
                    '<item/>',
                '</channel>',
            '</rss>',
        ]);
        $collection = $this->subject->getEntries($input);
        $this->assertInstanceOf(Collection::class, $collection, 'Did not get a collection');
        $this->assertCount(0, $collection, 'Collection was not empty.');
    }

    /**
     * @test
     */
    public function inputWithItemsResultsInCollectionWithNews()
    {
        $input = implode(PHP_EOL, [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<rss version="2.0">',
                '<channel>',
                    '<title>Official typo3.org news</title>',
                    '<link>https://www.typo3.org/</link>',
                    '<description>test</description>',
                    '<language>en-gb</language>',
                    '<copyright>TYPO3 News</copyright>',
                    '<pubDate>Thu, 23 May 2019 07:09:18 +0200</pubDate>',
                    '<lastBuildDate>Thu, 23 May 2019 07:09:18 +0200</lastBuildDate>',
                    '<item>',
                        '<pubDate>Wed, 22 May 2019 11:13:51 +0200</pubDate>',
                        '<title>Join Us at the Fluid 3.0 Workshop in Denmark</title>',
                        '<link>https://typo3.org/article/join-us-at-the-fluid-30-workshop-in-denmark/</link>',
                        '<description>Description of item</description>',
                        '<content><![CDATA[<p><a href="https://www.eventbrite.com/e/fluid-30-workshop-in-denmark-registration-61921868977" target="_blank" class="btn btn-primary">Register for the Workshop</a></p> <p><br /> typo3.org text prep.: Carlos Llanos • Proofreading: Mathias Bolt Lesniak</p>]]></content>',
                    '</item>',
                    '<item>',
                        '<pubDate>Wed, 22 May 2019 11:13:51 +0200</pubDate>',
                        '<title>Join Us at the Fluid 3.0 Workshop in Denmark</title>',
                        '<link>https://typo3.org/article/join-us-at-the-fluid-30-workshop-in-denmark/</link>',
                        '<description>Description of item</description>',
                        // TODO: Allow "content:encoded" within XSD / as valid XML
                        // '<content:encoded><![CDATA[<p><a href="https://www.eventbrite.com/e/fluid-30-workshop-in-denmark-registration-61921868977" target="_blank" class="btn btn-primary">Register for the Workshop</a></p> <p><br /> typo3.org text prep.: Carlos Llanos • Proofreading: Mathias Bolt Lesniak</p>]]></content:encoded>',
                    '</item>',
                '</channel>',
            '</rss>',
        ]);

        $collection = $this->subject->getEntries($input);
        $this->assertInstanceOf(Collection::class, $collection, 'Did not get a collection');
        $this->assertCount(2, $collection, 'Collection did not contain 2 entries.');
    }

    private function getInvalidXml(): string
    {
        return implode(PHP_EOL, [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<rss version="2.0">',
            '<openingTagWithoutclosing>',
            '</rss>',
        ]);
    }

    private function getInvalidRss(): string
    {
        return implode(PHP_EOL, [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<rss version="2.0">',
                '<channel>',
                    '<title>Official typo3.org news</title>',
                    '<link>https://www.typo3.org/</link>',
                    '<nonExistingTag/>',
                '</channel>',
            '</rss>',
        ]);
    }
}
