<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:linkstester',
    description: 'Add a short description for your command',
)]
class LinkstesterCommand extends Command
{

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $helper = $this->getHelper('process');
        $process = new Process(['php', '/app/fink.phar', 'http://localhost', '--concurrency=12', '--output=/app/tests/linktests.json', '--exclude-url=_profiler']);

        $helper->run($output, $process, 'The process failed :(', function ($type, $data) {
            if (Process::ERR === $type) {
                dump($data);
            } else {
                dump($data);
            }
        });



        return Command::SUCCESS;
    }
}
