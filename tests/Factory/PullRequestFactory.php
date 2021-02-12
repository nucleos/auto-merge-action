<?php

declare(strict_types=1);

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Tests\Factory;

use Ergebnis\Test\Util\Helper;

final class PullRequestFactory
{
    use Helper;

    /**
     * @param array<mixed> $parameters
     *
     * @return array<mixed>
     */
    public static function create(array $parameters = []): array
    {
        $faker = self::faker();

        $repo = null;

        $response = [
            'number'     => $faker->numberBetween(1, 99999),
            'title'      => $faker->sentence,
            'updated_at' => $faker->date('Y-m-d\TH:i:s\Z'),
            'merged_at'  => $faker->date('Y-m-d\TH:i:s\Z'),
            'base'       => [
                'ref' => $faker->sentence(1),
            ],
            'head' => [
                'ref'  => $faker->sentence(1),
                'sha'  => $faker->sha256,
                'repo' => $repo,
            ],
            'mergeable'       => $faker->optional()->boolean,
            'mergeable_state' => $faker->randomElement(['dirty', 'clean', 'draft']),
            'html_url'        => $faker->url,
            'labels'          => array_map(static function (): array {
                return [
                    'name' => self::faker()->title,
                ];
            }, range(0, $faker->numberBetween(0, 5))),
        ];

        return array_replace_recursive(
            $response,
            $parameters
        );
    }
}
