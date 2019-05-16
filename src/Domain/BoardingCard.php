<?php

namespace TravelRouter\Domain;

final class BoardingCard
{
    private $origin;
    private $destination;
    private $modeOfTransport;
    private $seat;
    private $comment;

    public function __construct(
        string $origin,
        string $destination,
        string $modeOfTransport,
        ?string $seat,
        ?string $comment
    )
    {
        $this->origin = $origin;
        $this->destination = $destination;
        $this->modeOfTransport = $modeOfTransport;
        $this->seat = $seat;
        $this->comment = $comment;
    }

    public function origin(): string
    {
        return $this->origin;
    }

    public function destination(): string
    {
        return $this->destination;
    }

    public function modeOfTransport(): string
    {
        return $this->modeOfTransport;
    }

    public function seat(): ?string
    {
        return $this->seat;
    }

    public function comment(): ?string
    {
        return $this->comment;
    }

}
