<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FollowingRepository")
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="username", columns={"account_id", "username"}),@ORM\UniqueConstraint(name="account_id", columns={"account_id", "pk"})})
 */
class Following
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer", options={"unsigned":true})
     */
    private $id;

    /**
     * @ORM\Column(name="pk", type="string", length=191)
     */
    private $accountId;

    /**
     * @ORM\Column(type="string", length=191)
     */
    private $username;

    /**
     * @ORM\Column(type="datetime")
     */
    private $creationDatetime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deletionDatetime;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFrozen = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isReciprocal = false;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Account")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

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

    public function getCreationDatetime(): ?\DateTimeInterface
    {
        return $this->creationDatetime;
    }

    public function setCreationDatetime(\DateTimeInterface $creationDatetime): self
    {
        $this->creationDatetime = $creationDatetime;

        return $this;
    }

    public function getDeletionDatetime(): ?\DateTimeInterface
    {
        return $this->deletionDatetime;
    }

    public function setDeletionDatetime(?\DateTimeInterface $deletionDatetime): self
    {
        $this->deletionDatetime = $deletionDatetime;

        return $this;
    }

    public function isFrozen(): ?bool
    {
        return $this->isFrozen;
    }

    public function setIsFrozen(bool $isFrozen): self
    {
        $this->isFrozen = $isFrozen;

        return $this;
    }

    /**
     * @return mixed
     */
    public function isReciprocal(): ?bool
    {
        return $this->isReciprocal;
    }

    /**
     * @param mixed $isReciprocal
     * @return $this
     */
    public function setIsReciprocal(bool $isReciprocal)
    {
        $this->isReciprocal = $isReciprocal;
        return $this;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @param Account $account
     * @return $this
     */
    public function setAccount(Account $account)
    {
        $this->account = $account;
        return $this;
    }
}
