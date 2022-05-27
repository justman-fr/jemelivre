<?php

namespace App\Admin;

use App\Entity\Author;
use App\Entity\Author as AuthorEntity;
use Sulu\Bundle\ActivityBundle\Infrastructure\Sulu\Admin\View\ActivityViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\Admin;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItem;
use Sulu\Bundle\AdminBundle\Admin\Navigation\NavigationItemCollection;
use Sulu\Bundle\AdminBundle\Admin\View\TogglerToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ToolbarAction;
use Sulu\Bundle\AdminBundle\Admin\View\ViewBuilderFactoryInterface;
use Sulu\Bundle\AdminBundle\Admin\View\ViewCollection;
use Sulu\Component\Security\Authorization\PermissionTypes;
use Sulu\Component\Security\Authorization\SecurityCheckerInterface;
use Sulu\Component\Webspace\Manager\WebspaceManagerInterface;

class AuthorAdmin extends Admin
{
    public const LIST_VIEW = "author.authors_list";
    public const ADD_FORM_VIEW = "author.author_add";
    public const ADD_DETAILS_FORM_VIEW = "author.author_add_form_details";
    public const EDIT_FORM_VIEW = "author.author_edit_form";
    public const EDIT_DETAILS_FORM_VIEW = "author.author_edit_form_details";
    public const EDIT_SEO_FORM_VIEW = "author.author_edit_form_seo";


    private $viewBuilderFactory;
    private $securityChecker;
    private $webspaceManager;
    private $activityViewBuilderFactory;

    public function __construct(
        ViewBuilderFactoryInterface $viewBuilderFactory,
        SecurityCheckerInterface $securityChecker,
        WebspaceManagerInterface $webspaceManager,
        ActivityViewBuilderFactoryInterface $activityViewBuilderFactory
    ){
        $this->viewBuilderFactory = $viewBuilderFactory;
        $this->securityChecker = $securityChecker;
        $this->webspaceManager = $webspaceManager;
        $this->activityViewBuilderFactory = $activityViewBuilderFactory;
    }

    public function configureNavigationItems(NavigationItemCollection $navigationItemCollection): void
    {
        if($this->securityChecker->hasPermission(AuthorEntity::SECURITY_CONTEXT, PermissionTypes::EDIT)){
            $navigationItem = new NavigationItem("Auteurs");
            $navigationItem->setView(static::LIST_VIEW);
            $navigationItem->setIcon("fa-users");
            $navigationItem->setPosition(30);
            $navigationItemCollection->add($navigationItem);
        }
    }

    public function configureViews(ViewCollection $viewCollection): void
    {
        $locales = $this->webspaceManager->getAllLocales();
        $listToolbarAction = [];
        $formToolbarAction = [];

        if($this->securityChecker->hasPermission(AuthorEntity::SECURITY_CONTEXT, PermissionTypes::ADD)){
            $listToolbarAction[] = new ToolbarAction("sulu_admin.add");
        }
        if($this->securityChecker->hasPermission(AuthorEntity::SECURITY_CONTEXT, PermissionTypes::EDIT)){
            $formToolbarAction[] = new ToolbarAction("sulu_admin.save");
            $formToolbarAction[] = new TogglerToolbarAction("Activer ?",
                "isActive",
                "enable",
                "disable"
            );
        }
        if($this->securityChecker->hasPermission(AuthorEntity::SECURITY_CONTEXT, PermissionTypes::DELETE)){
            $listToolbarAction[] = new ToolbarAction("sulu_admin.delete");
            $formToolbarAction[] = new ToolbarAction("sulu_admin.delete");
        }
        if($this->securityChecker->hasPermission(AuthorEntity::SECURITY_CONTEXT, PermissionTypes::VIEW)){
            $listToolbarAction[] = new ToolbarAction("sulu_admin.export");
        }

        if($this->securityChecker->hasPermission(AuthorEntity::SECURITY_CONTEXT, PermissionTypes::EDIT)){
            $viewCollection->add(
                $this->viewBuilderFactory->createListViewBuilder(static::LIST_VIEW, "/authors/:locale")
                    ->setResourceKey(AuthorEntity::RESOURCE_KEY)
                    ->setListKey(AuthorEntity::LIST_KEY)
                    ->setTitle("Auteurs")
                    ->addListAdapters(['table'])
                    ->addLocales($locales)
                    ->setDefaultLocale($locales[0])
                    ->setAddView(static::ADD_FORM_VIEW)
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($listToolbarAction)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::ADD_FORM_VIEW, "/authors/:locale/add")
                    ->setResourceKey(AuthorEntity::RESOURCE_KEY)
                    ->addLocales($locales)
                    ->setBackView(static::LIST_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::ADD_DETAILS_FORM_VIEW, "/details")
                    ->setResourceKey(AuthorEntity::RESOURCE_KEY)
                    ->setFormKey(AuthorEntity::FORM_KEY)
                    ->setTabTitle("sulu_admin.details")
                    ->setEditView(static::EDIT_FORM_VIEW)
                    ->addToolbarActions($formToolbarAction)
                    ->setParent(static::ADD_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createResourceTabViewBuilder(static::EDIT_FORM_VIEW, "/authors/:locale/:id")
                    ->setResourceKey(AuthorEntity::RESOURCE_KEY)
                    ->addLocales($locales)
                    ->setBackView(static::LIST_VIEW)
                    ->setTabTitle('title')
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createPreviewFormViewBuilder(static::EDIT_DETAILS_FORM_VIEW, "/details")
                    ->setResourceKey(AuthorEntity::RESOURCE_KEY)
                    ->setFormKey(AuthorEntity::FORM_KEY)
                    ->setTabTitle("sulu_admin.details")
                    ->addToolbarActions($formToolbarAction)
                    ->setParent(static::EDIT_FORM_VIEW)
            );

            $viewCollection->add(
                $this->viewBuilderFactory->createFormViewBuilder(static::EDIT_SEO_FORM_VIEW, "/seo")
                    ->setResourceKey(AuthorEntity::RESOURCE_KEY)
                    ->setFormKey(AuthorEntity::SEO_FORM_KEY)
                    ->setTabTitle("sulu_page.seo")
                    ->addToolbarActions($formToolbarAction)
                    ->setTitleVisible(true)
                    ->setTabOrder(2048)
                    ->setParent(static::EDIT_FORM_VIEW)
            );

            if($this->activityViewBuilderFactory->hasActivityListPermission()){
                $viewCollection->add(
                    $this->activityViewBuilderFactory->createActivityListViewBuilder(static::EDIT_FORM_VIEW . ".activity", "/activity", AuthorEntity::RESOURCE_KEY)
                        ->setParent(static::EDIT_FORM_VIEW)
                );
            }
        }
    }

    /**
     * @return mixed[]
     */
    public function getSecurityContexts(): array
    {
        return [
            self::SULU_ADMIN_SECURITY_SYSTEM => [
                'Auteur' => [
                    Author::SECURITY_CONTEXT => [
                        PermissionTypes::VIEW,
                        PermissionTypes::ADD,
                        PermissionTypes::EDIT,
                        PermissionTypes::DELETE,
                    ],
                ],
            ],
        ];
    }
}
