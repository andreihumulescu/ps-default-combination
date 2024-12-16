<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT Free License
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/license/mit
 *
 * @author    Andrei H
 * @copyright Since 2024 Andrei H
 * @license   MIT
 */
declare(strict_types=1);

namespace DefaultCombination\Repository;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class EmptyDefaultCombinationRepository implements RepositoryInterface
{
    /**
     * @var Connection
     */
    private Connection $connection;

    /**
     * @var string
     */
    private string $databasePrefix;

    public function __construct(Connection $connection, string $databasePrefix)
    {
        $this->connection = $connection;
        $this->databasePrefix = $databasePrefix;
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(int $langId = 1): array
    {
        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('sa.id_product, sa.id_product_attribute, quantity, pl.name')
            ->from($this->databasePrefix . 'stock_available', 'sa')
            ->innerJoin(
                'sa',
                $this->databasePrefix . 'product_lang',
                'pl',
                'pl.id_product = sa.id_product AND pl.id_lang = :langId'
            )
            ->where('sa.id_product_attribute IN (
                SELECT id_product_attribute FROM ' . $this->databasePrefix . 'product_attribute WHERE default_on = 1
            )')
            ->andWhere('sa.quantity = 0')
            ->setParameter('langId', $langId)
        ;

        return $qb->execute()->fetchAllAssociative();
    }

    /**
     * Check if the provided products have a default combination with stock > 0,
     * returning the list of products that do not match the criteria.
     *
     * @param array $ids
     * @param int $langId
     *
     * @return array
     */
    public function findByIds(array $ids = [], int $langId = 1): array
    {
        if (empty($ids)) {
            return [];
        }

        $qb = $this->connection->createQueryBuilder();
        $qb
            ->select('sa.id_product, sa.id_product_attribute, quantity, pl.name')
            ->from($this->databasePrefix . 'stock_available', 'sa')
            ->innerJoin(
                'sa',
                $this->databasePrefix . 'product_lang',
                'pl',
                'pl.id_product = sa.id_product AND pl.id_lang = :langId'
            )
            ->where('sa.id_product IN (' . implode(',', $ids) . ')')
            ->andWhere('sa.id_product_attribute IN (
                SELECT id_product_attribute FROM ' . $this->databasePrefix . 'product_attribute WHERE default_on = 1
            )')
            ->andWhere('sa.quantity = 0')
            ->setParameter('langId', $langId)
        ;

        return $qb->execute()->fetchAllAssociative();
    }
}
