<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Model\ProductOptionValueInterface;
use Sylius\Component\Product\Repository\ProductOptionRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class ProductOptionContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ProductOptionRepositoryInterface $productOptionRepository;
    private FactoryInterface $productOptionFactory;
    private FactoryInterface $productOptionValueFactory;
    private ObjectManager $objectManager;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductOptionRepositoryInterface $productOptionRepository,
        FactoryInterface $productOptionFactory,
        FactoryInterface $productOptionValueFactory,
        ObjectManager $objectManager
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productOptionRepository = $productOptionRepository;
        $this->productOptionFactory = $productOptionFactory;
        $this->productOptionValueFactory = $productOptionValueFactory;
        $this->objectManager = $objectManager;
    }

    private function createProductOption(
        string $name,
        ?string $code = null,
        ?string $position = null
    ): ProductOptionInterface {
        /** @var ProductOptionInterface $productOption */
        $productOption = $this->productOptionFactory->createNew();
        $productOption->setName($name);
        if (null === $code) {
            $code = StringInflector::nameToCode($name);
        }
        $productOption->setCode($code);
        $productOption->setPosition((null === $position) ? null : (int) $position);

        $this->sharedStorage->set('product_option', $productOption);
        $this->productOptionRepository->add($productOption);

        return $productOption;
    }

    private function createProductOptionValue(string $value, string $code): ProductOptionValueInterface
    {
        /** @var ProductOptionValueInterface $productOptionValue */
        $productOptionValue = $this->productOptionValueFactory->createNew();
        $productOptionValue->setValue($value);
        $productOptionValue->setCode($code);

        $this->sharedStorage->set(sprintf("%s_option_color_value", $code), $productOptionValue);

        return $productOptionValue;
    }

    /**
     * @Given the store has (also) a product option :name
     * @Given the store has a product option :name with a code :code
     */
    public function theStoreHasAProductOptionWithACode(string $name, ?string $code = null): void
    {
        $this->createProductOption($name, $code);
    }

    /**
     * @Given /^(this product option) has(?:| also) the "([^"]+)" option value with code "([^"]+)"$/
     */
    public function thisProductOptionHasTheOptionValueWithCode(
        ProductOptionInterface $productOption,
        string $productOptionValueName,
        string $productOptionValueCode
    ): void {
        $productOptionValue = $this->createProductOptionValue($productOptionValueName, $productOptionValueCode);
        $productOption->addValue($productOptionValue);

        $this->objectManager->flush();
    }
}
