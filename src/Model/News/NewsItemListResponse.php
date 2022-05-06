<?php

namespace App\Model\News;

class NewsItemListResponse
{
    /** @var NewsItemResponse[] */
    private array $items;

    public function getItems(): array
    {
        return $this->items;
    }

    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
