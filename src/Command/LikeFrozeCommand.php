<?php

namespace App\Command;

use App\Entity\Following;
use App\Instagram\Instagram;
use App\Repository\AccountRepository;
use App\Repository\FollowingRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LikeFrozeCommand extends Command
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
     * @var FollowingRepository
     */
    private $followingRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;

    public function __construct(
        Instagram $instagram,
        AccountRepository $accountRepository,
        FollowingRepository $followingRepository,
        ObjectManager $objectManager
    ) {
        parent::__construct();

        $this->instagram = $instagram;
        $this->accountRepository = $accountRepository;
        $this->followingRepository = $followingRepository;
        $this->objectManager = $objectManager;
    }

    protected function configure()
    {
        $this
            ->setName('like:froze')
            ->setDescription('')
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

        $likes = $this->instagram->getAccountCrawler($account)->getLikes();

        foreach ($likes as $like) {
            /**
             * @var Following $following
             */
            $followings = $this->followingRepository->findBy(['accountId' => $like->getUser()->getPk()]);
            foreach ($followings as $following) {
                $output->writeln(sprintf('froze %s', $following->getUsername()));
                $following->setIsFrozen(true);
            }
        }

        $this->objectManager->flush();
    }
}
