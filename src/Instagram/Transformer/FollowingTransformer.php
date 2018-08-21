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
     * @param $isFrozen
     * @return \Generator|Following[]
     */
    public function transformList(Account $account, iterable $users, $isFrozen)
    {
        foreach ($users as $user) {
            yield $this->transform($account, $user, $isFrozen);
        }
    }

    /**
     * @param Account $account
     * @param User $user
     * @param $isFrozen
     * @return Following
     */
    public function transform(Account $account, User $user, $isFrozen)
    {
        $following = new Following();
        $following->setAccount($account);
        $following->setAccountId($user->getPk());
        $following->setUsername($user->getUsername());
        $following->setIsFrozen($isFrozen);
        return $following;
    }
}
