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

        if ($entity) {
            $io->title('Remove Entity');
            $io->text('Entity: ' . $entity);
            @unlink('src/Entity/' . $entity . '.php');
            @unlink('src/Repository/' . $entity . 'Repository.php');
            @unlink('src/Form/' . $entity . 'Type.php');
            @rmdir('templates/' . $entity);
            @unlink('src/Controller/' . $entity . 'Controller.php');
            //on vérifie que les fichier sont supprimer
            if (!file_exists('src/Entity/' . $entity . '.php') && !file_exists('src/Repository/' . $entity . 'Repository.php') && !file_exists('src/Form/' . $entity . 'Type.php') &&  !file_exists('templates/' . $entity) && !file_exists('src/Controller/' . $entity . 'Controller.php')) {
                $io->success('Entity removed');
            } else {
                //on affiche les fichier qui n'ont pas été supprimer
                if (file_exists('src/Entity/' . $entity . '.php')) {
                    $io->error('src/Entity/' . $entity . '.php');
                }
                if (file_exists('src/Repository/' . $entity . 'Repository.php')) {
                    $io->error('src/Repository/' . $entity . 'Repository.php');
                }
                if (file_exists('src/Form/' . $entity . 'Rtype.php')) {
                    $io->error('src/Form/' . $entity . 'Type.php');
                }
                if (file_exists('templates/' . $entity)) {
                    $io->error('templates/' . $entity);
                }

                if (file_exists('src/Controller/' . $entity . 'Controller.php')) {
                    $io->error('src/Controller/' . $entity . 'Controller.php');
                }
                return Command::FAILURE;
            }
        } else {
            $io->error('Entity not found');
            return Command::FAILURE;
        }


        return Command::SUCCESS;
    }
}
