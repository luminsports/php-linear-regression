<?php

namespace LuminSports\LinearRegression;

class LeastSquares
{
    protected array $xCoords = [];

    protected array $yCoords = [];

    protected float $xSum;

    protected float $ySum;

    protected float $slope;

    protected float $intercept;

    protected float $rSquared;

    protected int $coordinateCount = 0;

    /**
     * Holds the y differences from the calculated regression line.
     *
     * @var float[]
     */
    protected array $yDifferences = [];

    /**
     * Holds the cumulative sum of yDifferences.
     *
     * @var float[]
     */
    protected array $cumulativeSum = [];

    /**
     * regression line points.
     *
     * @var Point[]
     */
    protected array $xy = [];

    public function __construct(array $xCoords, array $yCoords)
    {
        $this->appendData($xCoords, $yCoords);
    }

    /**
     * The amount of increase in y (vertical) for an increase of 1 on the x axis (horizontal).
     */
    public function getSlope(): float
    {
        return $this->slope;
    }

    /**
     * The value at which the regression line crosses the y axis (vertical).
     */
    public function getIntercept(): float
    {
        return $this->intercept;
    }

    /**
     * The "coefficient of determination" or "r-squared value"
     * always a number between 0 and 1
     * 1, all of the data points fall perfectly on the regression line. The predictor x accounts for all of the
     * variation in y
     * 0, the estimated regression line is perfectly horizontal. The predictor x accounts for none of the variation in
     * y.
     */
    public function getRSquared(): float
    {
        return $this->rSquared;
    }

    /**
     * Predict for a given y value (sample) the x value (target).
     */
    public function predictX(float $y): float
    {
        return bcdiv(bcsub($y, $this->getIntercept()), $this->getSlope());
    }

    /**
     * Predict for a given x value (target) the y value (sample).
     */
    public function predictY(float $x): float
    {
        return bcadd($this->getIntercept(), bcmul($x, $this->getSlope()));
    }

    /**
     * Get the differences of the actual data from the regression line
     * This is the differences in y values.
     *
     * @return float[]
     */
    public function getDifferencesFromRegressionLine(): array
    {
        if (0 === count($this->yDifferences)) {
            for ($i = 0; $i < $this->coordinateCount; $i++) {
                $this->yDifferences[] = bcsub($this->yCoords[$i], $this->predictY($this->xCoords[$i]));
            }
        }

        return $this->yDifferences;
    }

    /**
     * Get the cumulative some of the differences from the regression line.
     *
     * @return float[]
     */
    public function getCumulativeSumOfDifferencesFromRegressionLine(): array
    {
        if (0 === count($this->cumulativeSum)) {
            $differences = $this->getDifferencesFromRegressionLine();
            $this->cumulativeSum = [$differences[0]];
            for ($i = 1; $i < $this->coordinateCount; $i++) {
                $this->cumulativeSum[$i] = bcadd($differences[$i], $this->cumulativeSum[$i - 1]);
            }
        }

        return $this->cumulativeSum;
    }

    /**
     * Mean of Y values.
     */
    public function getMeanY(): float|int
    {
        return bcdiv($this->ySum, $this->coordinateCount);
    }

    /**
     * Return an array of Points corresponding to the regression line of the current data.
     *
     * @return Point[]
     */
    public function getRegressionLinePoints(): array
    {
        if (0 == count($this->xy)) {
            $minX = min($this->xCoords);
            $maxX = max($this->xCoords);
            $xStepSize = bcdiv(bcsub($maxX, $minX), $this->coordinateCount - 1);
            $this->xy = [];
            for ($i = 0; $i < $this->coordinateCount; $i++) {
                $x = bcadd($minX, bcmul($i, $xStepSize));
                $y = $this->predictY($x);
                $this->xy[] = new Point($x, $y); // add point
            }
        }

        return $this->xy;
    }

    protected function appendData(array $xCoords, array $yCoords): void
    {
        $this->xCoords = array_merge($this->xCoords, $xCoords);
        $this->yCoords = array_merge($this->yCoords, $yCoords);
        $this->countCoordinates();
        $this->compute();
    }

    /**
     * @throws SeriesCountMismatch
     * @throws SeriesHasZeroElements
     */
    protected function countCoordinates(): int
    {
        // calculate number points
        $this->coordinateCount = count($this->xCoords);
        $yCount = count($this->yCoords);

        // ensure both arrays of points are the same size
        if ($this->coordinateCount !== $yCount) {
            throw new SeriesCountMismatch("Number of elements in arrays do not match {$this->coordinateCount}:{$yCount}");
        }

        if ($this->coordinateCount === 0) {
            throw new SeriesHasZeroElements('Series has zero elements');
        }

        return $this->coordinateCount;
    }

    /**
     * Linear model that uses least squares method to approximate solution.
     */
    protected function compute(): void
    {
        $this->xSum = 0;
        $this->ySum = 0;
        $xxSum = 0;
        $xySum = 0;
        $yySum = 0;

        for ($i = 0; $i < $this->coordinateCount; $i++) {
            $xi = number_format($this->xCoords[$i], bcscale(), '.', '');
            $yi = number_format($this->yCoords[$i], bcscale(), '.', '');

            $this->xSum = bcadd($this->xSum, $xi);
            $this->ySum = bcadd($this->ySum, $yi);
            $xxSum = bcadd($xxSum, bcmul($xi, $xi));
            $yySum = bcadd($yySum, bcmul($yi, $yi));
            $xySum = bcadd($xySum, bcmul($xi, $yi));
        }

        // calculate slope

        $slopeNumerator = bcsub(bcmul($this->coordinateCount, $xySum), bcmul($this->xSum, $this->ySum));
        $slopeDenominator = bcsub(bcmul($this->coordinateCount, $xxSum), bcmul($this->xSum, $this->xSum));
        $this->slope = bccomp($slopeDenominator, 0) === 0 ? 0 : bcdiv($slopeNumerator, $slopeDenominator);

        // calculate intercept
        $this->intercept = bcdiv(bcsub($this->ySum, bcmul($this->slope, $this->xSum)), $this->coordinateCount);

        // Calculate R squared
        // Math.pow((n*sum_xy - sum_x*sum_y)/Math.sqrt((n*sum_xx-sum_x*sum_x)*(n*sum_yy-sum_y*sum_y)),2);
        $rNumerator = bcsub(bcmul($this->coordinateCount, $xySum), bcmul($this->xSum, $this->ySum));
        $rDenominator = bcsqrt(bcmul(bcsub(bcmul($this->coordinateCount, $xxSum), bcpow($this->xSum, 2)), bcsub(bcmul($this->coordinateCount, $yySum), bcpow($this->ySum, 2))));

        $this->rSquared = bcpow(bccomp($rDenominator, 0) === 0 ? 0 : abs(bcdiv($rNumerator, $rDenominator)), 2);
    }
}
