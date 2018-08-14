<?php
namespace App\Instagram;

use App\Entity\Account;
use Doctrine\Common\Persistence\ObjectManager;

class InstagramLoginStrategy
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * InstagramConnectionLoginStrategy constructor.
     * @param ObjectManager $objectManager
     */
    public function __construct(
        ObjectManager $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param InstagramConnection $instagramConnection
     * @param Account $account
     * @throws InvalidCredentialsException
     */
    public function login(InstagramConnection $instagramConnection, Account $account)
    {
        try {
            $this->doLogin($instagramConnection, $account);
            $account->incrementLoginCount();
            $account->setLastLoginDatetime(new \DateTime());
            $account->setStatus(Account::STATUS_OK);
            $this->objectManager->flush();
        } catch (InvalidCredentialsException $e) {
            $account->setStatus(Account::STATUS_KO);
            $this->objectManager->flush();
            throw $e;
        }
    }

    /**
     * @param InstagramConnection $instagramConnection
     * @param Account $account
     */
    private function doLogin(InstagramConnection $instagramConnection, Account $account)
    {
        try {
            $instagramConnection->login($account->getUsername(), $account->getPassword());
        } catch (\Exception $e) {
            //give a second chance
            sleep(1);
            $instagramConnection->login($account->getUsername(), $account->getPassword());
        }
    }
}
