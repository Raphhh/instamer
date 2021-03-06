<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Following;
use App\Instagram\InstagramAccountCrawler;
use App\Instagram\Transformer\FollowingTransformer;
use App\Repository\FollowingRepository;
use Doctrine\Common\Persistence\ObjectManager;

class FollowingSynchronizationProcess
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
     * FollowingManager constructor.
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
        $followings = $instagramAccountCrawler->getFollowingsByAccountId($account->getAccountId());
        $followings = $this->followingTransformer->transformList($account, $followings, true);


        // first add all the followings that have been added directly in instagram
        $ids = [];
        foreach ($followings as $i => $following) {
            $following = $this->addFollowing($following);
            $this->objectManager->flush();
            $ids[] = $following->getId();
            $this->objectManager->detach($following);
        }

        // second, delete all the followings that have been removed directly in instagram
        // that means all the active others
        foreach ($this->followingRepository->generateActivesBut($account, array_filter($ids)) as $i => $following) {
            $following->setDeletionDatetime(new \DateTime());
            $this->objectManager->flush();
            $this->objectManager->detach($following);
        }

        return $ids;
    }

    /**
     * @param Following $following
     * @return Following
     */
    public function addFollowing(Following $following)
    {
        $existing = $this->followingRepository->findOneBy([
            'account' => $following->getAccount(),
            'accountId' => $following->getAccountId(),
        ]);

        if ($existing) {
            //if the following was already been synchronized, be sure it is not deleted and set frozen.
            if ($existing->getDeletionDatetime()) {
                $existing->setDeletionDatetime(null);

                //todo ceci n'est pas juste dans le cas d'e l'attente d'une demande, car le following sera marqué comme deleted! du coup, il deviendra frozen ensuite.
                if (!$existing->isFrozen()) { //be sure to never unfreeze a frozen following
                    //$existing->setIsFrozen($following->isFrozen());
                }
            }
            return $existing;
        }

        //note that in this case the following is set as frozen (see FollowingTransformer)
        $this->objectManager->persist($following);
        return $following;
    }
}
