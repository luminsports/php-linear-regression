# AGENTS.md

Repository-specific guidance for coding agents working in `php-linear-regression`.

## Project Snapshot

- Pure PHP 8.1+ library implementing least-squares linear regression
- Runtime arithmetic uses `brick/math` and requires BCMath
- Source: `src/`; PHPUnit tests: `tests/`
- Quality tools: PHPStan, PHP CS Fixer, and PHPUnit
- No Laravel, framework service provider, database, or application runtime

## Source of Truth

- Supported dependencies and commands: `composer.json`
- Regression calculations and predictions: `src/LeastSquares.php`
- Regression points: `src/Point.php`
- Input length exception: `src/SeriesCountMismatch.php`
- Test configuration: `phpunit.xml`
- Static analysis: `phpstan.neon.dist`
- Formatting: `.php-cs-fixer.dist.php`
- CI: `.github/workflows/`

## Commands

Run from the repository root:

```bash
composer install
composer test
composer phpstan
composer check-style
```

Use `composer coverage` for an HTML coverage report and `composer fix-style` only when intentionally applying formatting. Run one test file with `./vendor/bin/phpunit tests/LeastSquaresTest.php` or a named test with `./vendor/bin/phpunit --filter test_slope`.

## Fast Test Runs

- Pest 5's test impact analysis runs only the tests your changes affect: `./vendor/bin/pest --parallel --tia`. The
  first run records a dependency graph from code coverage and takes about as long as a full parallel run. Later runs
  re-run the affected tests and replay cached results for the rest.
- TIA needs PCOV or Xdebug, and every test must be a Pest test: one PHPUnit test class stops the run with "Tia mode
  requires Pest tests". Convert class-based tests first. A `defineEnvironment()` override can move into a trait that
  the test file applies with `uses()`.
- The graph lives in `~/.pest/tia/<project-key>/` (`./vendor/bin/pest --baseline` prints the path) and is shared across
  worktrees. It re-records itself when `composer.lock` or `phpunit.xml` changes; `--tia --fresh` re-records it by hand.
  Edits to comments, docblocks, or formatting re-run nothing.
- `--parallel` alone also cuts a full run several-fold.
- TIA is for local iteration. Do not add `--tia` to CI, and do not count a replayed run as a full-suite run where this
  file asks for one.
- This repository is on Pest 4, which has no TIA. Upgrade to Pest 5 before using `--tia`.

## Architecture and Numerical Rules

- `LeastSquares` validates equal-length coordinate series, calculates slope, intercept, coefficient of determination, predictions, residuals, and regression-line points.
- Preserve arbitrary-precision intermediate arithmetic through `BigDecimal`; do not replace calculations with native floating-point arithmetic without accuracy evidence.
- Be explicit about precision and rounding. Changing either can alter public numerical results and requires focused regression tests.
- Keep X as the target/independent coordinate and Y as the observation/dependent coordinate throughout the API.
- Preserve lazy caches for residuals, cumulative residuals, and line points, or invalidate them correctly if mutable inputs are introduced.
- Do not add framework dependencies or application-specific behavior.

## Code Quality

- Follow `.php-cs-fixer.dist.php` and avoid unrelated formatting.
- Add parameter, property, and return types where compatibility permits.
- Use PHPDoc array shapes or element types when PHP cannot express them.
- Do not weaken PHPStan or add broad suppressions to hide new errors.
- Throw meaningful exceptions for invalid inputs; do not silently coerce mismatched series.
- Avoid logging or side effects in this calculation library.

## Testing Standards

- Use PHPUnit and place tests under `tests/` with `Test.php` suffixes.
- Encode the numerical contract and why precision matters, using explicit tolerances for floating-point outputs.
- Cover empty data, mismatched series, horizontal lines, predictions, residuals, cumulative residuals, and point serialization when touched.
- Add edge cases for zero denominators and precision changes when modifying calculations.
- Prefer small deterministic datasets. Do not make assertions so loose that incorrect regression output passes.
- Run the full suite for calculation changes; it is small and fast.

## Handoff Checklist

- Run tests, PHPStan, and the style check.
- Report test counts and any coverage command that was skipped.
- Confirm `src/` and `tests/` changes are intentional.
- Do not commit generated coverage, caches, Composer dependencies, IDE settings, or agent state.
- Report pre-existing failures and dirty files separately.
