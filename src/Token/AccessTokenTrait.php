<?php


namespace Bytes\ResponseBundle\Token;


use Bytes\ResponseBundle\Entity\CreatedUpdatedTrait;
use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Trait AccessTokenTrait
 * @package Bytes\ResponseBundle\Token
 */
trait AccessTokenTrait
{
    use CreatedUpdatedTrait;

    /**
     * @var Ulid
     * @ORM\Id
     * @ORM\Column(type="ulid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UlidGenerator::class)
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
     * @var \DateInterval|null
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
     * @var string|array|null
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
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tokenSource;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $class;

    /**
     * @var UserInterface|null
     */
    private $user;

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
     * @return \DateInterval|null
     */
    public function getExpiresIn(): ?\DateInterval
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
            $expiresIn = ComparableDateInterval::secondsToInterval($expiresIn);
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
            return empty($this->expiresAt) ? null : \DateTimeImmutable::createFromInterface($this->expiresAt);
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
     * @return TokenSource|null
     */
    public function getTokenSource(): ?TokenSource
    {
        try {
            return TokenSource::from($this->tokenSource);
        } catch (\TypeError $exception)
        {
            return null;
        }
    }

    /**
     * @param TokenSource|string|null $tokenSource
     * @return $this
     */
    public function setTokenSource(TokenSource|string|null $tokenSource): self
    {
        if(!empty($tokenSource))
        {
            if($tokenSource instanceof TokenSource)
            {
                $tokenSource = $tokenSource->value;
            }
        }
        $this->tokenSource = $tokenSource;
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
     * @return string|null
     */
    public function getIdentifier(): ?string
    {
        return $this->class;
    }

    /**
     * @param string|null $class
     * @return $this
     */
    public function setIdentifier(?string $class = null): self
    {
        $this->class = $class ?? static::class;
        return $this;
    }
}