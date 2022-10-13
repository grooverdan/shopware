<?php declare(strict_types=1);

namespace Shopware\Elasticsearch\Admin\Indexer;

use Doctrine\DBAL\Connection;
use Shopware\Core\Content\LandingPage\LandingPageDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IterableQuery;
use Shopware\Core\Framework\DataAbstractionLayer\Dbal\Common\IteratorFactory;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Plugin\Exception\DecorationPatternException;
use Shopware\Core\Framework\Uuid\Uuid;

/**
 * @package system-settings
 *
 * @internal
 */
final class LandingPageAdminSearchIndexer extends AbstractAdminIndexer
{
    private Connection $connection;

    private IteratorFactory $factory;

    private EntityRepository $repository;

    private int $indexingBatchSize;

    public function __construct(
        Connection $connection,
        IteratorFactory $factory,
        EntityRepository $repository,
        int $indexingBatchSize
    ) {
        $this->connection = $connection;
        $this->factory = $factory;
        $this->repository = $repository;
        $this->indexingBatchSize = $indexingBatchSize;
    }

    public function getDecorated(): AbstractAdminIndexer
    {
        throw new DecorationPatternException(self::class);
    }

    public function getEntity(): string
    {
        return LandingPageDefinition::ENTITY_NAME;
    }

    public function getName(): string
    {
        return 'landing-page-listing';
    }

    public function getIterator(): IterableQuery
    {
        return $this->factory->createIterator($this->getEntity(), null, $this->indexingBatchSize);
    }

    /**
     * @param array<string, mixed> $result
     *
     * @return array{total:int, data:EntityCollection<Entity>}
     */
    public function globalData(array $result, Context $context): array
    {
        $ids = array_column($result['hits'], 'id');

        return [
            'total' => (int) $result['total'],
            'data' => $this->repository->search(new Criteria($ids), $context)->getEntities(),
        ];
    }

    /**
     * @param array<string>|array<int, array<string>> $ids
     *
     * @throws \Doctrine\DBAL\Exception
     *
     * @return array<int|string, array<string, mixed>>
     */
    public function fetch(array $ids): array
    {
        $data = $this->connection->fetchAllAssociative(
            '
            SELECT LOWER(HEX(landing_page.id)) as id,
                   GROUP_CONCAT(landing_page_translation.name) as name,
                   GROUP_CONCAT(tag.name) as tags
            FROM landing_page
                INNER JOIN landing_page_translation
                    ON landing_page.id = landing_page_translation.landing_page_id
                LEFT JOIN landing_page_tag
                    ON landing_page.id = landing_page_tag.landing_page_id
                LEFT JOIN tag
                    ON landing_page_tag.tag_id = tag.id
            WHERE landing_page.id IN (:ids)
            GROUP BY landing_page.id
        ',
            [
                'ids' => Uuid::fromHexToBytesList($ids),
            ],
            [
                'ids' => Connection::PARAM_STR_ARRAY,
            ]
        );

        $mapped = [];
        foreach ($data as $row) {
            $id = $row['id'];
            $text = \implode(' ', array_filter(array_unique(array_values($row))));
            $mapped[$id] = ['id' => $id, 'text' => \strtolower($text)];
        }

        return $mapped;
    }
}
