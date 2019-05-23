<?php

namespace DanielSiepmann\Newsreader\Domain\Xml;

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

/**
 *
 */
class XsdProvider
{
    private $rootFolder = '';

    /**
     * @throws \InvalidArgumentException If rootFolder does not exist.
     */
    public function __construct(string $rootFolder = '')
    {
        if ($rootFolder === '') {
            $rootFolder = dirname(dirname(dirname(__FILE__))) . implode(DIRECTORY_SEPARATOR, [
                '',
                'Resources',
                'XSDs',
            ]);
        }

        $rootFolder = rtrim($rootFolder, '\\/');

        if (!is_dir($rootFolder)) {
            throw new \InvalidArgumentException('Folder does not exist: "' . $rootFolder . '".', 1558603778);
        }

        $this->rootFolder = $rootFolder;
    }

    public function get(string $format, string $version): string
    {
        $filepath = implode(DIRECTORY_SEPARATOR, [
            $this->rootFolder,
            $format,
            $version,
        ]) . '.xsd';

        if (!is_file($filepath)) {
            throw new \InvalidArgumentException(
                'No XSD found for given format and version. Tried: "' . $filepath . '".',
                1558603991
            );
        }

        return $filepath;
    }
}
