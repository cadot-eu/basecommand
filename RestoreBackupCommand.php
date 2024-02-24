<?php

namespace App\Command\base;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\EventListener\base\BackupListener;

#[AsCommand(
    name: 'restoreBackup',
    description: 'Remove entity file and repository, templates, controller',
)]
class RestoreBackupCommand extends Command
{
    protected static $defaultName = 'app:restore-backup';

    protected function configure()
    {
        $this
            ->setDescription('Restore a backup file.')
            ->addArgument('backupFilename', InputArgument::REQUIRED, 'The filename of the backup to restore.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $backupFilename = $input->getArgument('backupFilename');
        $backupDir = '/app/var/backups'; // Update this path if necessary
        $databasePath = '/app/var/data.db'; // Update this path if necessary

        // Restore the specified backup file
        $backupListener = new BackupListener();
        $backupListener->restoreBackup($backupFilename, $backupDir, $databasePath);

        $output->writeln("Backup '$backupFilename' has been restored.");

        return Command::SUCCESS;
    }
}
