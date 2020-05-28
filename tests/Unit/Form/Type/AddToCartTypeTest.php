<?php

declare(strict_types=1);

namespace Tests\Nedac\SyliusOrderNowPlugin\Unit\Form\Type;

use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Nedac\SyliusOrderNowPlugin\Form\Type\AddToCartType;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommand;
use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddToCartTypeTest extends MockeryTestCase
{
    public function testCanInstantiate(): AddToCartType
    {
        $type = new AddToCartType(AddToCartCommand::class);

        $this->assertInstanceOf(AbstractResourceType::class, $type);

        return $type;
    }

    /**
     * @depends testCanInstantiate
     * @param AddToCartType $type
     */
    public function testReturnsCorrectBlockPrefix(AddToCartType $type): void
    {
        $this->assertEquals('nedac_sylius_order_now_plugin_add_to_cart', $type->getBlockPrefix());
    }

    /**
     * @depends testCanInstantiate
     * @param AddToCartType $type
     */
    public function testCanBuildForm(AddToCartType $type): void
    {
        $builder = Mockery::mock(FormBuilderInterface::class);
        $builder
            ->shouldReceive('add')
            ->with('cartItem', CartItemType::class, ['product' => '123'])
            ->once()
            ->andReturnSelf()
        ;

        $type->buildForm($builder, ['product' => '123']);
    }

    /**
     * @depends testCanInstantiate
     */
    public function testCanConfigureOptions(): void
    {
        $resolver = Mockery::mock(OptionsResolver::class);
        $resolver
            ->shouldReceive('setDefined')
            ->with(['product'])
            ->once()
            ->andReturnSelf()
        ;
        $resolver
            ->shouldReceive('setAllowedTypes')
            ->with('product', ProductInterface::class)
            ->once()
            ->andReturnSelf()
        ;

        $type = Mockery::mock(AddToCartType::class)->makePartial();
        $type->shouldAllowMockingProtectedMethods();

        $type
            ->shouldReceive('parentConfigureOptions')
            ->once()
        ;

        $type->configureOptions($resolver);
    }
}
