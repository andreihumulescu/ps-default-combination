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

namespace DefaultCombination\Command;

use DefaultCombination\Service\ServiceInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SetCommand extends Command
{
    /**
     * @var ServiceInterface
     */
    private ServiceInterface $service;

    /**
     * SetCommand constructor.
     *
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this->setName('defaultcombination:set');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->service->execute();

        $output->writeln('The default combination has been set.');
    }
}
