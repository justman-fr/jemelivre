<?php

namespace App\Entity;

use App\Repository\AuthorsRepository;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Sulu\Component\Persistence\Model\AuditableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Component\Persistence\Model\AuditableInterface;

/**
 * @ORM\Entity(repositoryClass=AuthorsRepository::class)
 */
class Author implements AuditableInterface
{
    public const RESOURCE_KEY = "authors";
    public const LIST_KEY = "authors";
    public const SECURITY_CONTEXT = "authors";
    public const FORM_KEY = "author_details";
    public const SEO_FORM_KEY = "seo";
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Serializer\Expose()
     */
    private $id;


    use AuditableTrait;


    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private $name;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private $firstname;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="string", length=255)
     * @Serializer\Expose()
     */
    private $lastname;

    /**
     * @ORM\Column(type="string")
     * @Serializer\Expose()
     */
    private $routePath;

    /**
     * @ORM\ManyToOne(targetEntity=MediaInterface::class)
     * @Serializer\Expose()
     */
    private $cover;

    /**
     * @Assert\NotBlank
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Serializer\Expose()
     */
    private $isActive;

    /**
     * @ORM\Column(type="json", nullable=true)
     * @Serializer\Expose()
     */
    private $seo;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getRoutePath()
    {
        return $this->routePath;
    }

    /**
     * @param mixed $routePath
     */
    public function setRoutePath($routePath): void
    {
        $this->routePath = $routePath;
    }

    /**
     * @return mixed
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param mixed $cover
     */
    public function setCover($cover): void
    {
        $this->cover = $cover;
    }

    /**
     * @return mixed
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @param mixed $firstname
     */
    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    /**
     * @return mixed
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @param mixed $lastname
     */
    public function setLastname($lastname): void
    {
        $this->lastname = $lastname;
    }


    /**
     * @return mixed
     */
    public function isActive()
    {
        return $this->isActive;
    }

    /**
     * @param mixed $isActive
     */
    public function setIsActive(?bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return mixed
     */
    public function getSeo()
    {
        return $this->seo;
    }

    protected function emptySeo(): array
    {
        return [
            "seo" => [
                "title" => "",
                "description" => "",
                "keywords" => "",
                "canonicalUrl" => "",
                "noIndex" => "",
                "noFollow" => "",
                "hideinSitemap" => ""
            ]
        ];
    }

    /**
     * @Serializer\VirtualProperty(name="ext")
     * @return array
     */
    public function getExt(): array
    {
        return ($this->getSeo()) ? ['seo' => $this->getSeo()] : $this->emptySeo();
    }

    /**
     * @param mixed $seo
     */
    public function setSeo($seo): void
    {
        $this->seo = $seo;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description): void
    {
        $this->description = $description;
    }



}
