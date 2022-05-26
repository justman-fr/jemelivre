<?php

namespace App\Build;

use Doctrine\Persistence\ObjectManager;
use Massive\Bundle\BuildBundle\Build\BuilderContext;
use Massive\Bundle\BuildBundle\Build\BuilderInterface;
use Sulu\Bundle\SecurityBundle\Build\UserBuilder as SuluUserBuilder;
use Sulu\Bundle\SecurityBundle\Entity\User;
use Sulu\Bundle\SecurityBundle\Entity\UserSetting;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserBuilder implements BuilderInterface, ContainerAwareInterface
{
    /**
     * @var SuluUserBuilder
     */
    private $decoratedUserBuilder;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(SuluUserBuilder $decoratedUserBuilder, ContainerInterface $container = null)
    {
        $this->decoratedUserBuilder = $decoratedUserBuilder;
        $this->setContainer($container);
    }

    /**
     * Sets the container.
     */
    public function setContainer(ContainerInterface $container = null): void
    {
        if (null !== $container) {
            $this->container = $container;
            $this->decoratedUserBuilder->setContainer($container);
        }
    }

    public function getName()
    {
        return $this->decoratedUserBuilder->getName();
    }

    /**
     * @return string[]
     */
    public function getDependencies()
    {
        return $this->decoratedUserBuilder->getDependencies();
    }

    public function build(): void
    {
        $this->decoratedUserBuilder->build();

        $manager = $this->getManager();
        $userRepository = $manager->getRepository(User::class);
        $userSettingRepository = $manager->getRepository(UserSetting::class);

        /** @var User|null $user */
        $user = $userRepository->findOneBy(['username' => 'admin']);

        if (null === $user) {
            return;
        }

        /** @var UserSetting|null $userSetting */
        $userSetting = $userSettingRepository->findOneBy([
            'key' => 'sulu_admin.application.navigation_pinned',
            'user' => $user->getId(),
        ]);

        if (null === $userSetting) {
            $userSetting = new UserSetting();
            $userSetting->setKey('sulu_admin.application.navigation_pinned');
            $userSetting->setUser($user);
        }

        $userSetting->setValue('true');

        $manager->persist($userSetting);
        $manager->flush();
    }

    private function getManager(): ObjectManager
    {
        return $this->container->get('doctrine')->getManager();
    }

    public function setContext(BuilderContext $context): void
    {
        $this->decoratedUserBuilder->setContext($context);
    }
}
