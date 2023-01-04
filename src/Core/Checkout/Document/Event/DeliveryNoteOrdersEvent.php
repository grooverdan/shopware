<?php declare(strict_types=1);

namespace Shopware\Core\Checkout\Document\Event;

use Shopware\Core\Framework\Log\Package;

#[Package('customer-order')]
final class DeliveryNoteOrdersEvent extends DocumentOrderEvent
{
}
