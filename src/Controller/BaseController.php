<?php


namespace App\Controller;

use App\Interfaces\SerializableInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class BaseController extends AbstractController
{
    /**
     * @param SerializableInterface $item
     * @return array
     */
    public function item(SerializableInterface $item): array
    {
        return $item->serialize();
    }

    /**
     * @param SerializableInterface[] $data
     * @return array
     */
    public function collection(array $data): array
    {
        $result = [];

        foreach ($data as $item) {
            $result[] = $this->item($item);
        }

        return $result;
    }
}
