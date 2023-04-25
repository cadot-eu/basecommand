<?php

namespace App\Command\base;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'removeEntity',
    description: 'Remove entity file and repository, templates, controller',
)]
class RemoveEntityCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument('entity', InputArgument::OPTIONAL, 'Name of Entity');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $entity = ucfirst($input->getArgument('entity'));
        $actions = ['Repository','Controller','Form','Entity'];
        if ($entity) {
            $io->title('Remove Entity');
            $io->text('Entity: ' . $entity);
            foreach ($actions as $action) {
                switch ($action) {
                    case 'Form':
                        $name = $entity . 'Type';
                        break;
                    case 'Entity':
                        $name = $entity;
                        break;
                    default:
                        $name = $entity . $action;
                        break;
                }

                if (file_exists('src/' . $action . '/' . $name . '.php')) {
                    $io->comment('Remove src/' . $action . '/' . $name . '.php');
                    @unlink('src/' . $action . '/' . $name . '.php');
                    if (!file_exists('src/' . $action . '/' . $name . '.php')) {
                        $io->success($name . ' removed');
                    }
                }
            }
            if (file_exists('templates/' . lcfirst($entity))) {
                $io->comment('Remove templates/' . lcfirst($entity));
                //suppression des templates
                exec('rm -rf templates/' . lcfirst($entity));
                if (!file_exists('templates/' . lcfirst($entity))) {
                    $io->success('template removed');
                }
            }
               //on execute compoer autodump
            $io->text('composer dump-autoload');
            exec('composer dump-autoload');
        } else {
            $io->error('Entity not found');
            return Command::FAILURE;
        }


        return Command::SUCCESS;
    }
}
