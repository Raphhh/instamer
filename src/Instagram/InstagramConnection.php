<?php
namespace App\Instagram;

use App\Utils\ExceptionLogHelper;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class InstagramConnection
{
    const TEMPORIZATION = 5; //in seconds

    /**
     * @var LoggerInterface
     */
    protected $logger;
    /**
     * @var InstagramAPIFactory
     */
    private $instagramAPIFactory;

    /**
     * InstagramStoryCrawler constructor.
     *
     * @param InstagramAPIFactory $instagramAPIFactory
     * @param LoggerInterface|null $logger
     */
    public function __construct(InstagramAPIFactory $instagramAPIFactory, LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger();
        $this->instagramAPIFactory = $instagramAPIFactory;
    }

    /**
     * @return string
     */
    public function getCurrentAccountId()
    {
        return $this->getInstagram()->account_id;
    }

    /**
     * @return \InstagramAPI\Instagram
     */
    protected function getInstagram()
    {
        return $this->instagramAPIFactory->create();
    }

    /**
     * @param $username
     * @param $password
     *
     * @return \InstagramAPI\Response\LoginResponse|null
     */
    public function login($username, $password)
    {
        try {
            return $this->getInstagram()->login($username, $password);
        } catch (\Exception $e) {
            $this->logger->warning(
                'Login fail for instagram account',
                ExceptionLogHelper::formatContext($e, [
                    'username'          => $username,
                    'password'          => $password,
                ])
            );
            throw new InvalidCredentialsException('Login fail for instagram account', $e->getCode(), $e);
        }
    }

    /**
     * @param $username
     */
    public function resetSettings($username)
    {
        $this->getInstagram()->settings->deleteUser($username);
    }
}
