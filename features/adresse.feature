Feature: Gestion des fiches
  Je suis connecté
  J' ajoute une adresse
  J' édite une adresse
  Je supprime une adresse

  Background:
    Given I am logged in as an admin
    Given I am on "/adresse/"

  Scenario: Ajout une adresse
    Then I follow "Ajouter une adresse"
    And I fill in "adresse[nom]" with "Chez Monica"
    And I fill in "adresse[rue]" with "Rue Dupont"
    And I fill in "adresse[numero]" with "31"
    And I fill in "adresse[cp]" with "6900"
    And I fill in "adresse[localite]" with "Marche"
    And I press "Sauvegarder"
    Then I should see "Chez Monica"

  Scenario: Modifier une adresse
    Then I follow "Place Rouge"
    Then I follow "Modifier"
    And I fill in "adresse[numero]" with "Place Verte"
    And I press "Sauvegarder"
    Then I should see "Place Verte"

  Scenario: Supprimer une adresse
    Then I follow "Place Rouge"
    Then I press "Supprimer l'adresse"
   # Then print last response
    Then I should see "L'adresse a bien été supprimée"
