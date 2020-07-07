<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Webmozart\Assert\Assert;

final class ChannelContext implements Context
{
    private ChannelRepositoryInterface $repository;
    private SharedStorageInterface $sharedStorage;

    public function __construct(
        ChannelRepositoryInterface $repository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->repository = $repository;
        $this->sharedStorage = $sharedStorage;
    }

    /**
     * @Given I want to configure the channel :name
     */
    public function iWantToConfigureTheChannel(string $name): void
    {
        $channel = $this->repository->findOneBy(['name' => $name]);
        Assert::notNull($channel);

        $this->sharedStorage->set('channel', $channel);
    }
}
