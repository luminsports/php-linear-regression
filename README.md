# PHP Linear Regression

[![Code Style](https://github.com/luminsports/least-squares-regression/actions/workflows/php-cs-fixer.yml/badge.svg?branch=main)](https://github.com/luminsports/least-squares-regression/actions/workflows/php-cs-fixer.yml)
[![PHPStan](https://github.com/luminsports/least-squares-regression/actions/workflows/phpstan.yml/badge.svg?branch=main)](https://github.com/luminsports/least-squares-regression/actions/workflows/phpstan.yml)
[![Tests](https://github.com/luminsports/least-squares-regression/actions/workflows/tests.yml/badge.svg?branch=main)](https://github.com/luminsports/least-squares-regression/actions/workflows/tests.yml)

A small PHP library for fitting a straight line to paired data with ordinary least squares. It uses `brick/math` and BCMath for precise intermediate arithmetic and provides predictions, residuals, cumulative residuals, regression-line points, and the coefficient of determination.

Originally forked from [davebarnwell/ml-regression-least-squares](https://github.com/davebarnwell/ml-regression-least-squares).

## Requirements

- PHP 8.1 or later
- BCMath extension

## Installation

```bash
composer require luminsports/linear-regression
```

## Usage

```php
use LuminSports\LinearRegression\LeastSquares;

$targets = [1, 2, 3, 4];
$observations = [2.1, 4.0, 6.2, 7.9];

$regression = new LeastSquares($targets, $observations);

$slope = $regression->getSlope();
$intercept = $regression->getIntercept();
$rSquared = $regression->getRSquared();

$predictedObservation = $regression->predictY(5);
$predictedTarget = $regression->predictX(10);

$residuals = $regression->getDifferencesFromRegressionLine();
$cumulativeResiduals = $regression->getCumulativeSumOfDifferencesFromRegressionLine();
$linePoints = $regression->getRegressionLinePoints();
```

The X series represents target or independent values; the Y series represents observed or dependent values. Both arrays must contain the same number of elements or `SeriesCountMismatch` is thrown.

The optional constructor precision controls division and rounding:

```php
$regression = new LeastSquares($targets, $observations, precision: 12);
$regression->setPrecision(10);
```

## Development

```bash
composer install
composer test
composer phpstan
composer check-style
```

Generate an HTML coverage report with `composer coverage`. Apply formatting intentionally with `composer fix-style`.

## Contributing and Security

Keep changes focused on the numerical library, preserve supported PHP compatibility, and add regression tests for calculation changes. See the [Lumin Sports contributing guide](https://github.com/luminsports/.github/blob/main/CONTRIBUTING.md).

Report security vulnerabilities privately through the repository security policy; do not disclose sensitive details in a public issue.

## License

Released under the MIT License. See [LICENSE.md](LICENSE.md) and [COPYRIGHT](COPYRIGHT).
