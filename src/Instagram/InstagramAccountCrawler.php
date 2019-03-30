<?php
namespace App\Instagram;

use InstagramAPI\Exception\NotFoundException;

class InstagramAccountCrawler extends InstagramConnection
{
    /**
     * @param $username
     * @return string
     */
    public function getAccountIdFromUsername($username)
    {
        return $this->getInstagram()->people->getUserIdForName($username);
    }

    /**
     * @param $accountId
     * @return \InstagramAPI\Response\Model\User
     */
    public function getAccountByAccountId($accountId)
    {
        try {
            return $this->getInstagram()->people->getInfoById($accountId)->getUser();
        } catch (NotFoundException $e) {
            throw new InvalidCredentialsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $accountId
     * @param null $maxId
     * @param int $sleep
     * @return \Generator|\InstagramAPI\Response\Model\User[]
     */
    public function getFollowingsByAccountId($accountId, $maxId = null, $sleep = InstagramConnection::TEMPORIZATION)
    {
        try {
            $rankToken = \InstagramAPI\Signatures::generateUUID();
            do {
                $response = $this->getInstagram()->people->getFollowing($accountId, $rankToken, null, $maxId);
                foreach ($response->getUsers() as $user) {
                    yield $user;
                }
                $maxId = $response->getNextMaxId();
                sleep($sleep);
            } while ($maxId !== null);
        } catch (NotFoundException $e) {
            throw new InvalidCredentialsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $accountId
     * @param null $maxId
     * @param int $sleep
     * @return \Generator|\InstagramAPI\Response\Model\User[]
     */
    public function getFollowersByAccountId($accountId, $maxId = null, $sleep = InstagramConnection::TEMPORIZATION)
    {
        try {
            $rankToken = \InstagramAPI\Signatures::generateUUID();
            do {
                $response = $this->getInstagram()->people->getFollowers($accountId, $rankToken, null, $maxId);
                foreach ($response->getUsers() as $user) {
                    yield $user;
                }
                $maxId = $response->getNextMaxId();
                sleep($sleep);
            } while ($maxId !== null);
        } catch (NotFoundException $e) {
            throw new InvalidCredentialsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $accountId
     * @return \Generator|\InstagramAPI\Response\Model\User[]
     */
    public function discoverAccountsByAccountId($accountId)
    {
        try {
            $response = $this->getInstagram()->people->getSuggestedUsers($accountId);
            foreach ($response->getUsers() as $user) {
                yield $user;
            }
        } catch (NotFoundException $e) {
            throw new InvalidCredentialsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $accountId
     */
    public function follow($accountId)
    {
        try {
            $this->getInstagram()->people->follow($accountId);
        } catch (NotFoundException $e) {
            throw new InvalidCredentialsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param $accountId
     */
    public function unfollow($accountId)
    {
        try {
            $this->getInstagram()->people->unfollow($accountId);
        } catch (NotFoundException $e) {
            throw new InvalidCredentialsException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param null $maxId
     * @param int $sleep
     * @return \Generator|\InstagramAPI\Response\Model\Media[]
     */
    public function getLikes($maxId = null, $sleep = InstagramConnection::TEMPORIZATION)
    {
        try {
            do {
                $response = $this->getInstagram()->media->getLikedFeed($maxId);
                foreach ($response->getItems() as $item) {
                    yield $item;
                }
                $maxId = $response->getNextMaxId();
                sleep($sleep);
            } while ($maxId !== null);
        } catch (NotFoundException $e) {
            throw new InvalidCredentialsException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
