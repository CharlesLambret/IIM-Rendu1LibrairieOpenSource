# Witcher Character Scrapping

Il s'agit d'une mini bibliothèque réalisée dans le cadre d'une devoir qui extrait les personnages de The Witcher et leurs descriptions ainsi que différentes infos sur l'univers. Il contient de base deux fichiers json contenant les listes. (src/data)

## Exigences
Cette bibliothèque nécessite les packages suivants :

guzzlehttp/guzzle: ^7.7

symfony/css-selector: ^6.3

symfony/dom-crawler: ^6.3


## Installation

Vous pouvez installer le package via composer :

composer require charleslambret/witchercharacterscrapping


## Utilisation

- Vous pouvez soit directement accéder aux données contenues dans les Json en les appelant dans votre code, par exemple : 

// Inclure l'autoloader de Composer
require __DIR__ . '/vendor/autoload.php';

// Utiliser la fonction file_get_contents pour obtenir le contenu du fichier JSON
$charactersJson = file_get_contents(__DIR__ . '/vendor/charleslambret/witchercharacterscrapping/src/data/characters.json');
$contentsJson = file_get_contents(__DIR__ . '/vendor/charleslambret/witchercharacterscrapping/src/data/contents.json');

// Utiliser la fonction json_decode pour convertir le contenu JSON en un tableau PHP
$characters = json_decode($charactersJson, true);
$contents = json_decode($contentsJson, true);

- Soit vous pouvez mettre à jour le jeu de données en installant le package et en rentrant : 

php src/index.php 

Il vous sera demandé si vous voulez mettre à jour le fichier ou non, répondez oui. Les personnages et contenus seront ensuite téléchargés. De par leur grande quantité, une limite de 200 contenus est fixée à chaque téléchargement. Vous pouvez la modifier dans src/WitcherScraper.php -> faites un controle F en recherchant "200" et vous trouverez directement la valeur à modifier.
