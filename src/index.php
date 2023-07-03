<?php

require 'vendor/autoload.php';
use Charleslambret\Witchercharacterscrapping\WitcherScraper;

$scraper = new WitcherScraper();

// Check if data files already exist
if (file_exists(__DIR__ . '/data/characters.json') && file_exists(__DIR__ . '/data/contents.json')) {
    echo "Un jeu de données est déjà sauvegardé dans le package. Voulez-vous le mettre à jour ? (y/n) ";
    $response = trim(fgets(STDIN));

    if (strtolower($response) === 'y') {
        echo "Début du scrapping des personnages...\n";
        $characters = $scraper->getCharacters();

        echo "Début du scrapping des contenus associés...\n";
        $contents = $scraper->getAssociatedContents($characters);

        // Save the scraped data
        $scraper->saveData($characters, $contents);

        echo "Scrapping terminé. Sauvegarde des données...\n";
    }
}

// Retrieve the saved data
$characters = $scraper->getSavedCharacters();
$contents = $scraper->getSavedContents();

echo "Voici les personnages :\n";
foreach ($characters as $character) {
    print_r($character);
}

echo "Voici les contenus associés :\n";
foreach ($contents as $content) {
    print_r($content);
}
