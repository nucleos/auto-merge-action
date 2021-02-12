<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Tests\Config;

use Ergebnis\Test\Util\Helper;
use InvalidArgumentException;
use Nucleos\AutoMergeAction\Config\Configuration;
use PHPUnit\Framework\TestCase;

final class ConfigurationTest extends TestCase
{
    use Helper;

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function testThrowsExceptionForRepository(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);

        Configuration::fromInput([
            'ref' => $value,
        ]);
    }
}
