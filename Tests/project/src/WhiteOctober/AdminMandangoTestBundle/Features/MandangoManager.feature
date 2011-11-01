Feature: Mandango Manager

    Background:
        Given The database is empty

    Scenario: The documents are listed
        Given I have an author "Pablo"
          And I have an author "Pepe"
         When I am on "/admin/mandango/author"
         Then I should see "Pablo"
         Then I should see "Pepe"

    Scenario: The documents can be filtered by the simple filter
        Given I have an author "Pablo"
          And I have an author "Pepe"
         When I am on "/admin/mandango/author"
          And I fill in "q" with "Pabl"
          And I press "Filter"
         Then I should see "Pablo"
          And I should not see "Pepe"

    Scenario: The documents can be created
        Given I am on "/admin/mandango/author"
         When I follow "New"
          And I fill in "form[name]" with "Pablo"
          And I press "Save"
        Then I should be on "/admin/mandango/author"
         And I should see "Pablo"

    Scenario: The documents can be showed
        Given I have an author "Pablo"
          And I am on "/admin/mandango/author"
         When I follow "Show"
         Then I should see "Pablo"

    Scenario: The documents can be edited
        Given I have an author "Pablo"
          And I am on "/admin/mandango/author"
         When I follow "Edit"
          And I fill in "form[name]" with "Pepe"
          And I press "Save"
         Then I should be on "/admin/mandango/author"
          And I should see "Pepe"
          And I should not see "Pablo"

    Scenario: The documents can be deleted
        Given I have an author "Pablo"
          And I am on "/admin/mandango/author"
         When I press "Delete"
        Then I should be on "/admin/mandango/author"
         And I should not see "Pablo"
