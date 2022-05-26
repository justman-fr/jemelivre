<?php

namespace App\DataFixtures\ORM;

use App\DataFixtures\Data\SettingsData;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Pixel\CompanyBundle\Entity\Setting as CompanySetting;
use Pixel\SocialBundle\Entity\Setting;
use Sulu\Bundle\ContactBundle\Entity\Account;
use Sulu\Bundle\ContactBundle\Entity\AccountAddress;
use Sulu\Bundle\ContactBundle\Entity\AccountInterface;
use Sulu\Bundle\ContactBundle\Entity\Address;
use Sulu\Bundle\ContactBundle\Entity\AddressType;
use Sulu\Bundle\ContactBundle\Entity\Contact;
use Sulu\Bundle\ContactBundle\Entity\ContactInterface;
use Sulu\Bundle\FormBundle\Entity\Form;
use Sulu\Bundle\FormBundle\Entity\FormField;
use Sulu\Bundle\MediaBundle\Entity\Collection;
use Sulu\Bundle\MediaBundle\Entity\CollectionInterface;
use Sulu\Bundle\MediaBundle\Entity\CollectionMeta;
use Sulu\Bundle\MediaBundle\Entity\CollectionType;
use Sulu\Bundle\MediaBundle\Entity\File;
use Sulu\Bundle\MediaBundle\Entity\FileVersion;
use Sulu\Bundle\MediaBundle\Entity\FileVersionMeta;
use Sulu\Bundle\MediaBundle\Entity\Media;
use Sulu\Bundle\MediaBundle\Entity\MediaInterface;
use Sulu\Bundle\MediaBundle\Entity\MediaType;
use Sulu\Bundle\MediaBundle\Media\Storage\StorageInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AppFixtures extends Fixture implements OrderedFixtureInterface
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public const LOCALE_FR = 'fr';

    public function load(ObjectManager $manager)
    {
        $collection = $this->loadCollections($manager);
        $this->loadImages($manager, $collection['Pages'], 'pages');
        $this->loadAccounts($manager);
        $this->loadContacts($manager);
        $this->loadFormContact($manager);
        $this->loadSocialSettings($manager);
        $this->loadCompanySettings($manager);

        $manager->flush();
    }


    private function loadCollections(ObjectManager $manager): array
    {
        $collections = [];
        $collections['Pages'] = $this->createCollection(
            $manager,
            ['title' => 'Pages']
        );
        return $collections;
    }

    /**
     * @param mixed[] $data
     */
    private function createCollection(ObjectManager $manager, array $data): CollectionInterface
    {
        $collection = new Collection();

        /** @var CollectionType|null $collectionType */
        $collectionType = $manager->getRepository(CollectionType::class)->find(1);

        if (!$collectionType) {
            throw new \RuntimeException('CollectionType "1" not found. Have you loaded the Sulu fixtures?');
        }

        $collection->setType($collectionType);

        $meta = new CollectionMeta();
        $meta->setLocale(self::LOCALE_FR);
        $meta->setTitle($data['title']);
        $meta->setCollection($collection);

        $collection->addMeta($meta);
        $collection->setDefaultMeta($meta);

        $manager->persist($collection);
        $manager->persist($meta);

        return $collection;
    }

    private function loadImages(ObjectManager $manager, CollectionInterface $collection, $directory): array
    {
        $media = [];
        $finder = new Finder();
        foreach($finder->files()->in(__DIR__ . '/../images/' . $directory) as $file){
            $media[pathinfo($file, \PATHINFO_BASENAME)] = $this->createMedia($manager, $collection, $file);
        }
        return $media;
    }

    private function createMedia(
        ObjectManager       $manager,
        CollectionInterface $collection,
        SplFileInfo         $fileInfo
    ): MediaInterface
    {
        $fileName = $fileInfo->getBasename();
        $title = $fileInfo->getFilename();
        $uploadedFile = new UploadedFile($fileInfo->getPathname(), $fileName);

        $storageOptions = $this->storage->save(
            $uploadedFile->getPathname(),
            $fileName
        );

        $mediaType = $manager->getRepository(MediaType::class)->find(2);
        if (!$mediaType instanceof MediaType) {
            throw new \RuntimeException('MediaType "2" not found. Have you loaded the Sulu fixtures?');
        }

        $media = new Media();

        $file = new File();
        $file->setVersion(1)
            ->setMedia($media);

        $media->addFile($file)
            ->setType($mediaType)
            ->setCollection($collection);

        $fileVersion = new FileVersion();
        $fileVersion->setVersion($file->getVersion())
            ->setSize($uploadedFile->getSize())
            ->setName($fileName)
            ->setStorageOptions($storageOptions)
            ->setMimeType($uploadedFile->getMimeType() ?: 'image/jpeg')
            ->setFile($file);

        $file->addFileVersion($fileVersion);

        $fileVersionMeta = new FileVersionMeta();
        $fileVersionMeta->setTitle($title)
            ->setDescription('')
            ->setLocale(self::LOCALE_FR)
            ->setFileVersion($fileVersion);

        $fileVersion->addMeta($fileVersionMeta)
            ->setDefaultMeta($fileVersionMeta);

        $manager->persist($fileVersionMeta);
        $manager->persist($fileVersion);
        $manager->persist($media);

        return $media;
    }

    /**
     * @return AccountInterface[]
     */
    private function loadAccounts(ObjectManager $manager): array
    {
        $accounts = [];

        $accounts[] = $this->createAccount($manager, [
            'name' => "Nom du projet",
            'email' => 'contact@mon-projet.com',
            'phone' => '04 75 90 15 40',
            'address' => [
                'title' => 'Nom du projet',
                'street' => 'Place de la Mairie',
                'number' => '7a',
                'zip' => '26000',
                'city' => 'Valence',
                'countryCode' => 'FR',
            ],
        ]);

        return $accounts;
    }

    /**
     * @param mixed[] $data
     */
    private function createAccount(ObjectManager $manager, array $data): AccountInterface
    {
        $account = new Account();
        $account->setName($data['name']);
        $account->setMainEmail($data['email']);
        $account->setMainPhone($data['phone']);




        $manager->persist($account);

        return $account;
    }

    /**
     * @return ContactInterface[]
     */
    private function loadContacts(ObjectManager $manager): array
    {
        $contacts = [];

        $contacts[] = $this->createContact(
            $manager,
            ['firstName' => 'Pixel', 'lastName' => 'Dev']
        );

        return $contacts;
    }

    /**
     * @param mixed[] $data
     */
    private function createContact(ObjectManager $manager, array $data): ContactInterface
    {
        $contact = new Contact();
        $contact->setFirstName($data['firstName']);
        $contact->setLastName($data['lastName']);

        $manager->persist($contact);

        return $contact;
    }

    /**
     * @param ObjectManager $manager
     * @return void
     */
    private function loadFormContact(ObjectManager $manager): void
    {
        $data = ["name" => "Formulaire de contact"];

        $fields = [

            [
                'type' => 'firstName',
                'title' => 'Prénom',
                'width' => 'half',
                'required' => true,
            ],
            [
                'type' => 'lastName',
                'title' => 'Nom',
                'width' => 'half',
                'required' => true,
            ],
            [
                'type' => 'textarea',
                'title' => 'Message',
                'width' => 'full',
            ],
            [
                'type' => 'phone',
                'title' => 'Téléphone',
                'width' => 'full'
            ],
            [
                'type' => 'email',
                'title' => 'E-mail',
                'width' => 'full',
                'required' => true
            ],
            [
                'type' => 'checkbox',
                'title' => "En utilisant ce formulaire, j'accepte le stockage et la gestion de mes données selon la politique de confidentialité du site",
                'width' => 'full',
                'required' => true
            ]
        ];

        $this->createForm($manager, $data, $fields);
    }

    /**
     * @param ObjectManager $manager
     * @param array $data
     * @param array $fields
     * @return void
     */

    private function createForm(ObjectManager $manager, array $data, array $fields)
    {
        $form = new Form();
        $form->setDefaultLocale(self::LOCALE_FR);

        $formTranslation = $form->getTranslation(self::LOCALE_FR, true);
        $formTranslation->setForm($form);
        $formTranslation->setLocale(self::LOCALE_FR);
        $formTranslation->setTitle($data['name']);

        foreach ($fields as $orderNumber => $field) {
            if (!isset($existFieldKeys[$field['type']])) {
                $existFieldKeys[$field['type']] = 0;
            }

            ++$existFieldKeys[$field['type']];

            $fieldKey = $field['type'];

            if (1 !== $existFieldKeys[$field['type']]) {
                $fieldKey .= $existFieldKeys[$field['type']];
            }

            $this->addField(
                $form,
                [self::LOCALE_FR],
                $field['type'],
                $fieldKey,
                $orderNumber,
                $field['width'] ?? 'full',
                $field['required'] ?? false,
                $field['options'] ?? [],
                $field['title'],
                $manager
            );
        }

        $manager->persist($form);
        $manager->flush();

    }

    private function addField(
        Form          $form,
        array         $locales,
        string        $fieldType,
        string        $fieldKey,
        int           $orderNumber,
        string        $width = 'full',
        bool          $required = false,
        array         $options = [],
        string        $title,
        ObjectManager $manager
    )
    {
        $formField = $form->getField($fieldKey) ?: new FormField();
        $formField->setForm($form);
        $formField->setDefaultLocale(current($locales));
        $formField->setRequired($required);
        $formField->setType($fieldType);
        $formField->setWidth($width);
        $formField->setOrder($orderNumber);
        $formField->setKey($fieldKey);

        foreach ($locales as $locale) {
            $formFieldTranslation = $formField->getTranslation($locale, true);
            $formFieldTranslation->setTitle(ucfirst($title));
            $formFieldTranslation->setOptions($options);
        }

        $manager->persist($formField);
    }

    private function loadSocialSettings(ObjectManager $manager): void
    {
        $datas = new SettingsData();
        foreach($datas->getSocialDatas() as $setting){
            $this->createSocialSettings($manager, $setting);
        }
    }

    private function loadCompanySettings(ObjectManager $manager): void
    {
        $datas = new SettingsData();
        foreach($datas->getCompanyData() as $setting){
            $this->createCompanySettings($manager, $setting);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return \PHP_INT_MAX;
    }

    private function createSocialSettings(ObjectManager $manager, array $data): void
    {
        $setting = new Setting();
        $setting->setSocialMedias($data['socialMedias']);
        $manager->persist($setting);
    }

    private function createCompanySettings(ObjectManager $manager, array $data): void
    {
        $setting = new CompanySetting();
        $setting->setName($data['name']);
        $setting->setEmail($data['email']);
        $setting->setPhoneNumber($data['phoneNumber']);
        $setting->setAddress($data['address']);
        $setting->setPlaceId($data['placeId']);
        $setting->setOpeningHours($data['openingHours']);
        $manager->persist($setting);
    }
}
