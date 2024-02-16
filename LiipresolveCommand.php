<?php

namespace App\Command\base;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ArticleRepository;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\DomCrawler\Crawler;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Console\Input\ArrayInput;

class LiipresolveCommand extends Command
{
    private $entityManager;
    private $cacheManager;
    private $filterManager;

    public function __construct(EntityManagerInterface $entityManager,  CacheManager $cacheManager, FilterManager $filterManager)
    {
        $this->entityManager = $entityManager;
        $this->cacheManager = $cacheManager;
        $this->filterManager = $filterManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('liip:resolve-all')
            ->setDescription('Resolve Liip Imagine cache for all articles');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    { //on vérifie que artilce repository existe
        if ($this->entityManager->getRepository('App\Entity\Article')->count(['deletedAt' => null]) > 0) {
            $articles = $this->entityManager->getRepository('App\Entity\Article')->findBy(['deletedAt' => null]);
            foreach ($articles as $article) {
                $crawler = new Crawler($article->getArticle());
                $images = $crawler->filter('img')->extract(['src']);
                foreach ($images as $imagePath) {
                    if (substr($imagePath, 0, strlen('/uploads')) == '/uploads') {
                        $command = $this->getApplication()->find('liip:imagine:cache:resolve');
                        $arguments = [
                            'command' => 'liip:imagine:cache:resolve',
                            'paths' => [urldecode($imagePath)],
                        ];
                        $input = new ArrayInput($arguments);
                        $command->run($input, $output);
                        //$retour = $this->cacheManager->store($imagePath, $filter);
                        $output->writeln("Resolved cache for image '$imagePath' ");
                    }
                }
            }
        }
        $output->writeln('Cache resolution completed.');
        return Command::SUCCESS;
    }
}
