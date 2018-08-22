<?php

namespace App\Command;

use App\Instagram\Instagram;
use App\Instagram\Transformer\FollowingTransformer;
use App\Repository\AccountRepository;
use App\Repository\FollowingRepository;
use App\Service\FollowingDiscover;
use App\Service\FollowingSynchronizationProcess;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
    /**
     * @var FollowingDiscover
     */
    private $followingDiscover;
    /**
     * @var FollowingRepository
     */
    private $followingRepository;

    public function __construct(
        Instagram $instagram,
        AccountRepository $accountRepository,
        ObjectManager $objectManager,
        FollowingTransformer $followingTransformer,
        FollowingSynchronizationProcess $followingSynchronizationProcess,
        FollowingDiscover $followingDiscover,
        FollowingRepository $followingRepository
    ) {
        parent::__construct();

        $this->instagram = $instagram;
        $this->accountRepository = $accountRepository;
        $this->objectManager = $objectManager;
        $this->followingTransformer = $followingTransformer;
        $this->followingSynchronizationProcess = $followingSynchronizationProcess;
        $this->followingDiscover = $followingDiscover;
        $this->followingRepository = $followingRepository;
    }

    protected function configure()
    {
        $this
            ->setName('following:add')
            ->setDescription('Add relations')
            ->addArgument('username', InputArgument::REQUIRED)
            ->addOption('from', null, InputOption::VALUE_REQUIRED)
            ->addOption('limit', null, InputOption::VALUE_REQUIRED, '', 0);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $account = $this->accountRepository->findOneBy(['username' => $input->getArgument('username')]);

        if (!$account) {
            throw new \InvalidArgumentException('account not found');
        }

        $crawler = $this->instagram->getAccountCrawler($account);

        $output->writeln('<comment>followings add</comment>');
        $followings = $this->followingDiscover->discover($crawler, $input->getOption('from'));
        $followings = $this->followingTransformer->transformList($account, $followings, false);

        $index = 0;

        foreach ($followings as $i => $following) {

            $output->write(sprintf(
                '%s %s (%s)',
                $index,
                $following->getUsername(),
                $following->getAccountId()
            ));

            $existing = $this->followingRepository->findOneBy([
                'account' => $following->getAccount(),
                'accountId' => $following->getAccountId(),
            ]);

            if ($existing && !$existing->getDeletionDatetime()) {
                $output->writeln(sprintf(
                    ' => already following #%s',
                    $existing->getId()
                ));
                continue;
            }

            $output->writeln(' => follow');

            $crawler->follow($following->getAccountId());
            $following = $this->followingSynchronizationProcess->addFollowing($following);

            $this->objectManager->flush();
            $this->objectManager->detach($following);

            if ($input->getOption('limit') && $input->getOption('limit') <= ++$index) {
                break;
            }

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
