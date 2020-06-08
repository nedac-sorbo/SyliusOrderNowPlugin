<div class="repo-badge inline-block vertical-align">
  <a title="Latest push build on default branch: passed" name="status-images" class="pointer open-popup">
    <img src="https://travis-ci.com/nedac-sorbo/SyliusOrderNowPlugin.svg?branch=master" alt="build:unknown">
  </a>
</div>
<br />
This plugin adds a small form to each and every product card in the shop. The form allows customers to add products to
their cart without having to visit the product detail and cart summary page.

![product_cards](product-cards.png)

As can be seen in the image above, this works best when using the "match" product variant selection method. Please see the
official Sylius documentation on how to setup the product variant selection method in the shop.

##### Supported Sylius versions:
<table>
    <tr><td>1.6</td></tr>
</table>


> **_NOTE:_** *This plugin requires PHP 7.4 or up*

#### Installation:
1. Install using composer:
    ```bash
    composer require nedac/sylius-order-now-plugin
    ```

2. Register bundle:
    ```php
    <?php
    # config/bundles.php
    return [
        # ...
        Nedac\SyliusOrderNowPlugin\NedacSyliusOrderNowPlugin::class => ['all' => true],
    ];
    ```

3. If you have **overridden** the `ProductAssociation`, `Product` and/or `ProductReview` controller in
your project, please make sure they use the corresponding trait:
    - `ProductAssociation` -> `Nedac\SyliusOrderNowPlugin\Controller\ProductAssociationControllerTrait`
    - `Product` -> `Nedac\SyliusOrderNowPlugin\Controller\ProductControllerTrait`
    - `ProductReview` -> `Nedac\SyliusOrderNowPlugin\Controller\ProductReviewTrait`
    > **_NOTE:_** *If the methods in the traits have already been implemented in the controller in your project then the logic will have to be merged.*

4. If you have overridden controllers in your project and you've gone through step 3, please do not change your Sylius
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

5. Make the plugins route available:
    ```yaml
    # config/routes/sylius_shop.yaml

    # ...

    nedac_sylius_order_now_plugin:
        resource: '@NedacSyliusOrderNowPlugin/Resources/config/shop_routing.yml'
    ```

6. When using Sylius 1.7, add/override templates like below (if on Sylius 1.6 skip this step):
    ```twig
    {# templates/bundles/SyliusShopBundle/Homepage/_carousel.html.twig #}

    <div class="carousel-wrapper">
        <div class="carousel">
            {% for product in products %}
                <div class="carousel-item">
                    {% include '@SyliusShop/Product/_box.html.twig' with {'cardForm': forms[loop.index - 1]} %}
                </div>
            {% endfor %}
        </div>

        <div class="carousel-nav">
            <button class="carousel-left ui huge black icon button">
                <i class="left arrow icon"></i>
            </button>
            <button class="carousel-right ui huge black icon button">
                <i class="right arrow icon"></i>
            </button>
        </div>
    </div>
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Homepage/_list.html.twig #}

    {% if products|length == 4 %}
        {% set columns = "two" %}
    {% endif %}
    {% if products|length == 3 %}
        {% set columns = "three odd doubling" %}
    {% endif %}

    <div class="ui {{ columns|default('') }} cards">
        {% for product in products %}
            {% include '@SyliusShop/Product/_box.html.twig' with {'cardForm': forms[loop.index - 1]} %}
        {% endfor %}
    </div>
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Product/Box/_content.html.twig #}

    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

    {% form_theme cardForm 'form/product_card_form_theme.html.twig' %}

    {% set formId = 'nedac-sylius-order-now-plugin-form-' ~ product.id %}

    <div class="ui fluid card" {{ sylius_test_html_attribute('product') }}>
        <a href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="blurring dimmable image">
            <div class="ui dimmer">
                <div class="content">
                    <div class="center">
                        <div class="ui inverted button">{{ 'sylius.ui.view_more'|trans }}</div>
                    </div>
                </div>
            </div>
            {% include '@SyliusShop/Product/_mainImage.html.twig' with {'product': product} %}
        </a>
        <div class="content" {{ sylius_test_html_attribute('product-content') }}>
            <a href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="header sylius-product-name" {{ sylius_test_html_attribute('product-name', product.name) }}>{{ product.name }}</a>
            {% if not product.variants.empty() %}
                <div class="sylius-product-price" {{ sylius_test_html_attribute('product-price') }}>{{ money.calculatePrice(product|sylius_resolve_variant) }}</div>
            {% endif %}
        </div>
        <div class="ui bottom attached button nedac-order-now-button-container">
            {{ form_start(cardForm, {'action': path('nedac_shop_cart_add_item', {'productId': product.id}), 'attr': {'id': formId, 'novalidate': 'novalidate'}}) }}
            {{ form_row(cardForm.cartItem.quantity) }}
            {% if not product.simple %}
                {% if product.variantSelectionMethodChoice %}
                    {% include 'Product/Show/_variants.html.twig' %}
                {% else %}
                    {% include 'Product/Show/_options.html.twig' %}
                {% endif %}
            {% endif %}
            <i class="add icon"></i>
            {{ form_end(cardForm) }}
        </div>
    </div>
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Product/Index/_main.html.twig #}

    {% import '@SyliusUi/Macro/messages.html.twig' as messages %}
    {% import '@SyliusUi/Macro/pagination.html.twig' as pagination %}

    {{ sylius_template_event('sylius.shop.product.index.search', _context) }}

    <div class="ui clearing hidden divider"></div>

    {{ sylius_template_event('sylius.shop.product.index.before_list', {'products': resources.data}) }}

    {% if resources.data|length > 0 %}
        <div class="ui three cards" id="products" {{ sylius_test_html_attribute('products') }}>
            {% for product in resources.data %}
                {% include '@SyliusShop/Product/_box.html.twig' with {'cardForm': forms[loop.index - 1]} %}
            {% endfor %}
        </div>
        <div class="ui hidden divider"></div>

        {{ sylius_template_event('sylius.shop.product.index.before_pagination', {'products': resources.data}) }}

        {{ pagination.simple(resources.data) }}
    {% else %}
        {{ messages.info('sylius.ui.no_results_to_display') }}
    {% endif %}
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Product/_box.html.twig #}

    {{ sylius_template_event('sylius.shop.product.index.box', {'product': product, 'cardForm': cardForm}) }}
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Product/_horizontalList.html.twig #}

    <div class="ui four doubling cards">
        {% for product in products %}
            {% include '@SyliusShop/Product/_box.html.twig' with {'cardForm': forms[loop.index - 1]} %}
        {% endfor %}
    </div>
    ```
    ```twig
    {# templates/form/product_card_form_theme.html.twig #}

    {% block form_label %}
    {% endblock %}

    {% block integer_widget %}
        <input class="nedac-sylius-order-now-plugin-number-input" type="number" name="{{ full_name }}" value="1" min="1" />
    {% endblock %}

    {% block choice_widget %}
        {%- if required and placeholder is none and not placeholder_in_choices and not multiple and (attr.size is not defined or attr.size <= 1) -%}
            {% set required = false %}
        {%- endif -%}
        <select class="nedac-sylius-order-now-plugin-dropdown" name="{{ full_name }}" {% if multiple %} multiple="multiple"{% endif %}>
            {%- if placeholder is not none -%}
                <option value=""{% if required and value is empty %} selected="selected"{% endif %}>{{ placeholder != '' ? (translation_domain is same as(false) ? placeholder : placeholder|trans({}, translation_domain)) }}</option>
            {%- endif -%}
            {%- if preferred_choices|length > 0 -%}
                {% set options = preferred_choices %}
                {% set render_preferred_choices = true %}
                {{- block('choice_widget_options') -}}
                {%- if choices|length > 0 and separator is not none -%}
                    <option disabled="disabled">{{ separator }}</option>
                {%- endif -%}
            {%- endif -%}
            {%- set options = choices -%}
            {%- set render_preferred_choices = false -%}
            {{- block('choice_widget_options') -}}
        </select>
    {% endblock %}

    {% block form_row %}
        {{- form_widget(form) -}}
        {{- form_errors(form) -}}
    {% endblock %}

    {%- block form_widget_compound -%}
        {%- if form is rootform -%}
            {{ form_errors(form) }}
        {%- endif -%}
        {{- block('form_rows') -}}
        {{- form_rest(form) -}}
    {%- endblock form_widget_compound -%}
    ```
    ```twig
    {# templates/Product/Show/_options.html.twig #}

    {% for option_form in cardForm.cartItem.variant %}
        {{ form_row(option_form, { 'attr': { 'data-option': option_form.vars.name } }) }}
    {% endfor %}
    ```
    ```twig
    {# templates/Product/Show/_variants.html.twig #}

    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

    <table class="ui single line small table" id="sylius-product-variants">
        <thead>
        <tr>
            <th>{{ 'sylius.ui.variant'|trans }}</th>
            <th>{{ 'sylius.ui.price'|trans }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        {% for key, variant in product.variants %}
            <tr>
                <td>
                    {{ variant.name }}
                    {% if product.hasOptions() %}
                        <div class="ui horizontal divided list">
                            {% for optionValue in variant.optionValues %}
                                <div class="item">
                                    {{ optionValue.value }}
                                </div>
                            {% endfor %}
                        </div>
                    {% endif %}
                </td>
                <td class="sylius-product-variant-price">{{ money.calculatePrice(variant) }}</td>
                <td class="right aligned">
                    {{ form_widget(cardForm.cartItem.variant[key], {'label': false}) }}
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
    ```

7. If on Sylius 1.6, add/override templates as follows (if on Sylius 1.7 skip this step):
    ```twig
    {# templates/bundles/SyliusShopBundle/Product/Index/_main.html.twig #}

    {% import '@SyliusUi/Macro/messages.html.twig' as messages %}
    {% import '@SyliusUi/Macro/pagination.html.twig' as pagination %}

    {{ sonata_block_render_event('sylius.shop.product.index.before_search', {'products': resources.data}) }}

    {% include '@SyliusShop/Product/Index/_search.html.twig' %}

    {{ sonata_block_render_event('sylius.shop.product.index.after_search', {'products': resources.data}) }}

    {% include '@SyliusShop/Product/Index/_pagination.html.twig' %}
    {% include '@SyliusShop/Product/Index/_sorting.html.twig' %}

    <div class="ui clearing hidden divider"></div>

    {{ sonata_block_render_event('sylius.shop.product.index.before_list', {'products': resources.data}) }}

    {% if resources.data|length > 0 %}
        <div class="ui three cards" id="products">
            {% for product in resources.data %}
                {% include '@SyliusShop/Product/_box.html.twig' with {'cardForm': forms[loop.index - 1]} %}
            {% endfor %}
        </div>
        <div class="ui hidden divider"></div>

        {{ sonata_block_render_event('sylius.shop.product.index.before_pagination', {'products': resources.data}) }}

        {{ pagination.simple(resources.data) }}
    {% else %}
        {{ messages.info('sylius.ui.no_results_to_display') }}
    {% endif %}
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Product/_box.html.twig #}

    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

    {{ sonata_block_render_event('sylius.shop.product.index.before_box', {'product': product}) }}

    {% form_theme cardForm 'form/product_card_form_theme.html.twig' %}

    {% set formId = 'nedac-sylius-order-now-plugin-form-' ~ product.id %}

    <div class="ui fluid card">
        <a href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="blurring dimmable image">
            <div class="ui dimmer">
                <div class="content">
                    <div class="center">
                        <div class="ui inverted button">{{ 'sylius.ui.view_more'|trans }}</div>
                    </div>
                </div>
            </div>
            {% include '@SyliusShop/Product/_mainImage.html.twig' with {'product': product} %}
        </a>
        <div class="content">
            <a href="{{ path('sylius_shop_product_show', {'slug': product.slug, '_locale': product.translation.locale}) }}" class="header sylius-product-name">{{ product.name }}</a>
            {% if not product.variants.empty() %}
                <div class="sylius-product-price">{{ money.calculatePrice(product|sylius_resolve_variant) }}</div>
            {% endif %}
        </div>
        <div class="ui bottom attached button nedac-order-now-button-container">
            {{ form_start(cardForm, {'action': path('nedac_shop_cart_add_item', {'productId': product.id}), 'attr': {'id': formId, 'novalidate': 'novalidate'}}) }}
                {{ form_row(cardForm.cartItem.quantity) }}
                {% if not product.simple %}
                    {% if product.variantSelectionMethodChoice %}
                        {% include 'Product/Show/_variants.html.twig' %}
                    {% else %}
                        {% include 'Product/Show/_options.html.twig' %}
                    {% endif %}
                {% endif %}
                <i class="add icon"></i>
            {{ form_end(cardForm) }}
        </div>
    </div>

    {{ sonata_block_render_event('sylius.shop.product.index.after_box', {'product': product}) }}
    ```
    ```twig
    {# templates/bundles/SyliusShopBundle/Product/_horizontalList.html.twig #}

    <div class="ui four doubling cards">
        {% for product in products %}
            {% include '@SyliusShop/Product/_box.html.twig' with {'cardForm': forms[loop.index - 1]} %}
        {% endfor %}
    </div>
    ```
    ```twig
    {# templates/form/product_card_form_theme.html.twig #}

    {% block form_label %}
    {% endblock %}

    {% block integer_widget %}
        <input class="nedac-sylius-order-now-plugin-number-input" type="number" name="{{ full_name }}" value="1" min="1" />
    {% endblock %}

    {% block choice_widget %}
        {%- if required and placeholder is none and not placeholder_in_choices and not multiple and (attr.size is not defined or attr.size <= 1) -%}
            {% set required = false %}
        {%- endif -%}
        <select class="nedac-sylius-order-now-plugin-dropdown" name="{{ full_name }}" {% if multiple %} multiple="multiple"{% endif %}>
            {%- if placeholder is not none -%}
                <option value=""{% if required and value is empty %} selected="selected"{% endif %}>{{ placeholder != '' ? (translation_domain is same as(false) ? placeholder : placeholder|trans({}, translation_domain)) }}</option>
            {%- endif -%}
            {%- if preferred_choices|length > 0 -%}
                {% set options = preferred_choices %}
                {% set render_preferred_choices = true %}
                {{- block('choice_widget_options') -}}
                {%- if choices|length > 0 and separator is not none -%}
                    <option disabled="disabled">{{ separator }}</option>
                {%- endif -%}
            {%- endif -%}
            {%- set options = choices -%}
            {%- set render_preferred_choices = false -%}
            {{- block('choice_widget_options') -}}
        </select>
    {% endblock %}

    {% block form_row %}
        {{- form_widget(form) -}}
        {{- form_errors(form) -}}
    {% endblock %}

    {%- block form_widget_compound -%}
        {%- if form is rootform -%}
            {{ form_errors(form) }}
        {%- endif -%}
        {{- block('form_rows') -}}
        {{- form_rest(form) -}}
    {%- endblock form_widget_compound -%}
    ```
    ```twig
    {# templates/Product/Show/_options.html.twig #}

    {% for option_form in cardForm.cartItem.variant %}
        {{ form_row(option_form, { 'attr': { 'data-option': option_form.vars.name } }) }}
    {% endfor %}
    ```
    ```twig
    {# templates/Product/Show/_variants.html.twig #}

    {% import "@SyliusShop/Common/Macro/money.html.twig" as money %}

    <table class="ui single line small table" id="sylius-product-variants">
        <thead>
        <tr>
            <th>{{ 'sylius.ui.variant'|trans }}</th>
            <th>{{ 'sylius.ui.price'|trans }}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        {% for key, variant in product.variants %}
            <tr>
                <td>
                    {{ variant.name }}
                    {% if product.hasOptions() %}
                        <div class="ui horizontal divided list">
                            {% for optionValue in variant.optionValues %}
                                <div class="item">
                                    {{ optionValue.value }}
                                </div>
                            {% endfor %}
                        </div>
                    {% endif %}
                </td>
                <td class="sylius-product-variant-price">{{ money.calculatePrice(variant) }}</td>
                <td class="right aligned">
                    {{ form_widget(cardForm.cartItem.variant[key], {'label': false}) }}
                </td>
            </tr>
        {% endfor %}
        </tbody>
    </table>
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

#### Setup development environment:
```bash
docker-compose build
docker-compose up -d
docker-compose exec php composer --working-dir=/srv/sylius install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application build
docker-compose exec php bin/console assets:install public
docker-compose exec php bin/console doctrine:schema:create
docker-compose exec php bin/console sylius:fixtures:load -n
```
#### Running tests:
```bash
docker-compose exec php sh
bin/console doc:sche:cre
cd ../..
vendor/bin/phpcs
vendor/bin/phpstan analyse src/ --level max
vendor/bin/phpunit
vendor/bin/behat
```
