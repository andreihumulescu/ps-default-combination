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

if (!defined('_PS_VERSION_')) {
    exit;
}

interface ServiceInterface
{
    public function execute(array $productIds = [], int $langId = 1, int $shopId = 1): void;
}
