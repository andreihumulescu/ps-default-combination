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

namespace Defaultcombination\Controller\Admin;

use DefaultCombination\Service\ServiceInterface;
use PrestaShopBundle\Controller\Admin\FrameworkBundleAdminController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SetDefaultCombinationController extends FrameworkBundleAdminController
{
    /**
     * @var ServiceInterface
     */
    private ServiceInterface $service;

    /**
     * SetDefaultCombinationController constructor.
     *
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * The set default combination action.
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function setAction(Request $request): JsonResponse
    {
        $content = json_decode($request->getContent(), true);
        if (empty($content['productIds'])) {
            $content['productIds'] = [];
        }

        $this->service->execute($content['productIds']);

        $data = [
            'success' => true,
            'message' => $this->trans('The default combination has been set.', 'Modules.Defaultcombination.Admin'),
        ];

        return $this->json($data);
    }
}
