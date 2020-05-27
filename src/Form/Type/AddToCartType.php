<?php

declare(strict_types=1);

namespace Nedac\SyliusOrderNowPlugin\Form\Type;

use Sylius\Bundle\OrderBundle\Form\Type\CartItemType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AddToCartType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('cartItem', CartItemType::class, ['product' => $options['product']]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefined([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'nedac_sylius_order_now_plugin_add_to_cart';
    }
}
