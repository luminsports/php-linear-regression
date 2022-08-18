<?php

namespace LuminSports\LinearRegression;

class Point
{
    public function __construct(protected float $x, protected float $y)
    {
    }

    public function getX(): float
    {
        return $this->x;
    }

    public function setX(float $x): static
    {
        $this->x = $x;

        return $this;
    }

    public function getY(): float
    {
        return $this->y;
    }

    public function setY(float $y): static
    {
        $this->y = $y;

        return $this;
    }

    public function toArray(): array
    {
        return [
            'x' => $this->getX(),
            'y' => $this->getY(),
        ];
    }
}
