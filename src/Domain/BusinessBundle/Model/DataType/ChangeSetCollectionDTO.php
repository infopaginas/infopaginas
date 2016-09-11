<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.08.16
 * Time: 10:14
 */

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