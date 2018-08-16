<?php

namespace App\Command;

use App\Instagram\Instagram;
use App\Instagram\Transformer\FollowingTransformer;
use App\Repository\AccountRepository;
use App\Service\FollowingSynchronizationProcess;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FollowingAddCommand extends Command
{
    /**
     * @var Instagram
     */
    private $instagram;
    /**
     * @var AccountRepository
     */
    private $accountRepository;
    /**
     * @var FollowingSynchronizationProcess
     */
    private $followingSynchronizationProcess;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var FollowingTransformer
     */
    private $followingTransformer;

    public function __construct(
        Instagram $instagram,
        AccountRepository $accountRepository,
        ObjectManager $objectManager,
        FollowingTransformer $followingTransformer,
        FollowingSynchronizationProcess $followingSynchronizationProcess
    ) {
        parent::__construct();

        $this->instagram = $instagram;
        $this->accountRepository = $accountRepository;
        $this->objectManager = $objectManager;
        $this->followingTransformer = $followingTransformer;
        $this->followingSynchronizationProcess = $followingSynchronizationProcess;
    }

    protected function configure()
    {
        $this
            ->setName('following:add')
            ->setDescription('SAdd relations')
            ->addArgument(
                'username',
                InputArgument::REQUIRED
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $account = $this->accountRepository->findOneBy(['username' => $input->getArgument('username')]);

        if (!$account) {
            throw new \InvalidArgumentException('account not found');
        }

        $crawler = $this->instagram->getAccountCrawler($account);

        $output->writeln('<comment>followings add</comment>');
        $followings = $crawler->discoverAccountsByAccountId($account->getAccountId());
        $followings = $this->followingTransformer->transformList($account, $followings);
        foreach ($followings as $following) {

            $crawler->follow($following->getAccountId());
            $following = $this->followingSynchronizationProcess->addFollowing($following);

            $this->objectManager->flush();
            $this->objectManager->detach($following);

            $output->write('sleeping');
            sleep(1);
            $output->write('.');
            sleep(1);
            $output->write('.');
            sleep(1);
            $output->write('.');
            sleep(1);
            $output->write('.');
            sleep(1);
            $output->writeln('.');
        }
    }
}
