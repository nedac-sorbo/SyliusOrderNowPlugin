<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Doctrine\Persistence\ObjectManager;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Model\ProductOptionInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class ProductContext implements Context
{
    private SharedStorageInterface $sharedStorage;
    private ProductRepositoryInterface $productRepository;
    private ProductFactoryInterface $productFactory;
    private FactoryInterface $productVariantFactory;
    private FactoryInterface $channelPricingFactory;
    private ObjectManager $objectManager;
    private SlugGeneratorInterface $slugGenerator;
    private ProductVariantResolverInterface $defaultVariantResolver;

    public function __construct(
        SharedStorageInterface $sharedStorage,
        ProductRepositoryInterface $productRepository,
        ProductFactoryInterface $productFactory,
        FactoryInterface $productVariantFactory,
        FactoryInterface $channelPricingFactory,
        ObjectManager $objectManager,
        SlugGeneratorInterface $slugGenerator,
        ProductVariantResolverInterface $defaultVariantResolver
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
        $this->productVariantFactory = $productVariantFactory;
        $this->channelPricingFactory = $channelPricingFactory;
        $this->objectManager = $objectManager;
        $this->slugGenerator = $slugGenerator;
        $this->defaultVariantResolver = $defaultVariantResolver;
    }


    private function createChannelPricingForChannel(
        int $price,
        ChannelInterface $channel
    ): ChannelPricingInterface {
        /** @var ChannelPricingInterface $channelPricing */
        $channelPricing = $this->channelPricingFactory->createNew();
        $channelPricing->setPrice($price);
        $channelPricing->setChannelCode($channel->getCode());

        return $channelPricing;
    }

    private function saveProduct(ProductInterface $product): void
    {
        $this->productRepository->add($product);
        $this->sharedStorage->set('product', $product);
    }

    /**
     * @Given /^(this product) has (this product option)$/
     * @Given /^(this product) has (?:a|an) ("[^"]+" option)$/
     */
    public function thisProductHasThisProductOption(ProductInterface $product, ProductOptionInterface $option): void
    {
        $product->addOption($option);

        $this->objectManager->flush();
    }

    /**
     * @Given /^(this product) is available in "([^"]+)" ([^"]+) priced at ("[^"]+")$/
     */
    public function thisProductIsAvailableInSize(
        ProductInterface $product,
        string $optionValueName,
        string $optionName,
        int $price
    ): void {
        /** @var ProductVariantInterface $variant */
        $variant = $this->productVariantFactory->createNew();

        /** @var ProductOptionInterface $option */
        $option = $this->sharedStorage->get('product_option');
        Assert::eq($option->getName(), $optionName);

        $values = $option->getValues();
        $optionValue = null;
        foreach ($values as $value) {
            if ($value->getValue() === $optionValueName) {
                $optionValue = $value;
                break;
            }
        }
        Assert::notNull($optionValue, 'Failed to find option value with name: "' . $optionValueName . '"!');

        $variant->addOptionValue($optionValue);
        $variant->addChannelPricing(
            $this->createChannelPricingForChannel(
                $price,
                $this->sharedStorage->get('channel')
            )
        );
        $variant->setCode(sprintf('%s_%s', $product->getCode(), $optionValueName));
        $variant->setName($product->getName());

        $product->addVariant($variant);
        $this->objectManager->flush();
    }

    /**
     * @Given /^the store has(?:| a| an) "([^"]+)" configurable product$/
     * @Given /^the store has(?:| a| an) "([^"]+)" configurable product with "([^"]+)" slug$/
     */
    public function storeHasAConfigurableProduct(string $productName, ?string $slug = null): void
    {
        /** @var ChannelInterface|null $channel */
        $channel = null;
        if ($this->sharedStorage->has('channel')) {
            $channel = $this->sharedStorage->get('channel');
        }

        /** @var ProductInterface $product */
        $product = $this->productFactory->createNew();
        $product->setCode(StringInflector::nameToUppercaseCode($productName));
        $product->setVariantSelectionMethod(ProductInterface::VARIANT_SELECTION_MATCH);

        if (null !== $channel) {
            $product->addChannel($channel);

            foreach ($channel->getLocales() as $locale) {
                $product->setFallbackLocale($locale->getCode());
                $product->setCurrentLocale($locale->getCode());

                $product->setName($productName);
                $product->setSlug($slug ?: $this->slugGenerator->generate($productName));
            }
        }

        $this->saveProduct($product);
    }

    /**
     * @Given /^(this product) is tracked by the inventory$/
     * @Given /^(?:|the )("[^"]+" product) is(?:| also) tracked by the inventory$/
     */
    public function thisProductIsTrackedByTheInventory(ProductInterface $product): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setTracked(true);

        $this->objectManager->flush();
    }

    /**
     * @Given /^there (?:is|are) (\d+) unit(?:|s) of (product "([^"]+)") available in the inventory$/
     */
    public function thereIsQuantityOfProducts(string $quantity, ProductInterface $product): void
    {
        /** @var ProductVariantInterface $productVariant */
        $productVariant = $this->defaultVariantResolver->getVariant($product);
        $productVariant->setOnHand((int) $quantity);

        $this->objectManager->flush();
    }
}
