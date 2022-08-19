<?php

namespace LuminSports\LinearRegression\Test;

use LuminSports\LinearRegression\LeastSquares;
use LuminSports\LinearRegression\Point;
use LuminSports\LinearRegression\SeriesCountMismatch;
use PHPUnit\Framework\TestCase;

class LeastSquaresTest extends TestCase
{
    public function seriesDataProvider(): array
    {
        return [
            [
                [0, 0.5, 1.3, 1.9, 0.5, 0.4, 0.1, 0, 0.2, 0.2, 0, 0, 0, 0, 1.2, 0.8, 0, 0.5], // x targets
                [
                    201868.1605,
                    475056.2663,
                    468251.4275,
                    467885.0131,
                    373297.7536,
                    387378.5355,
                    476129.337,
                    503034.6228,
                    467649.461,
                    499841.583,
                    479034.4797,
                    426009.0819,
                    409965.3658,
                    520701.0312,
                    486729.1821,
                    531955.1877,
                    530280.1505,
                    505206.9367,
                ], // y samples
                [
                    -244980.1864,
                    17220.8581,
                    -7163.278864,
                    -20714.16688,
                    -84537.6546,
                    -68259.46043,
                    27083.57788,
                    56186.27595,
                    16406.28961,
                    48598.41161,
                    32186.13285,
                    -20839.26495,
                    -36882.98105,
                    73852.68435,
                    13511.88801,
                    67527.54269,
                    83431.80365,
                    47371.5285,
                ], // diffs from regression line
                [
                    -244980.1864,
                    -227759.3283,
                    -234922.6071,
                    -255636.774,
                    -340174.4286,
                    -408433.889,
                    -381350.3112,
                    -325164.0352,
                    -308757.7456,
                    -260159.334,
                    -227973.2012,
                    -248812.4661,
                    -285695.4472,
                    -211842.7628,
                    -198330.8748,
                    -130803.3321,
                    -47371.5285,
                    5.82E-10,
                ], // Cumulative Sum of diffs
                21974.1227,  // slope
                446848.3469,  // intercept
                0.0240439, // R Squared
                456126.309772, // Mean Y
                [
                    [0, 446848.3468],
                    [0.1117, 449304.2782],
                    [0.2235, 451760.2095],
                    [0.3352, 454216.1409],
                    [0.4470, 456672.0722],
                    [0.5588, 459128.0036],
                    [0.6705, 461583.9350],
                    [0.7823, 464039.8663],
                    [0.8941, 466495.7977],
                    [1.0058, 468951.7290],
                    [1.1176, 471407.6604],
                    [1.2294, 473863.5918],
                    [1.3411, 476319.5231],
                    [1.4529, 478775.4545],
                    [1.5647, 481231.3859],
                    [1.6764, 483687.3172],
                    [1.7882, 486143.2486],
                    [1.9, 488599.1799],
                ], // Regression Line Points
                [0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0], // prediction x-values
                [449045.759124, 451243.171394, 453440.583664, 455637.995934, 457835.408204, 460032.820474, 462230.232744, 464427.645014, 466625.057284, 468822.469554], // prediction y-values
            ],
        ];
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_slope($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints)
    {
        $regression = new LeastSquares($x, $y);

        $this->assertEqualsWithDelta(
            $slope,
            $regression->getSlope(),
            0.0001,
            'Slope doesn\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_intercept($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints)
    {
        $regression = new LeastSquares($x, $y);

        $this->assertEqualsWithDelta(
            $intercept,
            $regression->getIntercept(),
            0.0001,
            'Intercept doesn\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_r_squared($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints)
    {
        $regression = new LeastSquares($x, $y);

        $this->assertEqualsWithDelta(
            $rSquared,
            $regression->getRSquared(),
            0.0001,
            'R Squared doesn\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_differences($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints)
    {
        $regression = new LeastSquares($x, $y);

        $this->assertEqualsWithDelta(
            $diffs,
            $regression->getDifferencesFromRegressionLine(),
            0.0001,
            'Differences don\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_cumulative_sum_of_differences($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints)
    {
        $regression = new LeastSquares($x, $y);

        $this->assertEqualsWithDelta(
            $cumSumDiffs,
            $regression->getCumulativeSumOfDifferencesFromRegressionLine(),
            0.0001,
            'Cumulative sum of differences don\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_mean_y($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints)
    {
        $regression = new LeastSquares($x, $y);

        $this->assertEqualsWithDelta(
            $meanY,
            $regression->getMeanY(),
            0.0001,
            'MeanY doesn\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_regression_line_points($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints)
    {
        $regression = new LeastSquares($x, $y);

        $this->assertEqualsWithDelta(
            $regressionLinePoints,
            array_map(fn (Point $p) => [$p->getX(), $p->getY()], $regression->getRegressionLinePoints()),
            0.0001,
            'Regression line points don\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_predicted_x_values($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints, $predictionX, $predictionY)
    {
        $regression = new LeastSquares($x, $y);

        $predictedXValues = [];

        foreach ($predictionY as $yValue) {
            $predictedXValues[] = $regression->predictX($yValue);
        }

        $this->assertEqualsWithDelta(
            $predictionX,
            $predictedXValues,
            0.0001,
            'X-predicted values don\'t match'
        );
    }

    /**
     * @dataProvider seriesDataProvider
     */
    public function test_predicted_y_values($x, $y, $diffs, $cumSumDiffs, $slope, $intercept, $rSquared, $meanY, $regressionLinePoints, $predictionX, $predictionY)
    {
        $regression = new LeastSquares($x, $y);

        $predictedYValues = [];

        foreach ($predictionX as $xValue) {
            $predictedYValues[] = $regression->predictY($xValue);
        }

        $this->assertEqualsWithDelta(
            $predictionY,
            $predictedYValues,
            0.0001,
            'Y-predicted values don\'t match'
        );
    }

    public function test_it_can_calculate_against_an_empty_set_of_data()
    {
        $regression = new LeastSquares([], []);

        $this->assertEquals(0, $regression->getSlope());
        $this->assertEquals(0, $regression->getIntercept());
        $this->assertEquals(0, $regression->getMeanY());
        $this->assertEquals(0, $regression->getRSquared());
        $this->assertEquals(0, $regression->predictX(10));
        $this->assertEquals(0, $regression->predictY(10));
        $this->assertEquals([], $regression->getRegressionLinePoints());
        $this->assertEquals([], $regression->getDifferencesFromRegressionLine());
        $this->assertEquals([], $regression->getCumulativeSumOfDifferencesFromRegressionLine());
    }

    public function test_it_throws_an_exception_if_coordinates_counts_dont_match()
    {
        $this->expectException(SeriesCountMismatch::class);

        new LeastSquares([1, 2, 3], [1, 2, 3, 4]);
    }
}
