<?php

namespace App\Instagram\Transformer;

use App\Entity\Account;
use App\Entity\Following;
use InstagramAPI\Response\Model\User;

class FollowingTransformer
{
    /**
     * @param Account $account
     * @param iterable|User[] $users
     * @return \Generator|Following[]
     */
    public function transformList(Account $account, iterable $users)
    {
        foreach ($users as $user) {
            yield $this->transform($account, $user);
        }
    }

    /**
     * @param Account $account
     * @param User $user
     * @return Following
     */
    public function transform(Account $account, User $user)
    {
        $following = new Following();
        $following->setAccount($account);
        $following->setAccountId($user->getPk());
        $following->setUsername($user->getUsername());
        $following->setIsFrozen(true);
        return $following;
    }
}
