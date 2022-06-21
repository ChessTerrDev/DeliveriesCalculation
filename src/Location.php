<?php

namespace DeliveriesCalculation;

use DeliveriesCalculation\Constants;

class Location
{
    private ?string $ip;
    private ?array $geoData = null;

    public function __construct(?string $ip = null)
    {
        $this->ip = $ip;
        if (empty($this->ip) && !empty($_SERVER['REMOTE_ADDR'])) $this->ip = $_SERVER['REMOTE_ADDR'];

        if (Constants::LOCATION['SAVE_IN_SESSION'])
            $this->geoData = Session::getFromSession('GEO_DATA');

        if (!empty($this->ip) && $this->geoData == null) {
            $this->geoData = $this->getGeodataByDadata($ip);
            empty($this->geoData) && $this->geoData = $this->getGeodataByIpApi($ip);
            //empty($this->geoData) && $this->geoData = $this->getGeodataByGeoIp2($ip);

            if ($this->geoData) Session::addToSession($this->geoData, 'GEO_DATA');
        }
    }

    private function getGeodataByDadata($ip)
    {
        $httpClient = new \GuzzleHttp\Client([
                'allow_redirects' => false,
                'http_errors' => false,
            ]
        );
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Token ' . Constants::LOCATION['dadata']['token'],
        ];
        $param = [
            'ip' => $ip
        ];
        $response = $httpClient->get('https://suggestions.dadata.ru/suggestions/api/4_1/rs/iplocate/address', ['query' => $param, 'headers' => $headers]);
        $json = $response->getBody()->getContents();
        $result =  json_decode($json);

        if (empty($result->location->data->postal_code)) return null;

        $this->geoData['toPostalCode'] 		= $result->location->data->postal_code;
        $this->geoData['address'] 		    = $result->location->data->region .', '. $result->location->data->city;
        $this->geoData['city'] 		        = $result->location->data->city;
        $this->geoData['geo_lat']	 		= $result->location->data->geo_lat;
        $this->geoData['geo_lon']	 		= $result->location->data->geo_lon;
        $this->geoData['region_type']	 	= $result->location->data->region_type;
        $this->geoData['region']	 		= $result->location->data->region;

        return $this->geoData;
    }

    private function getGeodataByIpApi($ip)
    {
        $httpClient = new \GuzzleHttp\Client([
                'allow_redirects' => false,
                'http_errors' => false,
            ]
        );
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
        $param = [
          'lang' => 'ru'
        ];
        $response = $httpClient->get('http://ip-api.com/php/' . $ip, ['query' => $param, 'headers' => $headers]);
        $json = $response->getBody()->getContents();
        $result =  json_decode($json);

        if (empty($result->zip)) return null;

        $this->geoData['toPostalCode'] 		= $result->zip;
        $this->geoData['address'] 		    = $result->regionName . ', ' . $result->city;
        $this->geoData['city'] 		        = $result->city;
        $this->geoData['geo_lat']	 		= $result->lat;
        $this->geoData['geo_lon']	 		= $result->lon;
        $this->geoData['region_type']	 	= $result->region;
        $this->geoData['region']	 		= $result->regionName;

        return $this->geoData;
    }

    /**
     * @return array|void
     */
    public function getGeoData()
    {
        return !empty($this->geoData) ? $this->geoData : null;
    }



}