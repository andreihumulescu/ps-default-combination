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
$autoloader = dirname(__FILE__) . '/vendor/autoload.php';

if (is_readable($autoloader)) {
    include_once $autoloader;
} else {
    exit;
}

use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DefaultCombination extends Module
{
    private const HOOKS = [
        'actionAdminControllerSetMedia',
        'actionValidateOrderAfter',
    ];

    public function __construct()
    {
        $this->name = 'defaultcombination';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Andrei H';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '8.0.0',
            'max' => _PS_VERSION_,
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->trans('Default Combination', [], 'Modules.Defaultcombination.Admin');
        $this->description = $this->trans(
            'PrestaShop module that swaps the default combination when the current default one has no more stock items.',
            [],
            'Modules.Defaultcombination.Admin'
        );

        $this->confirmUninstall = $this->trans('Are you sure you want to uninstall?', [], 'Modules.Defaultcombination.Admin');
    }

    /**
     * {@inheritDoc}
     */
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        Tools::clearSf2Cache();

        return parent::install()
            && $this->registerHook(self::HOOKS);
    }

    /**
     * {@inheritDoc}
     */
    public function isUsingNewTranslationSystem()
    {
        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function getContent()
    {
        return $this->get('twig')->render(
            '@Modules/defaultcombination/views/templates/admin/configure.html.twig',
            [
                'productList' => $this->get('defaultcombination.empty_default_combination_repository')->findAll(),
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    public function hookActionAdminControllerSetMedia()
    {
        if (!Tools::getIsset('configure') || Tools::getValue('configure') !== $this->name) {
            return;
        }

        Media::addJsDef(
            [
                'defaultcombination' => [
                    'url' => SymfonyContainer::getInstance()->get('router')->generate('set_default_combination'),
                ],
            ]
        );

        $this->context->controller->addJS($this->_path . '/views/js/defaultcombination.js');
        $this->context->controller->addCSS($this->_path . '/views/css/defaultcombination.css');
    }

    /**
     * {@inheritDoc}
     */
    public function hookActionValidateOrderAfter(array $params)
    {
        // Do nothing. Currently, most of the services are not available in the front store.
        // TODO: Find an alternative way.
    }
}
