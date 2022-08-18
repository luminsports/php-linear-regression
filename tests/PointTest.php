<?php

namespace LuminSports\LinearRegression\Test;

use LuminSports\LinearRegression\Point;
use PHPUnit\Framework\TestCase;

class PointTest extends TestCase
{
    public function testXAndYCanBeGetAndSetOnPoint()
    {
        $point = new Point(1, 2);

        $this->assertEquals($point->getX(), 1);
        $this->assertEquals($point->getY(), 2);

        $point->setX(10.2);
        $point->setY(5.82E-10);

        $this->assertEquals($point->getX(), 10.2);
        $this->assertEquals($point->getY(), 5.82E-10);
    }
}
