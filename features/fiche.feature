Feature: Gestion des fiches
  Je suis connecté
  J' ajoute une fiche
  J' édite une fiche
  Je supprime une fiche
  Ses jours de travail animateur

  Background:
    Given I am logged in as an admin
    Given I am on "/fiche/"
    Then I should see "Rechercher une fiche"
    Then I fill in "search_fiche[nom]" with "manda"
    And I press "Rechercher"
    Then I should see "MANDABAR"

  Scenario: Ajout une fiche
    Then I follow "Ajouter une fiche"
    And I fill in "fiche[societe]" with "SODEBO"
    And I press "Sauvegarder"
    Then I should see "SODEBO"

  Scenario: Modifier une fiche
    Then I follow "MANDABAR"
    Then I follow "Modifier"
    And I fill in "fiche[societe]" with "MANDARINE"
    And I press "Sauvegarder"
    Then I should see "MANDARINE"

  Scenario: Supprimer une fiche
    Then I follow "MANDABAR"
    Then I press "Supprimer la fiche"
   # Then print last response
    Then I should see "La fiche a bien été supprimée"
