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

namespace DefaultCombination\Service;

use PrestaShop\PrestaShop\Adapter\Product\Combination\Repository\CombinationRepository;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetCombinationForEditing;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\Query\GetEditableCombinationsList;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Repository\RepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SetDefaultCombinationService implements ServiceInterface
{
    /**
     * @var CommandBusInterface
     */
    private CommandBusInterface $queryBus;

    /**
     * @var CombinationRepository
     */
    private CombinationRepository $combinationRepository;

    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $defaultCombinationRepository;

    public function __construct(
        CombinationRepository $combinationRepository,
        CommandBusInterface $queryBus,
        RepositoryInterface $defaultCombinationRepository
    ) {
        $this->combinationRepository = $combinationRepository;
        $this->queryBus = $queryBus;
        $this->defaultCombinationRepository = $defaultCombinationRepository;
    }

    /**
     * Set the default combination for the given product ids.
     * If the provided argument is empty, fetch the product ids from the database.
     *
     * @param array $productIds
     * @param int $langId
     * @param int $shopId
     */
    public function execute(array $productIds = [], int $langId = 1, int $shopId = 1): void
    {
        if (empty($productIds)) {
            $productIds = array_map(
                fn ($product) => $product['id_product'],
                $this->defaultCombinationRepository->findAll()
            );
        }

        foreach ($productIds as $productId) {
            $this->setNewDefaultCombination($productId, $langId, $shopId);
        }
    }

    /**
     * Set the new default combination for the given product id.
     *
     * @param int $productId
     * @param int $langId
     * @param int $shopId
     */
    private function setNewDefaultCombination(int $productId, int $langId, int $shopId): void
    {
        $productCombinations = $this->queryBus->handle(
            new GetEditableCombinationsList(
                $productId,
                $langId,
                ShopConstraint::shop($shopId)
            )
        )->getCombinations();

        $combinationList = [];

        $currentDefaultCombination = null;
        foreach ($productCombinations as $combination) {
            $combinationData = $this->queryBus->handle(
                new GetCombinationForEditing(
                    $combination->getCombinationId(),
                    ShopConstraint::shop($shopId)
                )
            );

            $combinationList[$combination->getCombinationId()] = $combinationData;

            if ($combination->isDefault()) {
                $currentDefaultCombination = $combinationData;
            }
        }

        $newDefaultCombination = null;
        $priceDiff = INF;
        foreach ($combinationList as $combination) {
            if ($combination->getStock()->getQuantity() === 0) {
                continue;
            }

            $combinationPrice = $combination->getPrices()->getProductPrice()->getCoefficient();
            $currentDefaultCombinationPrice = $currentDefaultCombination->getPrices()->getProductPrice()->getCoefficient();

            if ($combinationPrice >= $currentDefaultCombinationPrice) {
                $diff = $combinationPrice - $currentDefaultCombinationPrice;
                if ($diff < $priceDiff) {
                    $priceDiff = $diff;
                    $newDefaultCombination = $combination;
                }
            } else {
                $diff = $currentDefaultCombinationPrice - $combinationPrice;
                if ($diff < $priceDiff) {
                    $priceDiff = $diff;
                    $newDefaultCombination = $combination;
                }
            }
        }

        if (!$newDefaultCombination) {
            return;
        } else {
            $this->combinationRepository->setDefaultCombination(
                new ProductId($productId),
                new CombinationId($newDefaultCombination->getCombinationId()),
                ShopConstraint::shop($shopId)
            );
        }
    }
}
