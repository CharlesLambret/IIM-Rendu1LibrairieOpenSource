<?php

require 'vendor/autoload.php';
use Charleslambret\Witchercharacterscrapping\WitcherScraper;

$scraper = new WitcherScraper();

echo "Début du scrapping des personnages...\n";
$characters = $scraper->getCharacters();

echo "Début du scrapping des contenus associés...\n";
$contents = $scraper->getAssociatedContents($characters);

echo "Scrapping terminé. Voici les contenus associés :\n";
foreach ($contents as $content) {
    print_r($content);
}

