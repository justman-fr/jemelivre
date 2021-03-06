<?php

namespace App;

/*
 * This file is part of Sulu.
 *
 * (c) Sulu GmbH
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use FOS\HttpCache\SymfonyCache\HttpCacheProvider;
use Sulu\Bundle\HttpCacheBundle\Cache\SuluHttpCache;
use Sulu\Component\HttpKernel\SuluKernel;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class Kernel extends SuluKernel implements HttpCacheProvider
{
    /**
     * @var HttpKernelInterface|null
     */
    private $httpCache;

    public function getHttpCache()
    {
        if (!$this->httpCache) {
            $this->httpCache = new SuluHttpCache($this);
            // Activate the following for user based caching see also:
            // https://foshttpcachebundle.readthedocs.io/en/latest/features/user-context.html
            //
            //$this->httpCache->addSubscriber(
            //    new \FOS\HttpCache\SymfonyCache\UserContextListener([
            //        'session_name_prefix' => 'SULUSESSID',
            //    ])
            //);
        }

        return $this->httpCache;
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader)
    {
        $container->setParameter('container.dumper.inline_class_loader', true);

        parent::configureContainer($container, $loader);
    }
}
