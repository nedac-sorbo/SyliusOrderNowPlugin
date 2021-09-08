This plugin adds a small form to each and every product card in the shop. The form allows customers to add products to
their cart without having to visit the product detail and cart summary page.

![product_cards](product-cards.png)

As can be seen in the image above, this works best when using the "match" product variant selection method. Please see the
official Sylius documentation on how to setup the product variant selection method in the shop.

##### Supported Sylius versions:
<table>
    <tr><td>1.10</td></tr>
</table>


> **_NOTE:_** *This plugin requires PHP 7.4 or up*

#### Installation:
1. Install using composer:
    ```bash
    composer require nedac/sylius-order-now-plugin
    ```

2. If you have **overridden** the `ProductAssociation`, `Product` and/or `ProductReview` controller in
your project, please make sure they use the corresponding trait:
    - `ProductAssociation` -> `Nedac\SyliusOrderNowPlugin\Controller\ProductAssociationControllerTrait`
    - `Product` -> `Nedac\SyliusOrderNowPlugin\Controller\ProductControllerTrait`
    - `ProductReview` -> `Nedac\SyliusOrderNowPlugin\Controller\ProductReviewTrait`
    > **_NOTE:_** *If the methods in the traits have already been implemented in the controller in your project then the logic will have to be merged.*

3. If you have overridden controllers in your project and you've gone through step 2, please do not change your Sylius
configuration for those controllers. Otherwise:
    ```yaml
    # config/packages/_sylius.yaml

    # ...

    sylius_product:
        resources:
            product:
                classes:
                    controller: Nedac\SyliusOrderNowPlugin\Controller\ProductController
            product_association:
                classes:
                    controller: Nedac\SyliusOrderNowPlugin\Controller\ProductAssociationController

    # ...

    sylius_review:
        resources:
            product:
                review:
                    classes:
                        controller: Nedac\SyliusOrderNowPlugin\Controller\ProductReviewController
    ```

8. Install assets:
    ```bash
    bin/console sylius:install:assets
    ```

It might be necessary to clear the cache after installation:
```bash
bin/console cache:clear
```

The plugin should now be successfully installed.
