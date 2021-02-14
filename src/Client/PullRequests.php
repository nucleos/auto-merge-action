<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Client;

use Github\Client as GithubClient;
use Github\ResultPagerInterface;
use Nucleos\AutoMergeAction\Client\PullRequest\Query;
use Nucleos\AutoMergeAction\Domain\Issue;
use Nucleos\AutoMergeAction\Domain\Label;
use Nucleos\AutoMergeAction\Domain\PullRequest;
use Nucleos\AutoMergeAction\Domain\Repository;
use Webmozart\Assert\Assert;

final class PullRequests
{
    private GithubClient $github;
    private ResultPagerInterface $githubPager;

    public function __construct(GithubClient $github, ResultPagerInterface $githubPager)
    {
        $this->github      = $github;
        $this->githubPager = $githubPager;
    }

    public function merge(Repository $repository, PullRequest $pullRequest, bool $squash, ?string $title = null): void
    {
        $this->github->pullRequests()->merge(
            $repository->username(),
            $repository->name(),
            $pullRequest->issue()->toInt(),
            $squash ? '' : $pullRequest->title(),
            $pullRequest->head()->sha()->toString(),
            $squash ? 'squash' : 'merge',
            $title
        );
    }

    /**
     * @return PullRequest[]
     */
    public function search(Query $query): array
    {
        return array_map(function (array $searchResponse): PullRequest {
            $issue = Issue::fromInt($searchResponse['number']);
            $repository = Repository::fromUrl($searchResponse['repository_url']);

            $response = $this->github->pullRequests()->show(
                $repository->username(),
                $repository->name(),
                $issue->toInt()
            );

            Assert::isArray($response);

            return PullRequest::fromResponse($response);
        }, $this->githubPager->fetchAll($this->github->search(), 'issues', [$query->toString()]));
    }

    public function removeLabel(Repository $repository, PullRequest $pullRequest, Label $label): void
    {
        $this->github->issue()->labels()->remove(
            $repository->username(),
            $repository->name(),
            $pullRequest->issue()->toInt(),
            $label->name(),
        );
    }
}
