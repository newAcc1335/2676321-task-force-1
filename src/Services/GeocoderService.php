<?php

namespace app\src\Services;

use Yii;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GeocoderService
{
    private const string API_URL = 'https://geocode-maps.yandex.ru/1.x/';

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

        $point = $features[0]['GeoObject']['Point']['pos'] ?? null;
        if (!$point) {
            return null;
        }

        [$lng, $lat] = explode(' ', $point);

        return ['lat' => (float) $lat, 'lng' => (float) $lng];
    }
}
