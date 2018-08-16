<?php

namespace App\Command;

use App\Instagram\Instagram;
use App\Repository\AccountRepository;
use App\Service\FollowerSynchronizationProcess;
use App\Service\FollowingSynchronizationProcess;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FollowingSynchronizeCommand extends Command
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
     * @var FollowerSynchronizationProcess
     */
    private $followerSynchronizationProcess;

    public function __construct(
        Instagram $instagram,
        FollowingSynchronizationProcess $followingSynchronizationProcess,
        FollowerSynchronizationProcess $followerSynchronizationProcess,
        AccountRepository $accountRepository
    ) {
        parent::__construct();

        $this->instagram = $instagram;
        $this->followingSynchronizationProcess = $followingSynchronizationProcess;
        $this->followerSynchronizationProcess = $followerSynchronizationProcess;
        $this->accountRepository = $accountRepository;
    }

    protected function configure()
    {
        $this
            ->setName('following:synchronize')
            ->setDescription('Synchronize relations with local database')
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

        $output->writeln('<comment>followings synchronization</comment>');
        $result = $this->followingSynchronizationProcess->synchronize($crawler, $account);
        $output->writeln(sprintf('%s followings', count($result)));


        $output->writeln('<comment>followers synchronization</comment>');
        $result = $this->followerSynchronizationProcess->synchronize($crawler, $account);
        $output->writeln(sprintf('%s reciprocals', count($result)));
    }
}
