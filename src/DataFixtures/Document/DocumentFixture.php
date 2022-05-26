<?php

namespace App\DataFixtures\Document;

use App\DataFixtures\Data\HomepageData;
use App\DataFixtures\Data\PagesData;
use App\DataFixtures\ORM\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use OutOfBoundsException;
use Sulu\Bundle\DocumentManagerBundle\DataFixtures\DocumentFixtureInterface;
use Sulu\Bundle\PageBundle\Document\PageDocument;
use Sulu\Component\Content\Document\RedirectType;
use Sulu\Component\Content\Document\WorkflowStage;
use Sulu\Component\DocumentManager\DocumentManager;
use Sulu\Component\DocumentManager\Exception\DocumentManagerException;
use Sulu\Component\PHPCR\PathCleanupInterface;

class DocumentFixture implements DocumentFixtureInterface
{
    private PathCleanupInterface $pathCleanup;
    private EntityManagerInterface $entityManager;

    public function __construct(PathCleanupInterface $pathCleanup, EntityManagerInterface $entityManager)
    {
        $this->pathCleanup = $pathCleanup;
        $this->entityManager = $entityManager;
    }

    public function getOrder()
    {
        return 10;
    }

    public function load(DocumentManager $documentManager)
    {
        $this->loadPages($documentManager);
        $this->loadHomepage($documentManager);
        $documentManager->flush();
        $documentManager->clear();
    }

    public function loadPages(DocumentManager $documentManager): array
    {
        $datas = new PagesData($this->entityManager);
        $pages = [];
        foreach($datas->getDatas() as $pageData){
            $pages[$pageData['url']] = $this->createPage($documentManager, $pageData);
        }
        return $pages;
    }

    public function createPage(DocumentManager $documentManager, array $data): PageDocument
    {
        $locale = $data['locale'] ?? AppFixtures::LOCALE_FR;

        if (!isset($data['url'])) {
            $url = $this->pathCleanup->cleanup('/' . $data['title']);
            if (isset($data['parent_path'])) {
                $url = mb_substr($data['parent_path'], mb_strlen('/cmf/website/contents')) . $url;
            }

            $data['url'] = $url;
        }

        $extensionData = [
            'seo' => $data['seo'] ?? [],
            'excerpt' => $data['excerpt'] ?? [],
        ];

        unset($data['excerpt']);
        unset($data['seo']);

        /** @var PageDocument $pageDocument */
        $pageDocument = null;

        try {
            if (!isset($data['id']) || !$data['id']) {
                throw new OutOfBoundsException();
            }

            /** @var PageDocument $pageDocument */
            $pageDocument = $documentManager->find($data['id'], $locale);
        }
        catch (DocumentManagerException|OutOfBoundsException $e) {
            /** @var PageDocument $pageDocument */
            $pageDocument = $documentManager->create('page');
        }

        $pageDocument->setNavigationContexts($data['navigationContexts'] ?? []);
        $pageDocument->setLocale($locale);
        $pageDocument->setTitle($data['title']);
        $pageDocument->setResourceSegment($data['url']);
        $pageDocument->setStructureType($data['structureType'] ?? 'default');
        $pageDocument->setWorkflowStage(WorkflowStage::PUBLISHED);
        $pageDocument->getStructure()->bind($data);
        $pageDocument->setAuthor(1);
        $pageDocument->setExtensionsData($extensionData);

        if (isset($data['redirect'])) {
            $pageDocument->setRedirectType(RedirectType::EXTERNAL);
            $pageDocument->setRedirectExternal($data['redirect']);
        }

        $documentManager->persist(
            $pageDocument,
            $locale,
            ['parent_path' => $data['parent_path'] ?? '/cmf/website/contents']
        );
        $documentManager->publish($pageDocument, $locale);

        return $pageDocument;
    }

    public function loadHomepage(DocumentManager $documentManager): void
    {
        $homeDocument = $documentManager->find('/cmf/website/contents', AppFixtures::LOCALE_FR);
        $data = new HomepageData();
        $homeDocument->getStructure()->bind(
            $data->getDatas()
        );
        $documentManager->persist($homeDocument, AppFixtures::LOCALE_FR);
        $documentManager->publish($homeDocument, AppFixtures::LOCALE_FR);
    }
}
