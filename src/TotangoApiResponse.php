<?php

namespace SH\Totango;

use Throwable;

class TotangoApiResponse
{
    /**
     * @param  array{
     *     _error?: string,
     *     _revision: string,
     *     _type: string,
     *     _version: int,
     *     response: array,
     *     service_id: string,
     *     status: string,
     *     took?: int,
     * }  $data
     *
     * @return self
     */
    public static function fromApiResponse(array $data): self
    {
        return match ($data['status']) {
            'success' => new self(
                $data['status'],
                $data['response'],
                [
                    '_revision' => $data['_revision'],
                    '_type' => $data['_type'],
                    '_version' => $data['_version'],
                    'service_id' => $data['service_id'],
                    'took' => $data['took'],
                ]
            ),
            'failed' => new self(
                $data['status'],
                $data['_error'],
                [
                    '_error' => $data['_error'],
                    '_revision' => $data['_revision'],
                    '_type' => $data['_type'],
                    '_version' => $data['_version'],
                    'service_id' => $data['service_id'],
                ]
            ),
        };
    }

    public function __construct(
        public string $status,
        public array|string|Throwable $response,
        public array $meta
    ) {
    }
}
