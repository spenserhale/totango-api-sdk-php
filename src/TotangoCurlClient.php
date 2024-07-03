<?php

namespace SH\Totango;

use JsonException;

readonly class TotangoCurlClient implements TotangoClientInterface
{
    public function __construct(
        private string $token
    ) {
    }

    public function request(
        string $url,
        array $query,
        array $headers = []
    ): TotangoApiResponse {
        $headers[] = 'app-token:'.$this->token;
        $headers[] = 'Content-Type: application/x-www-form-urlencoded';

        try {
            $queryString = urlencode(json_encode($query, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            return new TotangoApiResponse('exception', $e, []);
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL            => 'https://api.totango.com/api/v1/search/accounts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => "query=$queryString",
            CURLOPT_HTTPHEADER     => $headers,
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        if ( ! str_starts_with($response, '{')) {
            return new TotangoApiResponse('failed', $response, []);
        }

        try {
            return TotangoApiResponse::fromApiResponse(json_decode($response, true, 512, JSON_THROW_ON_ERROR));
        } catch (JsonException $e) {
            return new TotangoApiResponse('exception', $e, []);
        }
    }
}
