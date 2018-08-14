<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AccountRepository")
 */
class Account
{
    const STATUS_OK = 0;
    const STATUS_KO = 1;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $accountId;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $password;

    /**
     * @ORM\Column(type="smallint", options={"unsigned":true, "default":"0"})
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDatetime;

    /**
     * @ORM\Column(type="integer", options={"unsigned":true, "default":"0"})
     */
    private $loginCount;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastLoginDatetime;

    public function __construct()
    {
        $this->creationDatetime = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccountId(): ?string
    {
        return $this->accountId;
    }

    public function setAccountId(string $accountId): self
    {
        $this->accountId = $accountId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getCreationDatetime(): ?\DateTimeInterface
    {
        return $this->creationDatetime;
    }

    public function setCreationDatetime(\DateTimeInterface $creationDatetime): self
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    public function getLoginCount(): ?int
    {
        return $this->loginCount;
    }

    public function setLoginCount(int $loginCount): self
    {
        $this->loginCount = $loginCount;

        return $this;
    }

    public function getLastLoginDatetime(): ?\DateTimeInterface
    {
        return $this->lastLoginDatetime;
    }

    public function setLastLoginDatetime(?\DateTimeInterface $lastLoginDatetime): self
    {
        $this->lastLoginDatetime = $lastLoginDatetime;

        return $this;
    }

    public function incrementLoginCount()
    {
        $this->loginCount++;
    }
}
