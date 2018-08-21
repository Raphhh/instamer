<?php

namespace App\Command;

use App\Instagram\Instagram;
use App\Repository\AccountRepository;
use App\Repository\FollowingRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class FollowingPruneCommand extends Command
{
    /**
     * @var AccountRepository
     */
    private $accountRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var FollowingRepository
     */
    private $followingRepository;
    /**
     * @var Instagram
     */
    private $instagram;

    public function __construct(
        Instagram $instagram,
        AccountRepository $accountRepository,
        FollowingRepository $followingRepository,
        ObjectManager $objectManager
    ) {
        parent::__construct();

        $this->accountRepository = $accountRepository;
        $this->objectManager = $objectManager;
        $this->followingRepository = $followingRepository;
        $this->instagram = $instagram;
    }

    protected function configure()
    {
        $this
            ->setName('following:prune')
            ->setDescription('Prune non recpiprocal relations')
            ->addArgument(
                'username',
                InputArgument::REQUIRED
            )
            ->addOption(
                'before',
                null,
                InputOption::VALUE_REQUIRED,
                'datetime limit',
                '10 days ago'
            )
            ->addOption(
                'dry-run',
                null,
                InputOption::VALUE_NONE,
                'dry-run'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $account = $this->accountRepository->findOneBy(['username' => $input->getArgument('username')]);

        if (!$account) {
            throw new \InvalidArgumentException('account not found');
        }

        $before = new \DateTime($input->getOption('before'));

        $crawler = null;
        if (!$input->getOption('dry-run')) {
            $crawler = $this->instagram->getAccountCrawler($account);
        }

        $output->writeln(sprintf('<comment>followings prune before %s</comment>', $before->format('c')));
        foreach ($this->followingRepository->generateDeactivables($account, $before) as $i => $following) {

            $output->writeln(sprintf(
                '[%s] %s (%s)',
                $following->getId(),
                $following->getUsername(),
                $following->getAccountId()
            ));

            if ($input->getOption('dry-run')) {
                continue;
            }

            $crawler->unfollow($following->getAccountId());
            $following->setDeletionDatetime(new \DateTime());

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
