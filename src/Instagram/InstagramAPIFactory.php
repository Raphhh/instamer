<?php
namespace App\Instagram;

use InstagramAPI\Instagram;

class InstagramAPIFactory
{
    /**
     * @var bool
     */
    private $debug;
    /**
     * @var bool
     */
    private $truncatedDebug;
    /**
     * @var array
     */
    private $storageConfig;

    /**
     * @var Instagram
     */
    private $instagram;

    /**
     * InstagramAPIFactory constructor.
     *
     * @param bool  $debug
     * @param bool  $truncatedDebug
     * @param array $storageConfig
     */
    public function __construct(
        $storageConfig = [],
        $debug = false,
        $truncatedDebug = false
    ) {

        $this->debug = $debug;
        $this->truncatedDebug = $truncatedDebug;
        $this->storageConfig = $storageConfig;
    }

    /**
     * @return Instagram
     */
    public function create()
    {
        if (!$this->instagram) {
            Instagram::$allowDangerousWebUsageAtMyOwnRisk = true;
            $this->instagram = new Instagram($this->debug, $this->truncatedDebug, $this->storageConfig);
        }
        return $this->instagram;
    }
}
