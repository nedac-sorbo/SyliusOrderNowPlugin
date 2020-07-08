@theme @javascript
Feature:
  As a customer
  I need to be able to add products to my cart from the product card on the product review index page
  So that I'm more likely to buy the product

  Background:
    Given I want to configure the channel "United States"
    And the store has a product option "Color"
    And this product option has the "Red" option value with code "red"
    And this product option has the "Green" option value with code "green"
    And this product option has the "Blue" option value with code "blue"
    And the store has a "Hat" configurable product
    And this product has this product option
    And this product is available in "Red" Color priced at "$25.17"
    And this product is available in "Green" Color priced at "$23.17"
    And this product is available in "Blue" Color priced at "$27.83"
    And I check this product's reviews

  Scenario: Adding product to cart from the create product review page
    When I click the order now button on the review index page
    And I see the summary of my cart
    Then there should be one item in my cart
