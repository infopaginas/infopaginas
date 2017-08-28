<?php

namespace Domain\BusinessBundle\Model\DataType;

class ChangeSetCollectionDTO
{
    private $data;

    public function __construct($collection)
    {
        foreach ($collection as $item) {
            $this->data[] = [
                'id' => $item->getId(),
                'value' => (string)$item,
            ];
        }
    }

    public function getJSONContent()
    {
        return json_encode($this->data);
    }
}
