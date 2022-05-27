<?php

namespace App\Controller\Admin;

use App\Common\DoctrineListRepresentationFactory;
use App\Domain\Event\AuthorCreatedEvent;
use App\Domain\Event\AuthorModifiedEvent;
use App\Domain\Event\AuthorRemovedEvent;
use App\Entity\Author;
use App\Repository\AuthorsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\View\ViewHandlerInterface;
use HandcraftedInTheAlps\RestRoutingBundle\Controller\Annotations\RouteResource;
use Sulu\Bundle\ActivityBundle\Application\Collector\DomainEventCollectorInterface;
use Sulu\Bundle\CategoryBundle\Category\CategoryManagerInterface;
use Sulu\Bundle\MediaBundle\Media\Manager\MediaManagerInterface;
use Sulu\Bundle\RouteBundle\Entity\RouteRepositoryInterface;
use Sulu\Bundle\RouteBundle\Manager\RouteManagerInterface;
use Sulu\Bundle\TrashBundle\Application\TrashManager\TrashManagerInterface;
use Sulu\Component\Rest\AbstractRestController;
use Sulu\Component\Rest\Exception\EntityNotFoundException;
use Sulu\Component\Rest\Exception\RestException;
use Sulu\Component\Rest\RequestParametersTrait;
use Sulu\Component\Security\SecuredControllerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @RouteResource("author")
 */
class AuthorController extends AbstractRestController implements ClassResourceInterface, SecuredControllerInterface
{

    use RequestParametersTrait;

    private $doctrineListRepresentationFactory;
    private $entityManager;
    private $mediaManager;
    private $categoryManager;
    private $webspaceManager;
    private $routeManager;
    private $routeRepository;
    private $authorRepository;
    private $domainEventCollector;
    private $trashManager;

    public function __construct(
        DoctrineListRepresentationFactory $doctrineListRepresentationFactory,
        EntityManagerInterface $entityManager,
        MediaManagerInterface $mediaManager,
        CategoryManagerInterface $categoryManager,
        ViewHandlerInterface $viewHandler,
        WebspaceManagerInterface $webspaceManager,
        RouteManagerInterface $routeManager,
        RouteRepositoryInterface $routeRepository,
        AuthorsRepository $authorRepository,
        DomainEventCollectorInterface $domainEventCollector,
        TrashManagerInterface $trashManager,
        ?TokenStorageInterface $tokenStorage = null
    ) {
        $this->doctrineListRepresentationFactory = $doctrineListRepresentationFactory;
        $this->entityManager = $entityManager;
        $this->mediaManager = $mediaManager;
        $this->categoryManager = $categoryManager;
        $this->webspaceManager = $webspaceManager;
        $this->routeManager = $routeManager;
        $this->routeRepository = $routeRepository;
        $this->authorRepository = $authorRepository;
        $this->domainEventCollector = $domainEventCollector;
        $this->trashManager = $trashManager;

        parent::__construct($viewHandler, $tokenStorage);
    }

    public function cgetAction(): Response
    {
        $listRepresentation = $this->doctrineListRepresentationFactory->createDoctrineListRepresentation(
            Author::RESOURCE_KEY
        );

        return $this->handleView($this->view($listRepresentation));
    }

    protected function load(int $id): ?Author
    {
        return $this->authorRepository->find($id);
    }

    protected function save(Author $author): void
    {
        $this->authorRepository->save($author);
    }

    protected function create(): Author
    {
        return $this->authorRepository->create();
    }

    public function getAction(int $id): Response
    {
        $programme = $this->load($id);
        if (!$programme) {
            throw new NotFoundHttpException();
        }

        return $this->handleView($this->view($programme));
    }

    public function putAction(Request $request, int $id): Response
    {
        $author = $this->load($id);
        if (!$author) {
            throw new NotFoundHttpException();
        }

        $data = $request->request->all();
        $this->mapDataToEntity($data, $author);
        $this->updateRoutesForEntity($author);
        $this->domainEventCollector->collect(
            new AuthorModifiedEvent($author, $data)
        );
        $this->entityManager->flush();
        $this->save($author);

        return $this->handleView($this->view($author));
    }

    public function postAction(Request $request): Response
    {
        $author = $this->create();
        $data = $request->request->all();
        $this->mapDataToEntity($data, $author);
        $this->save($author);
        $this->updateRoutesForEntity($author);
        $this->domainEventCollector->collect(
            new AuthorCreatedEvent($author, $data)
        );
        $this->entityManager->flush();

        return $this->handleView($this->view($author, 201));
    }

    public function deleteAction(int $id): Response
    {
        /** @var Author $author */
        $author = $this->entityManager->getRepository(Author::class)->find($id);
        $authorName = $author->getName();
        if($author){
            $this->trashManager->store(Author::RESOURCE_KEY, $author);
            $this->entityManager->remove($author);
            $this->removeRoutesForEntity($author);
            $this->domainEventCollector->collect(
                new AuthorRemovedEvent($id, $authorName)
            );
        }
        $this->entityManager->flush();

        return $this->handleView($this->view(null, 204));
    }

    /**
     * @param mixed[] $data
     */
    protected function mapDataToEntity(array $data, Author $entity): void
    {
        $coverId = $data['cover']['id'] ?? null;
        //$isActive = $data['isActive'] ?? null;


        $entity->setName($data['firstname'].' '.$data['lastname']);
        $entity->setFirstname($data['firstname']);
        $entity->setLastname($data['lastname']);
        //$entity->setIsActive($isActive);
        $entity->setRoutePath($data['routePath']);

        $entity->setCover($coverId ? $this->mediaManager->getEntityById($coverId) : null);

    }

    /**
     * @Rest\Post("/authors/{id}")
     *
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws EntityNotFoundException
     */
    public function postTriggerAction(int $id, Request $request): Response
    {
        $data = $request->request->all();
        $action = $this->getRequestParameter($request, 'action', true);
        //$locale = $this->getRequestParameter($request, 'locale', true);

        try {
            switch ($action) {
                case 'enable':
                    $item = $this->entityManager->getReference(Author::class, $id);
                    $item->setIsActive(true);
                    $this->entityManager->persist($item);
                    $this->domainEventCollector->collect(
                        new AuthorModifiedEvent($item, $data)
                    );
                    $this->entityManager->flush();
                    break;
                case 'disable':
                    $item = $this->entityManager->getReference(Author::class, $id);
                    $item->setIsActive(false);
                    $this->entityManager->persist($item);$this->domainEventCollector->collect(
                        new AuthorModifiedEvent($item, $data)
                    );
                    $this->entityManager->flush();
                    break;
                default:
                    throw new BadRequestHttpException(sprintf('Unknown action "%s".', $action));
            }
        } catch (RestException $exc) {
            $view = $this->view($exc->toArray(), 400);
            return $this->handleView($view);
        }

        return $this->handleView($this->view($item));
    }

    protected function updateRoutesForEntity(Author $entity): void
    {
        // create route for all locales of the application because event entity is not localized
        foreach ($this->webspaceManager->getAllLocales() as $locale) {
            $this->routeManager->createOrUpdateByAttributes(
                Author::class,
                (string)$entity->getId(),
                $locale,
                $entity->getRoutePath()
            );
        }
    }

    protected function removeRoutesForEntity(Author $entity): void
    {
        // remove route for all locales of the application because event entity is not localized
        foreach ($this->webspaceManager->getAllLocales() as $locale) {
            $routes = $this->routeRepository->findAllByEntity(
                Author::class,
                (string)$entity->getId(),
                $locale
            );

            foreach ($routes as $route) {
                $this->routeRepository->remove($route);
            }
        }
    }

    public function getSecurityContext(): string
    {
        return Author::SECURITY_CONTEXT;
    }
}
