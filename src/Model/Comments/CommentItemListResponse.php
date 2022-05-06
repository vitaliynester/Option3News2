<?php

namespace App\Model\Comments;

class CommentItemListResponse
{
    /** @var CommentItemResponse[] */
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
