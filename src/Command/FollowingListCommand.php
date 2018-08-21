<?php

namespace App\Command;

use App\Instagram\Instagram;
use App\Repository\AccountRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FollowingListCommand extends Command
{
    /**
     * @var Instagram
     */
    private $instagram;
    /**
     * @var AccountRepository
     */
    private $accountRepository;

    public function __construct(Instagram $instagram, AccountRepository $accountRepository)
    {
        parent::__construct();

        $this->instagram = $instagram;
        $this->accountRepository = $accountRepository;
    }

    protected function configure()
    {
        $this
            ->setName('remote:following:list')
            ->setDescription('List account followings')
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
        foreach ($crawler->getFollowingsByAccountId($account->getAccountId()) as $i => $account) {
            $output->writeln(sprintf(
                '%s %s (%s)',
                $i,
                $account->getUsername(),
                $account->getPk()
            ));
       }
    }
}
