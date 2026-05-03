<?php

namespace app\src\Services;

use Yii;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Сервис геокодирования для Yandex Geocoder API.
 */
class GeocoderService
{
    private const string API_URL = 'https://geocode-maps.yandex.ru/1.x/';

    /**
     * Возвращает географические координаты и город по адресу.
     *
     * @param string $location адрес для геокодирования
     * @return array|null координаты или null если не найдено
     */
    public function search(string $location): ?array
    {
        $client = new Client(['timeout' => 5]);

        try {
            $response = $client->request('GET', self::API_URL, [
                'query' => [
                    'apikey' => Yii::$app->params['yandexApiKey'],
                    'geocode' => $location,
                    'format' => 'json',
                    'lang' => 'ru_RU',
                    'results' => 1,
                ],
            ]);
        } catch (GuzzleException $e) {
            Yii::error($e, __METHOD__);
            return null;
        }

        $data = json_decode($response->getBody()->getContents(), true);

        if (!is_array($data)) {
            return null;
        }

        $features = $data['response']['GeoObjectCollection']['featureMember'] ?? [];
        if (empty($features)) {
            return null;
        }

        $geoObject = $features[0]['GeoObject'];

        $point = $geoObject['Point']['pos'] ?? null;
        if (!$point) {
            return null;
        }

        [$lng, $lat] = explode(' ', $point);

        return ['lat' => (float) $lat, 'lng' => (float) $lng, 'city' => $this->extractCity($geoObject)];
    }

    /**
     * Извлекает название города из ответа геокодера.
     */
    private function extractCity(array $geoObject): ?string
    {
        $components = $geoObject['metaDataProperty']['GeocoderMetaData']['Address']['Components'] ?? [];

        foreach ($components as $component) {
            if (($component['kind'] ?? null) === 'locality') {
                return $component['name'] ?? null;
            }
        }

        return null;
    }
}
