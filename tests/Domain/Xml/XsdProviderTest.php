<?php

namespace DanielSiepmann\Newsreader\Test\Domain\Xml;

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

use DanielSiepmann\Newsreader\Domain\Xml\XsdProvider;
use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;

class XsdProviderTest extends TestCase
{
    /**
     * @test
     */
    public function canBeCreatedWithDefaultFolder()
    {
        $this->assertInstanceOf(XsdProvider::class, new XsdProvider());
    }

    /**
     * @test
     */
    public function nonExistingRootFolderThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
        new XsdProvider('unkown/folder');
    }

    /**
     * @test
     */
    public function nonExistingFileWillThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new XsdProvider('unkown/folder'))->get('unkown', 'unkown');
    }

    /**
     * @test
     */
    public function existingFileCanBeRetrieved()
    {
        vfsStream::setup('root', null, [
            'Resources' => [
                'XSDs' => [
                    'Known' => [
                        '1.0.xsd' => 'content',
                    ],
                ],
            ],
        ]);

        $subject = new XsdProvider(vfsStream::url('root/Resources/XSDs/'));
        $path = $subject->get('Known', '1.0');

        $this->assertSame(
            vfsStream::url('root/Resources/XSDs/Known/1.0.xsd'),
            $path,
            'Provider did not return expected file path.'
        );
    }
}
