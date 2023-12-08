<?php

namespace Bytes\ResponseBundle\Token;

use Bytes\ResponseBundle\Entity\CreatedUpdatedTrait;
use Bytes\ResponseBundle\Enums\TokenSource;
use Bytes\ResponseBundle\Objects\ComparableDateInterval;
use Bytes\StringMaskBundle\Twig\StringMaskRuntime;
use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\IdGenerator\UlidGenerator;
use Symfony\Bridge\Doctrine\Types\UlidType;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Uid\Ulid;
use Symfony\Component\Validator\Constraints as Assert;
use ValueError;

/**
 * Trait AccessTokenTrait.
 *
 * @example @property ?\Symfony\Component\Uid\Ulid $id
 */
trait AccessTokenTrait
{
    use CreatedUpdatedTrait;

    #[ORM\Id]
    #[ORM\Column(type: UlidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: UlidGenerator::class)]
    protected ?Ulid $id = null;

    /**
     * User access token.
     */
    #[ORM\Column(type: 'string', length: 512)]
    private ?string $accessToken = null;

    /**
     * Refresh token.
     */
    #[ORM\Column(type: 'string', length: 512, nullable: true)]
    private ?string $refreshToken = null;

    /**
     * Time (in seconds) until the access token expires.
     *
     * @var DateInterval|null
     */
    #[ORM\Column(type: 'dateinterval')]
    private $expiresIn;

    /**
     * (Calculated) datetime when the access token expires.
     */
    #[ORM\Column(type: 'datetime')]
    private ?DateTimeInterface $expiresAt = null;

    /**
     * Space separated scopes.
     *
     * @var string|array|null
     */
    #[Assert\AtLeastOneOf([
        new Assert\Blank(),
        new Assert\NotNull(),
        new Assert\Length(max: 255),
    ])]
    #[ORM\Column(type: 'string', length: 255)]
    private ?string $scope = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $tokenType = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $tokenSource = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $class = null;

    private ?UserInterface $user = null;

    public function getId(): Ulid
    {
        return $this->id;
    }

    public function getAccessToken(bool $masked = false): ?string
    {
        if ($masked && class_exists('\Bytes\StringMaskBundle\Twig\StringMaskRuntime', false)) {
            return StringMaskRuntime::getMaskedString($this->accessToken);
        } else {
            return $this->accessToken;
        }
    }

    /**
     * @return $this
     */
    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(bool $masked = false): ?string
    {
        if ($masked && class_exists('\Bytes\StringMaskBundle\Twig\StringMaskRuntime', false)) {
            return StringMaskRuntime::getMaskedString($this->refreshToken);
        } else {
            return $this->refreshToken;
        }
    }

    /**
     * @return $this
     */
    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getExpiresIn(): ?DateInterval
    {
        return $this->expiresIn;
    }

    /**
     * @return $this
     *
     * @throws Exception
     */
    public function setExpiresIn(int|DateInterval|null $expiresIn): self
    {
        if (!empty($expiresIn) && is_numeric($expiresIn)) {
            $expiresIn = ComparableDateInterval::secondsToInterval($expiresIn);
            $now = new DateTimeImmutable();
            $this->setExpiresAt($now->add($expiresIn));
        }

        $this->expiresIn = $expiresIn;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getExpiresAt(): ?DateTimeImmutable
    {
        if ($this->expiresAt instanceof DateTimeImmutable) {
            return $this->expiresAt;
        } else {
            return empty($this->expiresAt) ? null : DateTimeImmutable::createFromInterface($this->expiresAt);
        }
    }

    /**
     * @return $this
     */
    public function setExpiresAt(?DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    public function getScope(): string
    {
        return $this->scope ?? '';
    }

    /**
     * @return $this
     */
    public function setScope(string|array|null $scope = ''): self
    {
        if (is_array($scope)) {
            $scope = implode(' ', $scope);
        }

        $this->scope = $scope ?? '';

        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    /**
     * @return $this
     */
    public function setTokenType(?string $tokenType): self
    {
        $this->tokenType = $tokenType;

        return $this;
    }

    public function getTokenSource(): ?TokenSource
    {
        if (is_null($this->tokenSource)) {
            return null;
        }

        try {
            return TokenSource::from($this->tokenSource);
        } catch (ValueError $exception) {
            return null;
        }
    }

    /**
     * @return $this
     */
    public function setTokenSource(TokenSource|string|null $tokenSource): self
    {
        if (!empty($tokenSource) && $tokenSource instanceof TokenSource) {
            $tokenSource = $tokenSource->value;
        }

        $this->tokenSource = $tokenSource;

        return $this;
    }

    public function getUser(): ?UserInterface
    {
        return $this->user;
    }

    /**
     * @return $this
     */
    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getIdentifier(): ?string
    {
        return $this->class;
    }

    /**
     * @return $this
     */
    public function setIdentifier(string $class = null): self
    {
        $this->class = $class ?? static::class;

        return $this;
    }
}
