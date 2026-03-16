<?php

declare(strict_types=1);

namespace DalleyIt\ContaoCatalogue\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\Parser\ParserInterface;
use DalleyIt\ContaoCatalogue\DaitContaoCatalogBundle;

final class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(DaitContaoCatalogBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
        ];
    }
}
