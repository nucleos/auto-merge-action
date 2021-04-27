<?php

/*
 * This file is part of the NucleosUserBundle package.
 *
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\AutoMergeAction\Command;

use Github\Exception\RuntimeException;
use Nucleos\AutoMergeAction\Client\PullRequest\Query;
use Nucleos\AutoMergeAction\Client\PullRequests;
use Nucleos\AutoMergeAction\Config\Configuration;
use Nucleos\AutoMergeAction\Domain\Label;
use Nucleos\AutoMergeAction\Domain\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class MergeCommand extends Command
{
    protected static $defaultName = 'merge';

    private PullRequests $pullRequests;

    public function __construct(PullRequests $pullRequests)
    {
        $this->pullRequests = $pullRequests;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDefinition(
                [
                    new InputArgument('repository', InputArgument::REQUIRED, 'The repository to scan (format: organisation/repository)'),
                    new InputOption('label', 'l', InputArgument::OPTIONAL, 'Label that indicates a pull request for merge.', Configuration::DEFAULT_LABEL),
                    new InputOption('ignore-label', 'i', InputArgument::IS_ARRAY, 'Label that forbids a merge.', Configuration::DEFAULT_IGNORE_LABEL),
                    new InputOption('squash', null, InputOption::VALUE_NONE, 'Squash commits.'),
                    new InputOption('dry-run', null, InputOption::VALUE_NONE, 'Only shows which pull requests would have been merged.'),
                ]
            )
            ->setDescription('Merges open pull requests that build successfully')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = $this->getConfiguration($input);

        $io = new SymfonyStyle($input, $output);

        if ($output->isVerbose()) {
            $io->title(sprintf(
                'Scanning "%s" for "%s" label (ignoring label: %s)',
                $config->repository(),
                $config->label(),
                $config->ignoreLabel()
            ));
        }

        $repository   = Repository::fromString($config->repository());
        $label        = Label::fromString($config->label());

        $pullRequests = $this->pullRequests->search(
            Query::labeled($repository, $label, Label::fromString($config->ignoreLabel()))
        );

        if ([] === $pullRequests) {
            if ($output->isVerbose()) {
                $io->success('No open pull requests found');
            }

            return self::SUCCESS;
        }

        \assert($output instanceof ConsoleOutputInterface);

        $section = $output->section();

        $table = new Table($section);
        $table->setHeaders([
            'Status',
            'ID',
            'Title',
        ]);

        foreach ($pullRequests as $pullRequest) {
            $status = '<fg=red>UNSTABLE</>';

            if ($pullRequest->updatedWithinTheLast60Seconds()) {
                $status = '<fg=yellow>SKIPPED</>';
            } elseif (true === $pullRequest->isMergeable() && $pullRequest->isCleanBuild()) {
                if ($config->isDryRun()) {
                    $io->write('<fg=yellow>READY</> ');
                } else {
                    if ($config->isSquash()) {
                        $status = '<fg=green>SQUASHED</>';
                    } else {
                        $status = '<fg=green>MERGED</>';
                    }

                    try {
                        $this->pullRequests->merge($repository, $pullRequest, $config->isSquash());
                        $this->pullRequests->removeLabel($repository, $pullRequest, $label);
                    } catch (RuntimeException $exception) {
                        $status = '<fg=red>ERROR</>';

                        $io->error($exception->getMessage());
                    }
                }
            }

            $table->appendRow([
                $status,
                sprintf('<href=%s>%s</>', $pullRequest->htmlUrl(), $pullRequest->issue()->toInt()),
                $pullRequest->title(),
            ]);
        }

        return self::SUCCESS;
    }

    private function getConfiguration(InputInterface $input): Configuration
    {
        return Configuration::fromInput([
            'repository'   => $input->getArgument('repository'),
            'label'        => $input->getOption('label'),
            'ignore-label' => $input->getOption('ignore-label'),
            'squash'       => $input->getOption('squash'),
            'dry-run'      => $input->getOption('dry-run'),
        ]);
    }
}
