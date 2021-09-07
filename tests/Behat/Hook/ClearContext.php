<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Hook;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Call\BeforeScenario;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class ClearContext implements Context
{
    /** @var RepositoryInterface[] */
    private array $repositories = [];

    public function __construct(RepositoryInterface ...$repositories)
    {
        $this->repositories = $repositories;
    }

    /**
     * @BeforeScenario
     */
    public function clearProducts(BeforeScenarioScope $scope): void
    {
        foreach ($this->repositories as $repository) {
            /** @var ResourceInterface[] $items */
            $items = $repository->findAll();
            foreach ($items as $item) {
                $repository->remove($item);
            }
        }
    }
}
