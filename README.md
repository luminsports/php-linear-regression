# Least Squares Linear Regression class

[![Code Style](https://github.com/luminsports/php-linear-regression/actions/workflows/php-cs-fixer.yml/badge.svg?branch=main)](https://github.com/luminsports/php-linear-regression/actions/workflows/php-cs-fixer.yml)
[![Tests](https://github.com/luminsports/php-linear-regression/actions/workflows/run-tests.yml/badge.svg?branch=main)](https://github.com/luminsports/php-linear-regression/actions/workflows/run-tests.yml)

A Linear regression class that uses the least squares method to approximate a straight line to a data set.

```composer install luminsports/linear-regression```

Usage:
```php
$x = [...]; // target values
$y = [...]; // observation values

$linearRegression = new \LuminSports\LinearRegression\LeastSquares($x, $y);

$slope = $linearRegression->getSlope();
$yIntercept = $linearRegression->getIntercept();
    
// return array of differences of y values from the regression line
$differences = $linearRegression->getDifferencesFromRegressionLine();

// return array of cumulative sum of the differences of y values from the regression line
$cumulativeSum = $linearRegression->getCumulativeSumOfDifferencesFromRegressionLine();

// return array of Point objects giving the x,y values of the regression line
// for current data
$regressionLine = $linearRegression->getRegressionLinePoints();

$regressionLine[0]->getX();
$regressionLine[0]->getY();

$predictedX = $linearRegression->predictX($anObservationValue);

$predictedY = $linearRegression->predictY($aTargetValue);

$rSquared = $linearRegression->getRSquared(); // Regression fit; 1 = perfect fit 0 = no fit
```
