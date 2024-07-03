<?php

namespace SH\Totango;

use Throwable;

/**
 * Use the following parameters in the API call's query parameters:
 * terms (mandatory): An array contains one or more filter conditions to use for the search
 * fields (mandatory): list of fields to return as results. Note that the account name and account-id are always returned as well.
 * count (optional, default: 1000): The maximum number of accounts to return in the result set. If there are more results, you should use paging* to cycle through the result set. The max value for count is 1000.
 * offset (optional, default: 0): Record number (0 states "start at record 0"). The record size can be defined using the count parameter (and limited to 1000).
 * To page through results, ask for 1000 records (count: 1000). If you receive 1000 records, assume there’s more, in which case you want to pull the next 1000 records (offset: 1000…then 2000…etc.). Repeat paging until the number of records returned is less than 1000.
 * sort_by, sort_order (optional, default display_name and ASC): The order to sort the result set by.
 *
 * @see https://support.totango.com/hc/en-us/articles/204174135-Search-API-accounts-and-users
 */
readonly class TotangoSearchApi
{
    public function __construct(
        private TotangoClientInterface $client,
        private string $host = 'https://api.totango.com'
    ) {
    }

    /**
     * Retrieve accounts that match the given terms and fields up to the given count (max 1000).
     *
     * @return Throwable|array{
     *    total_hits: int,
     *    hits: array<
     *      array{
     *        name: string,
     *        display_name: string,
     *        selected_fields: array[]
     *      }
     *    >,
     *    stats: array
     *  }
     */
    public function accounts(
        array $terms,
        array $fields,
        int $count = 1000,
        int $offset = 0,
        string $sort_by = 'display_name',
        string $sort_order = 'ASC'
    ): array|Throwable {
        $response = $this->client->request(
            "$this->host/api/v1/search/accounts",
            [
                'terms'      => $terms,
                'fields'     => $fields,
                'count'      => $count,
                'offset'     => $offset,
                'sort_by'    => $sort_by,
                'sort_order' => $sort_order,
                'scope'      => 'all'
            ]
        );

        return match ($response->status) {
            'failed' => new TotangoApiException($response->response),
            'exception' => $response->response,
            default => $response->response['accounts'] ?? [],
        };
    }

    /**
     * Retrieve all accounts that match the given terms and fields by paging through the results.
     *
     * @return Throwable|array< array{
     *   name: string,
     *   display_name: string,
     *   selected_fields: array[]
     * }>
     */
    public function allAccounts(
        array $terms,
        array $fields,
        string $sort_by = 'display_name',
        string $sort_order = 'ASC'
    ): array|Throwable {
        $accounts = [];
        $count = 1000;
        $offset = 0;
        $total = null;

        do {
            $response = $this->accounts($terms, $fields, $count, $offset, $sort_by, $sort_order);
            if ($response instanceof Throwable) {
                return $response;
            }

            $accounts[] = $response['hits'];
            $offset += $count;
            $total ??= $response['total_hits'];
        } while ($offset < $total);

        return array_merge(...$accounts);
    }
}
