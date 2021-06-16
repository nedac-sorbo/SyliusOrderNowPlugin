#@theme @javascript
#Feature: Display a flash message when product cannot be added to cart when the stock is insufficient
#  As a customer
#  I need to see a flash message when my product cannot be added to the cart because there is insufficient stock
#  In order to not lose customer engagement by showing an error page
#
#  Background:
#    Given I want to configure the channel "United States"
#    And the store classifies its products as "Hats"
#    And the store has a product option "Color"
#    And this product option has the "Red" option value with code "red"
#    And this product option has the "Green" option value with code "green"
#    And this product option has the "Blue" option value with code "blue"
#    And the store has a "Hat" configurable product
#    And this product has this product option
#    And this product is available in "Red" Color priced at "$25.17"
#    And this product is available in "Green" Color priced at "$23.17"
#    And this product is available in "Blue" Color priced at "$27.83"
#    And this product belongs to "Hats"
#    And this product is tracked by the inventory
#    And there are 1 units of product "Hat" available in the inventory
#
#  Scenario: Flash message is displayed on homepage
#    When I visit the homepage
#    And I add 2 units of color "red" to the cart with the order now button
#    Then I should be notified that there is insufficient stock for product "Hat"
#
#  Scenario: Flash message is displayed on product index page
#    When I browse products from taxon "Hats"
#    And I add 2 units of color "red" to the cart with the order now button on the product index page
#    Then I should be notified that there is insufficient stock for product "Hat" on the product index page
#
#  Scenario: Flash message is displayed on product show page
#    When I view product "Hat"
#    And I add 2 units of color "red" to the cart with the order now button on the product show page
#    Then I should be notified that there is insufficient stock for product "Hat" on the product show page
#
#  Scenario: Flash message is displayed on cart summary page
#    When I visit the homepage
#    And I click the order now button
#    And I see the summary of my cart
#    And I add 1 units of color "red" to the cart with the order now button on the cart summary page
#    Then I should be notified that there is insufficient stock for product "Hat" on the cart summary page
#
#  Scenario: Flash message is displayed on product review create page
#    When I want to review product "Hat"
#    And I add 2 units of color "red" to the cart with the order now button on the review create page
#    Then I should be notified that there is insufficient stock for product "Hat" on the review page
#
#  Scenario: Flash message is displayed on product review index page
#    When I check this product's reviews
#    And I add 2 units of color "red" to the cart with the order now button on the review index page
#    Then I should be notified that there is insufficient stock for product "Hat" on the review page
