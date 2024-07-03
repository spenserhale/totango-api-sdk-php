<?php

namespace SH\Totango;

interface TotangoClientInterface
{
    public function request(
        string $url,
        array $query,
        array $headers = []
    ): TotangoApiResponse;
}
