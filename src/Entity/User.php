<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[UniqueEntity(
	fields: ['email'],
	message: 'Cet email est déjà utilisé.'
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank(
		message: 'Veuillez saisir un email',
    )]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank(
		message: 'Veuillez saisir un mot de passe'
    )]
    #[Assert\Regex(
	    pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*[\d\W]).{8,}$/',
	    message: 'Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, et un chiffre ou caractère spécial.'
    )]
    private ?string $password = null;

	#[ORM\Column(type: Types::TEXT, nullable: true)]
	private ?string $description = null;

    #[ORM\Column]
    #[Assert\NotBlank(
		message: 'Veuillez saisir un nom'
    )]
	#[Assert\Length(
		min: 4,
		max: 255,
		minMessage: 'Le minimum de caractères est de 4 (actuellement {{ value }}).',
		maxMessage: 'Le maximum de caractère est de 255 (actuellement {{ value }}).')]
    private ?string $name = null;

    /**
     * @var Collection<int, Media>
     */
    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $medias;

    #[ORM\Column]
    private ?bool $blocked;

    public function __construct()
    {
        $this->medias = new ArrayCollection();
		$this->blocked = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
	    if ($this->isBlocked()) {
		    $roles[] = 'ROLE_BLOCKED';
	    }else{
		    $roles = array_diff($roles, ['ROLE_BLOCKED']);
	    }

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

	public function getDescription(): ?string
	{
		return $this->description;
	}

	public function setDescription(?string $description): static
	{
		$this->description = $description;

		return $this;
	}

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedias(): Collection
    {
        return $this->medias;
    }

    public function addMedia(Media $media): static
    {
        if (!$this->medias->contains($media)) {
            $this->medias->add($media);
            $media->setUser($this);
        }

        return $this;
    }

    public function removeMedia(Media $media): static
    {
        if ($this->medias->removeElement($media)) {
            // set the owning side to null (unless already changed)
            if ($media->getUser() === $this) {
                $media->setUser(null);
            }
        }

        return $this;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

	public function isBlocked(): bool
	{
		return $this->blocked;
	}

	public function setBlocked(bool $blocked): static
	{
		if ($blocked) {
			if (!in_array('ROLE_BLOCKED', $this->roles, true)) {
				$this->roles[] = 'ROLE_BLOCKED';
			}
			$this->blocked = true;
		} else {
			$this->roles = array_diff($this->roles, ['ROLE_BLOCKED']);
			$this->blocked = false;
		}

		$this->roles = array_values($this->roles);

		return $this;
	}

}
