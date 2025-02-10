<?php

namespace App\Traits;

trait PaginationTrait
{
    public function  returnPaginationInformation($dataValue)
    {
        $pagination = [
            'meta' => [
                'currentPage' => $dataValue->currentPage(),
                'lastPage' => $dataValue->lastPage(),
                'perPage' => $dataValue->perPage(),
                'total' => $dataValue->total(),
            ],
            'links' => [
                'currentPageUrl' => $dataValue->url($dataValue->currentPage()),
                'previousPageUrl' => $dataValue->previousPageUrl(),
                'nextPageUrl' => $dataValue->nextPageUrl(),
                'lastPageUrl' => url("/api/notifications?page=".$dataValue->lastPage()),
            ]
        ];

        return $pagination;
    }
}
