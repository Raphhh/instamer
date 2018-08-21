<?php

namespace App\Command;

use App\Instagram\Instagram;
use App\Repository\AccountRepository;
use App\Service\FollowingDiscover;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FollowerDiscoverCommand extends Command
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
     * @var FollowingDiscover
     */
    private $followingDiscover;

    public function __construct(
        Instagram $instagram,
        AccountRepository $accountRepository,
        FollowingDiscover $followingDiscover
    ) {
        parent::__construct();

        $this->instagram = $instagram;
        $this->accountRepository = $accountRepository;
        $this->followingDiscover = $followingDiscover;
    }

    protected function configure()
    {
        $this
            ->setName('remote:follower:discover')
            ->setDescription('Discover potential followers')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addOption('from', null, InputOption::VALUE_REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $account = $this->accountRepository->findOneBy(['username' => $input->getArgument('username')]);

        if (!$account) {
            throw new \InvalidArgumentException('account not found');
        }

        $crawler = $this->instagram->getAccountCrawler($account);
        $accounts = $this->followingDiscover->discover($crawler, $input->getOption('from'));
        foreach ($accounts as $i => $account) {
            $output->writeln(sprintf(
                '%s %s (%s)',
                $i,
                $account->getUsername(),
                $account->getPk()
            ));
       }
    }
}
