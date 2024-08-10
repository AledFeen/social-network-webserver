<?php

namespace App\Services\Paginate;

class PaginatedResponse
{
    public $data;
    public $currentPage;
    public $lastPage;
    public $total;
    public function __construct($data, $currentPage, $lastPage, $total)
    {
        $this->data = $data;
        $this->currentPage = $currentPage;
        $this->lastPage = $lastPage;
        $this->total = $total;
    }
    public function toArray()
    {
        return [
            'data' => $this->data,
            'current_page' => $this->currentPage,
            'last_page' => $this->lastPage,
            'total' => $this->total,
        ];
    }
}
