<?php

namespace App\Instagram;

use App\Entity\Account;

class Instagram
{
    /**
     * @var InstagramAccountCrawler
     */
    private $instagramAccountCrawler;
    /**
     * @var InstagramLoginStrategy
     */
    private $instagramLoginStrategy;

    public function __construct(
        InstagramAccountCrawler $instagramAccountCrawler,
        InstagramLoginStrategy $instagramLoginStrategy
    ) {

        $this->instagramAccountCrawler = $instagramAccountCrawler;
        $this->instagramLoginStrategy = $instagramLoginStrategy;
    }

    /**
     * @param Account $account
     * @return InstagramAccountCrawler
     */
    public function getAccountCrawler(Account $account)
    {
        $this->instagramLoginStrategy->login($this->instagramAccountCrawler, $account);
        return $this->instagramAccountCrawler;
    }
}
