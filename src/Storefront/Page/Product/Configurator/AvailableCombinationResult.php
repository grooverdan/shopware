<?php declare(strict_types=1);

namespace Shopware\Storefront\Page\Product\Configurator;

use Shopware\Core\Content\Product\SalesChannel\Detail\AvailableCombinationResult as CoreAvailableCombinationResultAlias;
use Shopware\Core\Framework\Log\Package;

/**
 * @deprecated tag:v6.5.0 - Class will be removed, use Shopware\Core\Content\Product\SalesChannel\Detail\AvailableCombinationResult instead
 */
#[Package('inventory')]
class AvailableCombinationResult extends CoreAvailableCombinationResultAlias
{
}
