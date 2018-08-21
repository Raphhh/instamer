<?php

namespace App\Service;

use App\Instagram\InstagramAccountCrawler;

class FollowingDiscover
{
    /**
     * @param InstagramAccountCrawler $instagramAccountCrawler
     * @param string $fromUsername
     * @return \Generator|\InstagramAPI\Response\Model\User[]
     */
    public function discover(InstagramAccountCrawler $instagramAccountCrawler, $fromUsername = '')
    {
        if ($fromUsername) {
            $accountId = $instagramAccountCrawler->getAccountIdFromUsername($fromUsername);
            return $instagramAccountCrawler->getFollowingsByAccountId($accountId);
        }
        return $instagramAccountCrawler->discoverAccountsByAccountId($instagramAccountCrawler->getCurrentAccountId());
    }
}
