<?php

namespace DanielSiepmann\Newsreader\Tests\Domain\Collection;

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
use PHPUnit\Framework\TestCase;

class NewsTest extends TestCase
{
    /**
     * @test
     */
    public function emptyCollectionCanBeCreated()
    {
        $empty = new NewsCollection();
        $this->assertCount(0, $empty, 'Could not create empty instance.');
    }

    /**
     * @test
     */
    public function collectionWithSingleNewsCanBeCreated()
    {
        $collection = new NewsCollection($this->getNews());
        $this->assertCount(1, $collection, 'Could not create single instance.');
    }

    /**
     * @test
     */
    public function collectionWithMultipleNewsCanBeCreated()
    {
        $collection = new NewsCollection($this->getNews(), $this->getNews());
        $this->assertCount(2, $collection, 'Could not create instance with two news.');
    }

    private function getNews(): News
    {
        return $this->getMockBuilder(News::class)
            ->disableOriginalConstructor()
            ->getMock();
    }
}
