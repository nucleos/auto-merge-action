<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Tests\Domain;

use DateTimeImmutable;
use DateTimeZone;
use Ergebnis\Test\Util\Helper;
use InvalidArgumentException;
use Nucleos\AutoMergeAction\Domain\PullRequest;
use Nucleos\AutoMergeAction\Tests\Factory\PullRequestFactory;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
final class PullRequestTest extends TestCase
{
    use Helper;

    public function testThrowsExceptionIfResponseIsEmpty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse([]);
    }

    public function testUsesNumberFromResponse(): void
    {
        $response = PullRequestFactory::create();

        $pullRequest = PullRequest::fromResponse($response);

        static::assertSame($response['number'], $pullRequest->issue()->toInt());
    }

    public function testThrowsExceptionIfNumberIsNotSet(): void
    {
        $response = PullRequestFactory::create();
        unset($response['number']);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    public function testThrowsExceptionIfNumberIsZero(): void
    {
        $response = PullRequestFactory::create([
            'number' => 0,
        ]);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    public function testThrowsExceptionIfNumberIsNgeative(): void
    {
        $response = PullRequestFactory::create([
            'number' => -1,
        ]);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    public function testUsesTitleFromResponse(): void
    {
        $value = self::faker()->sentence;

        $response = PullRequestFactory::create([
            'title' => $value,
        ]);

        $pullRequest = PullRequest::fromResponse($response);

        static::assertSame($value, $pullRequest->title());
    }

    public function testThrowsExceptionIfTitleIsNotSet(): void
    {
        $response = PullRequestFactory::create();
        unset($response['title']);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function testThrowsExceptionIfTitleIs(string $value): void
    {
        $response = PullRequestFactory::create([
            'title' => $value,
        ]);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    public function testUsesUpdatedAtFromResponse(): void
    {
        $response = PullRequestFactory::create([
            'updated_at' => $value = self::faker()->date('Y-m-d\TH:i:s\Z'),
        ]);

        $pullRequest = PullRequest::fromResponse($response);

        static::assertSame(
            $value,
            $pullRequest->updatedAt()->format('Y-m-d\TH:i:s\Z')
        );
    }

    public function testThrowsExceptionIfUpdatedAtIsNotSet(): void
    {
        $response = PullRequestFactory::create();
        unset($response['updated_at']);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function testThrowsExceptionIfUpdatedAtIs(string $value): void
    {
        $response = PullRequestFactory::create([
            'updated_at' => $value,
        ]);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    public function testThrowsExceptionIfMergedAtIsNotSet(): void
    {
        $response = PullRequestFactory::create();
        unset($response['merged_at']);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    /**
     * @dataProvider \Ergebnis\Test\Util\DataProvider\StringProvider::empty()
     */
    public function testThrowsExceptionIfMergedAtIs(string $value): void
    {
        $response = PullRequestFactory::create([
            'merged_at' => $value,
        ]);

        $this->expectException(InvalidArgumentException::class);

        PullRequest::fromResponse($response);
    }

    public function testValid(): void
    {
        $response = [
            'number'     => 123,
            'title'      => 'Update dependency',
            'updated_at' => '2020-01-01T19:00:00Z',
            'merged_at'  => '2020-01-01T19:00:00Z',
            'base'       => [
                'ref' => $baseRef = 'baseRef',
            ],
            'head' => [
                'ref'  => $headRef = 'headRef',
                'sha'  => $headSha = 'sha',
            ],
            'user' => [
                'id'       => $userId = 42,
                'login'    => $userLogin = 'userLogin',
                'html_url' => $userHtmlUrl = 'https://test.com',
            ],
            'mergeable'       => true,
            'mergeable_state' => 'dirty',
            'body'            => $body = 'The body!',
            'html_url'        => $htmlUrl = 'https://test.com',
            'labels'          => [
                [
                    'name'  => $labelName = 'patch',
                ],
            ],
        ];

        $pr = PullRequest::fromResponse($response);

        static::assertSame($headSha, $pr->head()->sha()->toString());
        static::assertTrue($pr->isMergeable());
        static::assertSame($htmlUrl, $pr->htmlUrl());
        static::assertTrue($pr->hasLabels());

        $label = $pr->labels()[0];
        static::assertSame($labelName, $label->name());
    }

    public function testUpdatedWithinTheLast60SecondsReturnsTrue(): void
    {
        $now = new DateTimeImmutable(
            'now',
            new DateTimeZone('UTC')
        );

        $response = PullRequestFactory::create([
            'updated_at' => $now->format('Y-m-d\TH:i:s\Z'),
        ]);

        $pr = PullRequest::fromResponse($response);

        static::assertTrue($pr->updatedWithinTheLast60Seconds());
    }

    public function testUpdatedWithinTheLast60SecondsReturnsFalse(): void
    {
        $now = new DateTimeImmutable(
            '2020-01-01 19:00:00',
            new DateTimeZone('UTC')
        );

        $response = PullRequestFactory::create([
            'updated_at' => $now->format('Y-m-d\TH:i:s\Z'),
        ]);

        $pr = PullRequest::fromResponse($response);

        static::assertFalse($pr->updatedWithinTheLast60Seconds());
    }
}
