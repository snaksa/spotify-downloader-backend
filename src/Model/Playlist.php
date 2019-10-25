<?php

namespace App\Model;

use App\Interfaces\SerializableInterface;

class Playlist implements SerializableInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $total_tracks;

    /**
     * @var array
     */
    private $images;

    /**
     * Auth constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->total_tracks = $data['tracks']['total'];
        $this->images = $data['images'];
    }

    public function serialize(): array
    {
        return [
            'id' => $this->getId(),
            'name' => $this->getName(),
            'total' => $this->getTotalTracks(),
            'images' => $this->getImages(),
        ];
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getTotalTracks(): string
    {
        return $this->total_tracks;
    }

    /**
     * @param string $total_tracks
     */
    public function setTotalTracks(string $total_tracks): void
    {
        $this->total_tracks = $total_tracks;
    }

    /**
     * @return array
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * @param array $images
     */
    public function setImages(array $images): void
    {
        $this->images = $images;
    }
}
