<?php declare(strict_types=1);

namespace Shopware\Framework\Write\Resource;

use Shopware\Framework\Write\Flag\Required;
use Shopware\Framework\Write\Field\FkField;
use Shopware\Framework\Write\Field\IntField;
use Shopware\Framework\Write\Field\ReferenceField;
use Shopware\Framework\Write\Field\StringField;
use Shopware\Framework\Write\Field\BoolField;
use Shopware\Framework\Write\Field\DateField;
use Shopware\Framework\Write\Field\SubresourceField;
use Shopware\Framework\Write\Field\LongTextField;
use Shopware\Framework\Write\Field\LongTextWithHtmlField;
use Shopware\Framework\Write\Field\FloatField;
use Shopware\Framework\Write\Field\TranslatedField;
use Shopware\Framework\Write\Field\UuidField;
use Shopware\Framework\Write\Resource;

class PremiumProductResource extends Resource
{
    protected const UUID_FIELD = 'uuid';
    protected const AMOUNT_FIELD = 'amount';
    protected const PRODUCT_ORDER_NUMBER_FIELD = 'productOrderNumber';
    protected const PREMIUM_ORDER_NUMBER_FIELD = 'premiumOrderNumber';

    public function __construct()
    {
        parent::__construct('premium_product');
        
        $this->primaryKeyFields[self::UUID_FIELD] = (new UuidField('uuid'))->setFlags(new Required());
        $this->fields[self::AMOUNT_FIELD] = new FloatField('amount');
        $this->fields[self::PRODUCT_ORDER_NUMBER_FIELD] = new StringField('product_order_number');
        $this->fields[self::PREMIUM_ORDER_NUMBER_FIELD] = (new StringField('premium_order_number'))->setFlags(new Required());
        $this->fields['productDetail'] = new ReferenceField('productDetailUuid', 'uuid', \Shopware\ProductDetail\Writer\ProductDetailResource::class);
        $this->fields['productDetailUuid'] = (new FkField('product_detail_uuid', \Shopware\ProductDetail\Writer\ProductDetailResource::class, 'uuid'))->setFlags(new Required());
        $this->fields['shop'] = new ReferenceField('shopUuid', 'uuid', \Shopware\Shop\Writer\ShopResource::class);
        $this->fields['shopUuid'] = (new FkField('shop_uuid', \Shopware\Shop\Writer\ShopResource::class, 'uuid'))->setFlags(new Required());
    }
    
    public function getWriteOrder(): array
    {
        return [
            \Shopware\ProductDetail\Writer\ProductDetailResource::class,
            \Shopware\Shop\Writer\ShopResource::class,
            \Shopware\Framework\Write\Resource\PremiumProductResource::class
        ];
    }
}
