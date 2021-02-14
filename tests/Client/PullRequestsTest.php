<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Tests\Client;

use Ergebnis\Test\Util\Helper;
use Github\Api\Issue as IssueApi;
use Github\Api\Issue\Labels as LabelApi;
use Github\Api\PullRequest as PullRequestApi;
use Github\Api\Search;
use Github\Client as GithubClient;
use Github\ResultPagerInterface;
use Nucleos\AutoMergeAction\Client\PullRequest\Query;
use Nucleos\AutoMergeAction\Client\PullRequests;
use Nucleos\AutoMergeAction\Domain\Label;
use Nucleos\AutoMergeAction\Domain\PullRequest;
use Nucleos\AutoMergeAction\Domain\Repository;
use Nucleos\AutoMergeAction\Tests\Factory\PullRequestFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class PullRequestsTest extends TestCase
{
    use Helper;

    /**
     * @var PullRequestApi&MockObject
     */
    private PullRequestApi $pullRequestApi;

    /**
     * @var IssueApi&MockObject
     */
    private IssueApi $issueApi;

    /**
     * @var LabelApi&MockObject
     */
    private LabelApi $labelApi;

    /**
     * @var GithubClient&MockObject
     */
    private GithubClient $github;

    /**
     * @var ResultPagerInterface&MockObject
     */
    private ResultPagerInterface $githubPager;

    private PullRequests $pullRequests;

    protected function setUp(): void
    {
        $this->pullRequestApi = $this->createMock(PullRequestApi::class);
        $this->issueApi       = $this->createMock(IssueApi::class);
        $this->labelApi       = $this->createMock(LabelApi::class);

        $this->github      = $this->getMockBuilder(GithubClient::class)
            ->addMethods(['pullRequests', 'issue', 'search'])
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock()
        ;

        $this->github
            ->method('pullRequests')
            ->willReturn($this->pullRequestApi)
        ;
        $this->github
            ->method('issue')
            ->willReturn($this->issueApi)
        ;
        $this->issueApi
            ->method('labels')
            ->willReturn($this->labelApi)
        ;

        $this->githubPager = $this->createMock(ResultPagerInterface::class);

        $this->pullRequests = new PullRequests(
            $this->github,
            $this->githubPager
        );
    }

    public function testMerge(): void
    {
        $this->pullRequestApi
            ->expects(static::once())
            ->method('merge')
            ->with(
                'acme',
                'repository',
                23,
                'My title',
                '0815',
                'merge',
                null
            )
        ;

        $this->pullRequests->merge(
            Repository::fromString('acme/repository'),
            PullRequest::fromResponse(PullRequestFactory::create([
                'number' => 23,
                'title'  => 'My title',
                'head'   => [
                    'sha' => '0815',
                ],
            ])),
            false
        );
    }

    public function testMergeWithSquash(): void
    {
        $this->pullRequestApi
            ->expects(static::once())
            ->method('merge')
            ->with(
                'acme',
                'repository',
                23,
                '',
                '0815',
                'squash',
                'Merge title'
            )
        ;

        $this->pullRequests->merge(
            Repository::fromString('acme/repository'),
            PullRequest::fromResponse(PullRequestFactory::create([
                'number' => 23,
                'title'  => 'My title',
                'head'   => [
                    'sha' => '0815',
                ],
            ])),
            true,
            'Merge title',
        );
    }

    public function testSearch(): void
    {
        $search = $this->createMock(Search::class);

        $this->github->method('search')
            ->willReturn($search)
        ;

        $this->pullRequestApi->method('show')
            ->willReturn(PullRequestFactory::create())
        ;

        $this->githubPager->method('fetchAll')
            ->with($search, 'issues', ['my-query'])
            ->willReturn([
                self::createSearchResponse(),
                self::createSearchResponse(),
            ])
        ;

        $response = $this->pullRequests->search(Query::fromString('my-query'));

        static::assertCount(2, $response);
    }

    public function testRemoveLabel(): void
    {
        $this->labelApi
            ->expects(static::once())
            ->method('remove')
            ->with(
                'acme',
                'repository',
                23,
                'my-label'
            )
        ;

        $this->pullRequests->removeLabel(
            Repository::fromString('acme/repository'),
            PullRequest::fromResponse(PullRequestFactory::create([
                'number' => 23,
            ])),
            Label::fromString('my-label')
        );
    }

    /**
     * @param array<mixed> $parameters
     *
     * @return array<mixed>
     */
    private static function createSearchResponse(array $parameters = []): array
    {
        $faker = self::faker();

        $response = [
            'number'         => $faker->numberBetween(1, 99999),
            'repository_url' => 'acme/repo',
        ];

        return array_replace_recursive(
            $response,
            $parameters
        );
    }
}
