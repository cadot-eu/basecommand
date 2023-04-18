<?php

namespace App\Command\base;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'changepassword',
    description: 'Add a short description for your command',
)]
class ChangepasswordCommand extends Command
{
    private $em,$userPasswordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
        $this->em = $em;

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->addArgument('userid', InputArgument::REQUIRED, 'id de l\'user')
            ->addArgument('password', InputArgument::REQUIRED, 'nouveau password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $id = $input->getArgument('userid');
        $password = $input->getArgument('password');
        $objetRepository = $this->em->getRepository('App\Entity\\User');

        $user = $objetRepository->find(['id' => $id]);
        $user->setPassword($this->userPasswordHasher->hashPassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}
