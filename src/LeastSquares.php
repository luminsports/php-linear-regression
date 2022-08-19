<?php

namespace LuminSports\LinearRegression;

use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;

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
        if ($this->getSlope() === 0.0) {
            return 0;
        }

        return BigDecimal::of($y)->minus($this->getIntercept())->dividedBy($this->getSlope(), 16, RoundingMode::HALF_UP)->toFloat();
    }

    /**
     * Predict for a given x value (target) the y value (sample).
     */
    public function predictY(float $x): float
    {
        if ($this->getSlope() === 0.0) {
            return $this->getIntercept();
        }

        return BigDecimal::of($this->getIntercept())->plus(BigDecimal::of($x)->multipliedBy($this->getSlope()))->toFloat();
    }

    /**
     * Get the differences of the actual data from the regression line
     * This is the differences in y values.
     *
     * @return float[]
     */
    public function getDifferencesFromRegressionLine(): array
    {
        if ($this->coordinateCount === 0) {
            return [];
        }

        if (count($this->yDifferences) === 0) {
            for ($i = 0; $i < $this->coordinateCount; $i++) {
                $this->yDifferences[] = BigDecimal::of($this->yCoords[$i])->minus($this->predictY($this->xCoords[$i]))->toFloat();
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
        if ($this->coordinateCount === 0) {
            return [];
        }

        if (count($this->cumulativeSum) === 0) {
            $differences = $this->getDifferencesFromRegressionLine();
            $this->cumulativeSum = [$differences[0]];
            for ($i = 1; $i < $this->coordinateCount; $i++) {
                $this->cumulativeSum[$i] = BigDecimal::of($differences[$i])->plus($this->cumulativeSum[$i - 1])->toFloat();
            }
        }

        return $this->cumulativeSum;
    }

    /**
     * Mean of Y values.
     */
    public function getMeanY(): float|int
    {
        if ($this->ySum === 0 || $this->coordinateCount === 0) {
            return 0;
        }

        return BigDecimal::of($this->ySum)->dividedBy($this->coordinateCount, 16, RoundingMode::HALF_UP)->toFloat();
    }

    /**
     * Return an array of Points corresponding to the regression line of the current data.
     *
     * @return Point[]
     */
    public function getRegressionLinePoints(): array
    {
        if ($this->coordinateCount === 0) {
            return [];
        }

        if (count($this->xy) === 0) {
            $minX = BigDecimal::of(min($this->xCoords));
            $maxX = BigDecimal::of(max($this->xCoords));
            $xStepSize = $maxX->minus($minX)->dividedBy($this->coordinateCount - 1, 16, RoundingMode::HALF_UP);
            $this->xy = [];
            for ($i = 0; $i < $this->coordinateCount; $i++) {
                $x = $minX->plus($xStepSize->multipliedBy($i))->toFloat();
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
        $this->compute();
    }

    /**
     * @throws SeriesCountMismatch
     * @throws SeriesHasZeroElements
     */
    protected function countCoordinates(): void
    {
    }

    /**
     * Linear model that uses least squares method to approximate solution.
     */
    protected function compute(): void
    {
        // calculate number points
        $this->coordinateCount = count($this->xCoords);
        $yCount = count($this->yCoords);

        // ensure both arrays of points are the same size
        if ($this->coordinateCount !== $yCount) {
            throw new SeriesCountMismatch("Number of elements in arrays do not match {$this->coordinateCount}:{$yCount}");
        }

        if ($this->coordinateCount === 0) {
            $this->xSum = 0;
            $this->ySum = 0;
            $this->slope = 0;
            $this->intercept = 0;
            $this->rSquared = 0;

            return;
        }

        $xSum = BigDecimal::zero();
        $ySum = BigDecimal::zero();
        $xxSum = BigDecimal::zero();
        $xySum = BigDecimal::zero();
        $yySum = BigDecimal::zero();

        for ($i = 0; $i < $this->coordinateCount; $i++) {
            $xi = BigDecimal::of($this->xCoords[$i]);
            $yi = BigDecimal::of($this->yCoords[$i]);

            $xSum = $xSum->plus($xi);
            $ySum = $ySum->plus($yi);
            $xxSum = $xxSum->plus($xi->multipliedBy($xi));
            $yySum = $yySum->plus($yi->multipliedBy($yi));
            $xySum = $xySum->plus($xi->multipliedBy($yi));
        }

        // calculate slope
        $slopeNumerator = $xySum->multipliedBy($this->coordinateCount)->minus($xSum->multipliedBy($ySum));
        $slopeDenominator = $xxSum->multipliedBy($this->coordinateCount)->minus($xSum->multipliedBy($xSum));
        $slope = $slopeDenominator->isGreaterThan(0) ? $slopeNumerator->dividedBy($slopeDenominator, 16, RoundingMode::HALF_UP) : BigDecimal::zero();

        // calculate intercept
        $intercept = $ySum->minus($slope->multipliedBy($xSum))->dividedBy($this->coordinateCount, 16, RoundingMode::HALF_UP);

        // Calculate R squared
        // Math.pow((n*sum_xy - sum_x*sum_y)/Math.sqrt((n*sum_xx-sum_x*sum_x)*(n*sum_yy-sum_y*sum_y)),2);
        $rNumerator = $slopeNumerator;
        $rDenominator = $xxSum->multipliedBy($this->coordinateCount)
            ->minus($xSum->power(2))
            ->multipliedBy($yySum->multipliedBy($this->coordinateCount)->minus($ySum->power(2)))
            ->sqrt(16);

        $rSquared = ($rDenominator->isGreaterThan(0) ? $rNumerator->dividedBy($rDenominator, 16, RoundingMode::HALF_UP)->abs() : BigDecimal::zero())->power(2);

        $this->xSum = $xSum->toFloat();
        $this->ySum = $ySum->toFloat();
        $this->slope = $slope->toFloat();
        $this->intercept = $intercept->toFloat();
        $this->rSquared = $rSquared->toFloat();
    }
}
