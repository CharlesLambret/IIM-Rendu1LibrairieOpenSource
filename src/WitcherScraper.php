<?php

namespace Charleslambret\Witchercharacterscrapping;

use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class WitcherScraper
{
    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getCharacters()
    {
        $res = $this->client->request('GET', 'https://witcher-archive.fandom.com/fr/wiki/Cat%C3%A9gorie:Personnages_dans_The_Witcher');

        $crawler = new Crawler((string) $res->getBody(), 'https://witcher-archive.fandom.com');

        $characters = [];
        $crawler->filter('.category-page__member-link')->each(function (Crawler $node, $i) use (&$characters) {
            $characterName = $node->text();
            echo "Récupération des infos du personnage : " . $characterName . "\n";

            $characterLink = $node->link()->getUri();
            $res = $this->client->request('GET', $characterLink);
            $crawler = new Crawler((string) $res->getBody(), 'https://witcher-archive.fandom.com');
            
            $description = $crawler->filter('.mw-parser-output p')->slice(0, 3)->each(function (Crawler $node, $i) {
                return $node->text();
            });

            $associatedContents = $crawler->filter('.mw-parser-output p a')->each(function (Crawler $node, $i) {
                $content = new \stdClass();
                $content->text = $node->text();
                $content->href = $node->attr('href');
                return $content;
            });

            // Create a new object and set its properties
            $character = new \stdClass();
            $character->name = $characterName;
            $character->description = $description;
            $character->associatedContents = $associatedContents;

            $characters[] = $character;
        });

        return $characters;
    }

    public function getAssociatedContents($characters)
{
    $contents = [];
    foreach ($characters as $character) {
        foreach ($character->associatedContents as $associatedContent) {
            echo "Récupération du contenu associé : " . $associatedContent->text . "\n";

            $contentLink = 'https://witcher-archive.fandom.com' . $associatedContent->href;
            $res = $this->client->request('GET', $contentLink);
            $crawler = new Crawler((string) $res->getBody(), 'https://witcher-archive.fandom.com');

            $descriptionNode = $crawler->filter('.mw-parser-output p')->first();
            $description = $descriptionNode->count() > 0 ? $descriptionNode->text() : '';

            // Create a new object and set its properties
            $content = new \stdClass();
            $content->name = $associatedContent->text;
            $content->description = $description;

            $contents[] = $content;
        }
    }

    return $contents;
}

}
