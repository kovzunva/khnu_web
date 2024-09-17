<?php

// app/Http/Controllers/ImportController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use Symfony\Component\DomCrawler\Crawler;

class ImportController extends Controller
{
    public function edition(Request $request){  
        $url = $request->input('url');

        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL))
            return response()->json(['error' => 'Некоректна адреса URL']);

        if (strpos($url, 'https://www.yakaboo.ua') === false && strpos($url, 'https://book-ye.com.ua') === false
            && strpos($url, 'https://vivat-book.com.ua') === false && strpos($url, 'https://nashformat.ua') === false) {
            return response()->json(['error' => 'Імпорт з цього сайту не підтримується. Сервіси для імпорту: Наш Формат, Vivat, Книгарня "Є", YAKABOO']);
        }

        try {
            $client = new Client();
            $response = $client->get($url);
            $htmlContent = $response->getBody()->getContents();
            $crawler = new Crawler($htmlContent);

            $edition = new \stdClass();

            switch (true) {
                // YAKABOO
                case strpos($url, 'https://www.yakaboo.ua') !== false:
                    $edition->name = $crawler->filter('.base-product__title')->text();
                    $edition->name = trim(str_replace(['Електронна книга', 'Книга'], '', $edition->name));
                    $edition->avtor = $crawler->filter('.base-product__author')->text();
                    $edition->about = $crawler->filter('.main__description')->text();
                    $edition->img = $crawler->filter('.slide__item img')->attr('src');

                    
                    $scriptTag = $crawler->filter('script:contains("window.__INITIAL_STATE__")')->first();
                    $scriptText = $scriptTag->text();
                    $decodedScriptText = html_entity_decode($scriptText);

                    preg_match('/"attribute_label":"Видавництво","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->publisher = $matches[1];
                    preg_match('/"attribute_label":"Тип обкладинки","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->type_of_cover = $matches[1];
                    preg_match('/"attribute_label":"Мова","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->language = $matches[1];
                    preg_match('/"attribute_label":"Кількість сторінок","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->pages = $matches[1];
                    preg_match('/"attribute_label":"Серія книг","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->seria = $matches[1];
                    preg_match('/"attribute_label":"Формат","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->format = $matches[1];
                    preg_match('/"attribute_label":"ISBN","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->isbn = $matches[1];
                    preg_match('/"attribute_label":"Ілюстрації","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->illustrations = $matches[1];
                    preg_match('/"attribute_label":"Ілюстратор","label":"([^"]+)"/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->illustrator = $matches[1];
                    preg_match('/"attribute_label":"Рік видання","label":(\d+)/', $decodedScriptText, $matches);
                    if (isset($matches[1])) $edition->year = $matches[1];


                    break;
        
                // Книгарня "Є"
                case strpos($url, 'https://book-ye.com.ua') !== false:
                    $edition->name = $crawler->filter('.card__title')->text();
                    $edition->avtor = $this->extractTextFromInfoSection($crawler, "Автор");
                    $edition->publisher = $this->extractTextFromInfoSection($crawler, "Видавництво");
                    $edition->year = $this->extractTextFromInfoSection($crawler, "Рік видання");
                    $edition->language = $this->extractTextFromInfoSection($crawler, "Мова");
                    $edition->pages = $this->extractTextFromInfoSection($crawler, "Кількість сторінок");
                    $edition->isbn = $this->extractTextFromInfoSection($crawler, "ISBN");
                    $edition->type_of_cover = $this->extractTextFromInfoSection($crawler, "Обкладинка");
                    $edition->format = $this->extractTextFromInfoSection($crawler, "Формат");
                    $edition->translator = $this->extractTextFromInfoSection($crawler, "Перекладач");
                    $edition->about = $crawler->filter('.article__description.content__txt.article__annotation')->text();
                    $edition->img = "https://book-ye.com.ua" . $crawler->filter('.preview__media')->attr('src');
                    break;

                // Vivat
                case strpos($url, 'https://vivat-book.com.ua') !== false:
                    if ($crawler->filter('#pagetitle')->count() > 0) 
                    $edition->name = $crawler->filter('#pagetitle')->text();
                    if ($crawler->filter('td.type:contains("Автори") + td.value a')->count() > 0) 
                    $edition->avtor = $crawler->filter('td.type:contains("Автори") + td.value a')->text();
                    $edition->publisher = $crawler->filter('td.type:contains("Видавництво") + td.value a')->text();
                    if ($crawler->filter('td.type:contains("Рік видання") + td.value')->count() > 0) 
                    $edition->year = $crawler->filter('td.type:contains("Рік видання") + td.value')->text();
                    if ($crawler->filter('td.type:contains("Мова видання") + td.value')->count() > 0) 
                    $edition->language = $crawler->filter('td.type:contains("Мова видання") + td.value')->text();
                    if ($crawler->filter('td.type:contains("Кількість сторінок") + td.value')->count() > 0) 
                    $edition->pages = $crawler->filter('td.type:contains("Кількість сторінок") + td.value')->text();
                    if ($crawler->filter('td.type:contains("ISBN") + td.value')->count() > 0) 
                    $edition->isbn = $crawler->filter('td.type:contains("ISBN") + td.value')->text();
                    if ($crawler->filter('td.type:contains("Обкладинка") + td.value')->count() > 0) 
                    $edition->type_of_cover = $crawler->filter('td.type:contains("Обкладинка") + td.value')->text();
                    if ($crawler->filter('.description')->count() > 0) 
                    $edition->about = $crawler->filter('.description')->text();
                    if ($crawler->filter('#card-lightgallery-photo-thumbs .active img')->count() > 0) 
                    $edition->img = 'https://vivat-book.com.ua'.$crawler->filter('#card-lightgallery-photo-thumbs .active img')->first()->attr('src');
                    break;       
                // Наш Формат
                case strpos($url, 'https://nashformat.ua') !== false:
                    $edition->avtor = ($authorNode = $crawler->filter('.author_title'))->count() > 0 ? $authorNode->text() : null;
                    
                    $edition->name = ($titleNode = $crawler->filter('h1'))->count() > 0 ? $titleNode->each(function (Crawler $node) {
                        return trim(str_replace(['Електронна книга «','Книга «', '»'], '', $node->text()));
                    }) : null;
                
                    $edition->publisher = $crawler->filter('td.attr:contains("Видавництво") + td.value a')->count() > 0 ? $crawler->filter('td.attr:contains("Видавництво") + td.value a')->text() : null;
                    $edition->translator = $crawler->filter('td.attr:contains("Перекладачі") + td.value a')->count() > 0 ? $crawler->filter('td.attr:contains("Перекладачі") + td.value a')->text() : null;
                    $edition->year = $crawler->filter('td.attr:contains("Рік видання") + td.value')->count() > 0 ? $crawler->filter('td.attr:contains("Рік видання") + td.value')->text() : null;
                    $edition->year = trim(strstr($edition->year, '-', true));
                    $edition->language = $crawler->filter('td.attr:contains("Мова") + td.value')->count() > 0 ? $crawler->filter('td.attr:contains("Мова") + td.value')->text() : null;
                    $edition->pages = $crawler->filter('td.attr:contains("Кількість сторінок") + td.value')->count() > 0 ? $crawler->filter('td.attr:contains("Кількість сторінок") + td.value')->text() : null;
                    $edition->isbn = $crawler->filter('td.attr:contains("ISBN") + td.value')->count() > 0 ? $crawler->filter('td.attr:contains("ISBN") + td.value')->text() : null;
                    $edition->type_of_cover = $crawler->filter('td.attr:contains("Палітурка") + td.value')->count() > 0 ? $crawler->filter('td.attr:contains("Палітурка") + td.value')->text() : null;
                    $edition->about = $crawler->filter('.part-annotation.isShow')->count() > 0 ? $crawler->filter('.part-annotation.isShow')->text() : null;
                    $edition->img = $crawler->filter('#main_image')->count() > 0 ? $crawler->filter('#main_image')->attr('href') : null;
                    break;  
            }
            
            if (isset($edition->publisher)){
            $edition->publisher = preg_replace('/"/', '«', $edition->publisher, 1); 
            $edition->publisher = preg_replace('/"/', '»', $edition->publisher, 1); 
            }

            return response()->json(['edition' => $edition]);
        }
        catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Для Книгарні "Є" 
    public function extractTextFromInfoSection(Crawler $crawler, $label)
    {
        $infoElements = $crawler->filter('div.card__info');
        $text = '';

        foreach ($infoElements as $element) {
            if (strpos($element->textContent, $label) !== false) {
                $text = str_replace($label, '', $element->textContent);
                $text = trim($text, " \t\n\r\0\x0B:");
                break;
            }
        }

        return $text;
    }

    // Для Vivat
    private function extractTextFromInfoSectionVivat($crawler, $label)
    {
        $infoSection = $crawler->filter('.info-section');
        $labelElement = $infoSection->filter('span:contains("' . $label . '")');
        $text = $labelElement->nextAll()->text();

        return trim($text);
    }
    
}

