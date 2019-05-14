Feature: Processing order
  In order to create freight bills
  As a user
  I need to be able to place an order

  Rules:
  - there are three delivery types
  - ordered items comes from warehouses where fewest of them are in stock

  Background:
    Given there is a warehouse "A"
    And there is a warehouse "B"

  Scenario: Buying single product
    Given in warehouse "A" there are 3 products "Banana"
    When I order products with delivery type "standard"
      | productName | quantity |
      | Banana      | 3        |
    Then warehouse "A" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Banana      | 3        |

  Scenario: Buying single product with express delivery method
    Given in warehouse "A" there are 3 products "Banana"
    When I order products with delivery type "express"
      | productName | quantity |
      | Banana      | 3        |
    Then warehouse "A" should receive freight bill with delivery type "express" and products:
      | productName | quantity |
      | Banana      | 3        |

  Scenario: Buying single product with fast delivery method
    Given in warehouse "A" there are 3 products "Banana"
    When I order products with delivery type "fast"
      | productName | quantity |
      | Banana      | 3        |
    Then warehouse "A" should receive freight bill with delivery type "fast" and products:
      | productName | quantity |
      | Banana      | 3        |

  Scenario: Buying many product
    Given in warehouse "A" there are 3 products "Banana"
    And in warehouse "A" there are 10 products "Kiwi"
    When I order products with delivery type "standard"
      | productName | quantity |
      | Banana      | 3        |
      | Kiwi        | 2        |
    Then warehouse "A" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Banana      | 3        |
      | Kiwi        | 2        |

  Scenario: Buying single product from warehouse with fewest items in stock
    Given in warehouse "A" there are 3 products "Banana"
    And in warehouse "B" there are 5 products "Banana"
    When I order products with delivery type "standard"
      | productName | quantity |
      | Banana      | 3        |
    Then warehouse "A" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Banana      | 3        |

  Scenario: Buying single product from the warehouse with sufficient items in only one stock
    Given in warehouse "A" there are 3 products "Banana"
    And in warehouse "B" there are 5 products "Banana"
    When I order products with delivery type "standard"
      | productName | quantity |
      | Banana      | 4        |
    Then warehouse "B" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Banana      | 4        |

  Scenario: Buying many products when they are available in different warehouses
    Given in warehouse "A" there are 3 products "Banana"
    And in warehouse "B" there are 2 products "Kiwi"
    When I order products with delivery type "standard"
      | productName | quantity |
      | Banana      | 3        |
      | Kiwi        | 2        |
    Then warehouse "A" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Banana      | 3        |
    And warehouse "B" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Kiwi        | 2        |

  Scenario: Buying many products when they are available in many warehouses
    Given in warehouse "A" there are 5 products "Banana"
    And in warehouse "B" there are 10 products "Kiwi"
    And in warehouse "B" there are 10 products "Banana"
    And in warehouse "B" there are 5 products "Kiwi"
    When I order products with delivery type "standard"
      | productName | quantity |
      | Banana      | 3        |
      | Kiwi        | 2        |
    Then warehouse "A" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Banana      | 3        |
    And warehouse "B" should receive freight bill with delivery type "standard" and products:
      | productName | quantity |
      | Kiwi        | 2        |
