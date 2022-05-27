<?php

declare(strict_types=1);

namespace App\Preview;

use App\Entity\Author;
use App\Repository\AuthorsRepository;
use Sulu\Bundle\CategoryBundle\Category\CategoryManagerInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\PreviewBundle\Preview\Object\PreviewObjectProviderInterface;

class AuthorObjectProvider implements PreviewObjectProviderInterface
{
    private $authorRepository;
    private $mediaManager;
    private $categoryManager;

    public function __construct(AuthorsRepository $authorRepository, MediaManagerInterface $mediaManager, CategoryManagerInterface $categoryManager)
    {
        $this->authorRepository = $authorRepository;
        $this->mediaManager = $mediaManager;
        $this->categoryManager = $categoryManager;
    }

    public function getObject($id, $locale): Author
    {
        return $this->authorRepository->find((int)$id);
    }

    public function getId($object): int
    {
        return $object->getId();
    }

    /**
     * @param Author $object
     * @param $locale
     * @param array $data
     * @return void
     */
    public function setValues($object, $locale, array $data)
    {
        $coverId = $data['cover']['id'] ?? null;
       // $isActive = $data['isActive'] ?? null;


        $object->setName($data['name']);
        //$object->setIsActive($isActive);
        $object->setRoutePath($data['routePath']);

        $object->setCover($coverId ? $this->mediaManager->getEntityById($coverId) : null);

    }

    public function setContext($object, $locale, array $context)
    {
        if (\array_key_exists('template', $context)) {
            $object->setStructureType($context['template']);
        }

        return $object;
    }

    /**
     * @param Author $object
     * @return string
     */
    public function serialize($object)
    {
        return serialize($object);
    }

    public function deserialize($serializedObject, $objectClass)
    {
        return unserialize($serializedObject);
    }

    public function getSecurityContext($id, $locale): ?string
    {
        // TODO: Implement getSecurityContext() method.
    }
}
