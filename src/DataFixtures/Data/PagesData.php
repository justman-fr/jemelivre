<?php

namespace App\DataFixtures\Data;

use App\DataFixtures\ORM\AppFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use RuntimeException;
use Sulu\Bundle\FormBundle\Entity\FormTranslation;
use Sulu\Bundle\MediaBundle\Entity\Media;

class PagesData
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getDatas(): array
    {
        $pages = [];
        $pages[] = [
            'locale' => AppFixtures::LOCALE_FR,
            'title' => 'Par genre',
            'url' => '/genre-litteraire',
            'blocks' => [
                [
                    'type' => 'text',
                    'heading' => 'h2',
                    'text' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum posuere vehicula urna, vitae sollicitudin massa eleifend vel.</p>
                    <p>Nunc id quam enim. Etiam vitae nisi ut urna blandit viverra sed eget purus. Mauris quis velit quis magna gravida sagittis. Cras scelerisque faucibus turpis a ultricies. Sed nec tellus quam.</p>"
                ]
            ],
            'valeurs' => [
                [
                    'type' => 'text_image',
                    'titre' => 'Lorem',
                    'contenu' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum posuere vehicula urna, vitae sollicitudin massa eleifend vel.</p>
                    <p>Nunc id quam enim. Etiam vitae nisi ut urna blandit viverra sed eget purus. Mauris quis velit quis magna gravida sagittis. Cras scelerisque faucibus turpis a ultricies. Sed nec tellus quam.</p>"
                ],
                [
                    'type' => 'text_image',
                    'titre' => 'Lorem #2',
                    'contenu' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum posuere vehicula urna, vitae sollicitudin massa eleifend vel.</p>"
                ]
            ],
            'navigationContexts' => ['main'],
            'structureType' => 'qui-sommes-nous'
        ];
        $pages[] = [
            'locale' => AppFixtures::LOCALE_FR,
            'title' => "Sous-menu",
            'url' => "/genre-litteraire/sous-menu",
            'parent_path' => "/cmf/website/contents/par-genre",
            'article' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer a sollicitudin magna. Donec efficitur purus sed purus gravida, eu suscipit justo lobortis. Curabitur vel mollis ex.</p>",
            'navigationContexts' => ['main'],
            'structureType' => 'default'
        ];
        $pages[] = [
            'locale' => AppFixtures::LOCALE_FR,
            'title' => 'Auteurs',
            'url' => '/auteurs',
            'contentPresentation' => "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum posuere vehicula urna, vitae sollicitudin massa eleifend vel.</p>
            <p>Nunc id quam enim. Etiam vitae nisi ut urna blandit viverra sed eget purus. Mauris quis velit quis magna gravida sagittis. Cras scelerisque faucibus turpis a ultricies. Sed nec tellus quam.</p>",
            'imagePresentation' => [
                'id' => [$this->getMediaId('no-image.jpg')]
            ],
            'coord' => "<h2>Fixture SARL</h2>
            <p>7 rue du test<br> 26200 Montélimar</p>
            <p><strong>Tel :</strong> 01.02.03.04.05</p>",
            'form' => $this->getFormId('Formulaire de contact'),
            'navigationContexts' => ['main'],
            'structureType' => 'contact'
        ];
        return $pages;
    }

    private function getMediaId(string $name): int
    {
        try {
            $id = $this->entityManager->createQueryBuilder()
                ->from(Media::class, 'media')
                ->select('media.id')
                ->innerJoin('media.files', 'file')
                ->innerJoin('file.fileVersions', 'fileVersion')
                ->where('fileVersion.name = :name')
                ->setMaxResults(1)
                ->setParameter('name', $name)
                ->getQuery()->getSingleScalarResult();

            return (int)$id;
        }
        catch (NonUniqueResultException $e) {
            throw new RuntimeException(sprintf('Too many images with the name "%s" found.', $name), 0, $e);
        }
    }

    private function getFormId(string $name): int
    {
        try {
            $id = $this->entityManager->createQueryBuilder()
                ->from(FormTranslation::class, 'form')
                ->select('form.id')
                ->where('form.title = :name')
                ->setMaxResults(1)
                ->setParameter('name', $name)
                ->getQuery()->getSingleScalarResult();

            return (int)$id;
        }
        catch (NonUniqueResultException $e) {
            throw new RuntimeException(sprintf('No form found "%s" found.', $name), 0, $e);
        }
    }
}
