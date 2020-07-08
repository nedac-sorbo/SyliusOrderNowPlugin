@theme @javascript
Feature:
  As a customer
  I need to be able to add products to my cart from the latest products cards on the homepage
  So that I'm more likely to buy the products

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

  Scenario: Adding a product to the cart from the latest products section on the homepage
    When I visit the homepage
    And I click the order now button
    And I see the summary of my cart
    Then there should be one item in my cart
