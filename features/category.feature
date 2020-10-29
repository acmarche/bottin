Feature: Gestion des catégories
  Je suis connecté
  J' ajoute une catégorie
  J' édite une catégorie
  Je supprime une catégorie

  Background:
    Given I am logged in as an admin
    Given I am on "/category/"

  Scenario: Ajout une catégorie
    Then I follow "Ajouter une catégorie"
    And I fill in "category[name]" with "Sport"
    And I press "Sauvegarder"
    Then I should see "Sport"

  Scenario: Ajout une sous catégorie
    Then I follow "Commerces"
    Then I follow "Ajouter une sous catégorie"
    And I fill in "category[name]" with "Automobile"
    And I press "Sauvegarder"
    Then I should see "Automobile"

  Scenario: Modifier une catégorie
    Then I follow "Gyneco"
    Then I follow "Modifier"
    And I fill in "category[name]" with "Gynecologie"
    And I press "Sauvegarder"
    Then I should see "Gynecologie"

  Scenario: Supprimer une catégorie
    Then I follow "Pharmacie"
    Then I press "Supprimer la catégorie"
   # Then print last response
    Then I should see "La catégorie a bien été supprimée"
