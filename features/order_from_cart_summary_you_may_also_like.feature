@order_now @javascript
Feature:
  As a customer
  I need to be able to add products to my cart from the products cards on cart summary page
  So that I'm more likely to buy the products

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product option "Color"
    And this product option has the "Red" option value with code "red"
    And this product option has the "Green" option value with code "green"
    And this product option has the "Blue" option value with code "blue"
    And the store has a "Hat" configurable product
    And this product has this product option
    And this product is available in "Red" Color priced at "$25.17"
    And this product is available in "Green" Color priced at "$23.17"
    And this product is available in "Blue" Color priced at "$27.83"
    And I visit the homepage
    And I click the order now button

  Scenario: Adding a product to the cart from the products you may also like section on the cart summary page
    When I see the summary of my cart
    And I click the order now button on the cart summary page
    Then I should see "Hat" with quantity 2 in my cart
