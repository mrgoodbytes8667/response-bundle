<?php


namespace Bytes\ResponseBundle\Token;


use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait AccessTokenTrait
 * @package Bytes\ResponseBundle\Token
 */
trait AccessTokenTrait
{
    /**
     * @var Ulid
     * @ORM\Id()
     * @ORM\Column(type="ulid")
     * @ORM\GeneratedValue()
     */
    protected $id;

    /**
     * User access token
     * @var string|null
     * @ORM\Column(type="string", length=512)
     */
    private $accessToken;

    /**
     * Refresh token
     * @var string|null
     * @ORM\Column(type="string", length=512, nullable=true)
     */
    private $refreshToken;

    /**
     * Time (in seconds) until the access token expires
     * @var int|null
     * @ORM\Column(type="dateinterval")
     */
    private $expiresIn;

    /**
     * (Calculated) datetime when the access token expires
     * @var \DateTimeInterface|null
     * @ORM\Column(type="datetime")
     */
    private $expiresAt;

    /**
     * Space separated scopes
     * @var string|null
     * @ORM\Column(type="string", length=255)
     * @Assert\NotNull()
     */
    private $scope;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tokenType;

    /**
     * @var UserInterface|null
     */
    private $user;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface
     * @ORM\Column(type="datetime")
     */
    protected $updatedAt;

    /**
     * @return Ulid
     */
    public function getId(): Ulid
    {
        return $this->id;
    }

    /**
     * @param bool $masked
     * @return string|null
     */
    public function getAccessToken(bool $masked = false): ?string
    {
        if($masked && class_exists('\Bytes\StringMaskBundle\Twig\StringMaskRuntime', false)) {
            return \Bytes\StringMaskBundle\Twig\StringMaskRuntime::getMaskedString($this->accessToken);
        } else {
            return $this->accessToken;
        }
    }

    /**
     * @param string|null $accessToken
     * @return $this
     */
    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    /**
     * @param bool $masked
     * @return string|null
     */
    public function getRefreshToken(bool $masked = false): ?string
    {
        if($masked && class_exists('\Bytes\StringMaskBundle\Twig\StringMaskRuntime', false)) {
            return \Bytes\StringMaskBundle\Twig\StringMaskRuntime::getMaskedString($this->refreshToken);
        } else {
            return $this->refreshToken;
        }
    }

    /**
     * @param string|null $refreshToken
     * @return $this
     */
    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    /**
     * @param int|\DateInterval|null $expiresIn
     * @return $this
     * @throws \Exception
     */
    public function setExpiresIn(int|\DateInterval|null $expiresIn): self
    {
        if (!empty($expiresIn) && is_numeric($expiresIn)) {
            $expiresIn = new \DateInterval(sprintf('PT%dS', $expiresIn));
            $now = new \DateTimeImmutable();
            $this->setExpiresAt($now->add($expiresIn));
        }
        $this->expiresIn = $expiresIn;
        return $this;
    }

    /**
     * @return \DateTimeInterface|null
     */
    public function getExpiresAt(): ?\DateTimeImmutable
    {
        if($this->expiresAt instanceof \DateTimeImmutable)
        {
            return $this->expiresAt;
        } else {
            return \DateTimeImmutable::createFromInterface($this->expiresAt);
        }
    }

    /**
     * @param \DateTimeInterface|null $expiresAt
     * @return $this
     */
    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getScope(): string
    {
        return $this->scope ?? '';
    }

    /**
     * @param string|array $scope
     * @return $this
     */
    public function setScope(string|array $scope = ''): self
    {
        if(is_array($scope))
        {
            $scope = implode(' ', $scope);
        }
        $this->scope = $scope;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    /**
     * @param string|null $tokenType
     * @return $this
     */
    public function setTokenType(?string $tokenType): self
    {
        $this->tokenType = $tokenType;
        return $this;
    }

    /**
     * @return UserInterface|null
     */
    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @param UserInterface|null $user
     * @return $this
     */
    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt(): \DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     * @return $this
     */
    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


}