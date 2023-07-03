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
        $characters = [];
        $id = 0;
        $res = $this->client->request('GET', 'https://witcher-archive.fandom.com/fr/wiki/Cat%C3%A9gorie:Personnages_dans_The_Witcher');
        $crawler = new Crawler((string) $res->getBody(), 'https://witcher-archive.fandom.com');
        $crawler->filter('.category-page__member-link')->each(function (Crawler $node, $i) use (&$characters, &$id) {
            $characterName = $node->text();
            echo "Récupération des infos du personnage : " . $characterName . "\n";

            $characterLink = $node->link()->getUri();
            $res = $this->client->request('GET', $characterLink);
            $characterCrawler = new Crawler((string) $res->getBody(), 'https://witcher-archive.fandom.com');
            
            $description = $characterCrawler->filter('.mw-parser-output p')->slice(0, 3)->each(function (Crawler $node, $i) {
                return $node->text();
            });

            $associatedContents = $characterCrawler->filter('.mw-parser-output p a')->each(function (Crawler $node, $i) {
                $content = new \stdClass();
                $content->text = $node->text();
                $content->href = $node->attr('href');
                return $content;
            });

            // Create a new object and set its properties
            $character = new \stdClass();
            $character->id = $id++;
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
        $fetchedUrls = [];
        $count = 0;
        $id = 0;
        foreach ($characters as $character) {
            foreach ($character->associatedContents as $associatedContent) {
                if ($count >= 200) {
                    break 2;
                }

                $contentLink = $associatedContent->href;
                if (!preg_match('/^https?:\/\//', $contentLink)) {
                    $contentLink = 'https://witcher-archive.fandom.com' . $contentLink;
                }

                if (isset($fetchedUrls[$contentLink])) {
                    continue;
                }

                echo "Récupération du contenu associé : " . $associatedContent->text . "\n";

                try {
                    $res = $this->client->request('GET', $contentLink);
                    $crawler = new Crawler((string) $res->getBody(), 'https://witcher-archive.fandom.com');

                    $descriptionNode = $crawler->filter('.mw-parser-output p')->first();
                    $description = $descriptionNode->count() > 0 ? $descriptionNode->text() : '';

                    $content = new \stdClass();
                    $content->id = $id++;
                    $content->characterId = $character->id;
                    $content->name = $associatedContent->text;
                    $content->description = $description;

                    $contents[] = $content;
                    $fetchedUrls[$contentLink] = true;
                    $count++;
                } catch (\Exception $e) {
                    echo "Une erreur s'est produite lors de la récupération du contenu associé : " . $e->getMessage() . "\n";
                }
            }
        }

        return $contents;
    }

    public function saveData($characters, $contents)
    {
        file_put_contents(__DIR__ . '/data/characters.json', json_encode($characters));
        file_put_contents(__DIR__ . '/data/contents.json', json_encode($contents));
    }

    public function getSavedCharacters()
    {
        $data = file_get_contents(__DIR__ . '/data/characters.json');
        return json_decode($data);
    }

    public function getSavedContents()
    {
        $data = file_get_contents(__DIR__ . '/data/contents.json');
        return json_decode($data);
    }
}
