<?php

namespace Domain\BusinessBundle\Model\DataType;

class ChangeSetArrayDTO
{
    private $data;

    public function __construct($collection)
    {
        foreach ($collection as $key => $item) {
            $this->data[] = [
                'id' => $key,
                'value' => json_encode($item),
            ];
        }
    }

    public function getJSONContent()
    {
        return json_encode($this->data);
    }
}