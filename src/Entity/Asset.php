<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AssetRepository")
 */
class Asset
{
    public const AVAILABLE_CURRENCIES = ['BTC', 'ETH', 'IOTA'];

    /**
     * !!! Important !!!
     * Must be nullable because of PHP 7.4.2
     * https://github.com/doctrine/orm/issues/7999
     *
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private ?string $uid = null;

    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private string $label;

    /**
     * @Assert\NotBlank()
     * @Assert\Expression(
     *     "this.getCurrency() in this.getAvailableCurrencies()",
     *     message="This currency is not available."
     * )
     *
     * @ORM\Column(type="string", length=4)
     */
    private string $currency;

    /**
     * @Assert\NotBlank()
     * @Assert\Positive
     *
     * @ORM\Column(type="decimal", precision=41, scale=20, options={"unsigned" = true})
     */
    private float $value;

    /**
     * @Assert\Positive
     *
     * @ORM\Column(type="decimal", precision=21, scale=2, options={"unsigned" = true}, nullable=true)
     */
    private ?float $valueInUSD = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="assets", cascade={"persist"})
     */
    private User $user;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int|null $id
     *
     * @return self
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @param string|null $uid
     *
     * @return self
     */
    public function setUid(?string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return self
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     *
     * @return self
     */
    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param float $value
     *
     * @return self
     */
    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getValueInUSD(): ?float
    {
        return $this->valueInUSD;
    }

    /**
     * @param float|null $valueInUSD
     *
     * @return self
     */
    public function setValueInUSD(?float $valueInUSD): self
    {
        $this->valueInUSD = $valueInUSD;

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableCurrencies(): array
    {
        return self::AVAILABLE_CURRENCIES;
    }
}
