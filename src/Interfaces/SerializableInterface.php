<?php

namespace App\Interfaces;

interface SerializableInterface
{
    /**
     * @return array
     */
    public function serialize(): array;
}
