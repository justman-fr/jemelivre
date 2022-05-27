<?php

namespace App\Routing;

use App\Controller\Admin\AuthorController;
use App\Controller\Website\ProgrammeController;
use App\Entity\Author;
use App\Repository\AuthorsRepository;
use Sulu\Bundle\RouteBundle\Routing\Defaults\RouteDefaultsProviderInterface;

class AuthorRouteDefaultsProvider implements RouteDefaultsProviderInterface
{
    private $authorRepository;

    public function __construct(AuthorsRepository $authorRepository) {
        $this->authorRepository = $authorRepository;
    }

    /**
     * @return mixed[]
     */
    public function getByEntity($entityClass, $id, $locale, $object = null)
    {
        return [
            '_controller' => AuthorController::class . '::indexAction',
            'programme' => $object ?: $this->authorRepository->find($id),
        ];
    }

    public function isPublished($entityClass, $id, $locale)
    {
        return true;
    }

    public function supports($entityClass)
    {
        return Author::class === $entityClass;
    }
}
