<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Following;
use App\Instagram\InstagramAccountCrawler;
use App\Instagram\Transformer\FollowingTransformer;
use App\Repository\FollowingRepository;
use Doctrine\Common\Persistence\ObjectManager;

class FollowerSynchronizationProcess
{
    /**
     * @var FollowingRepository
     */
    private $followingRepository;
    /**
     * @var ObjectManager
     */
    private $objectManager;
    /**
     * @var FollowingTransformer
     */
    private $followingTransformer;

    /**
     * FollowerSynchronizationProcess constructor.
     * @param ObjectManager $objectManager
     * @param FollowingRepository $followingRepository
     * @param FollowingTransformer $followingTransformer
     */
    public function __construct(
        ObjectManager $objectManager,
        FollowingRepository $followingRepository,
        FollowingTransformer $followingTransformer
    ) {
        $this->followingRepository = $followingRepository;
        $this->objectManager = $objectManager;
        $this->followingTransformer = $followingTransformer;
    }

    /**
     * @param InstagramAccountCrawler $instagramAccountCrawler
     * @param Account $account
     * @return array
     */
    public function synchronize(
        InstagramAccountCrawler $instagramAccountCrawler,
        Account $account
    ) {

        $followers = $instagramAccountCrawler->getFollowersByAccountId($account->getAccountId());
        $followers = $this->followingTransformer->transformList($account, $followers);

        //first we set as reciprocal all the followings that are followers
        $ids = [];
        foreach ($followers as $i => $follower) {
            $following = $this->setFollowingAsReciprocal($follower);
            if ($following) {
                $ids[] = $following->getId();
                $this->objectManager->flush();
                $this->objectManager->detach($following);
            }
        }

        //second, all the other followings reciprocal must be set as not reciprocal
        foreach ($this->followingRepository->generateReciprocalsBut($ids) as $i => $following) {
            $following->setIsReciprocal(false);
            $this->objectManager->flush();
            $this->objectManager->detach($following);
        }

        return $ids;
    }

    /**
     * @param Following $follower
     * @return Following|null
     */
    private function setFollowingAsReciprocal(Following $follower)
    {
        $existing = $this->followingRepository->findOneBy(['accountId' => $follower->getAccountId()]);
        if ($existing) {
            $existing->setIsReciprocal(true);
            return $existing;
        }
        return null;
    }
}
