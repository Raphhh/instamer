<?php

namespace App\Service;

use App\Entity\Account;
use App\Instagram\Instagram;

class AccountCreator
{
    /**
     * @var Instagram
     */
    private $instagram;

    public function __construct(Instagram $instagram)
    {
        $this->instagram = $instagram;
    }

    /**
     * @param $username
     * @param $password
     * @return Account
     */
    public function createAccount($username, $password):Account
    {
        $account = new Account();
        $account->setUsername($username);
        $account->setPassword($password);

        $accountId = $this->instagram->getAccountCrawler($account)->getAccountIdFromUsername($username);
        $account->setAccountId($accountId);

        return $account;
    }
}
