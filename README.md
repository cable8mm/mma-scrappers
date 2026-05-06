# MMA Scrapers

A lightweight PHP library for scraping and parsing MMA data into simple DTOs.

[![build & tests](https://github.com/cable8mm/mma-scrapers/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/mma-scrapers/actions/workflows/run-tests.yml)
[![coding style](https://github.com/cable8mm/mma-scrapers/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/mma-scrapers/actions/workflows/code-style.yml)
[![deploy-to-github-pages](https://github.com/cable8mm/mma-scrapers/actions/workflows/deploy-to-github-pages.yml/badge.svg)](https://github.com/cable8mm/mma-scrapers/actions/workflows/deploy-to-github-pages.yml)
[![update changelog](https://github.com/cable8mm/mma-scrapers/actions/workflows/update-changelog.yml/badge.svg)](https://github.com/cable8mm/mma-scrapers/actions/workflows/update-changelog.yml)
![Packagist Dependency Version](https://img.shields.io/packagist/dependency-v/cable8mm/mma-scrapers/php)
![Packagist Version](https://img.shields.io/packagist/v/cable8mm/mma-scrapers)
![Packagist Downloads](https://img.shields.io/packagist/dt/cable8mm/mma-scrapers)
![Packagist Stars](https://img.shields.io/packagist/stars/cable8mm/mma-scrapers)
![GitHub License](https://img.shields.io/github/license/cable8mm/mma-scrapers)

## Features

- Source-specific scrapers and parsers for MMA websites.
- Normalized DTOs for events, fights, and fighters.
- Fixture-friendly parser design using Symfony DomCrawler.
- Mockable HTTP layer through `HttpClientInterface`.
- Helper services for fighter matching, Sherdog ID resolution, and fight deduplication.
- No database dependency.

## Requirements

- PHP `^8.4`
- Composer

## Installation

```bash
composer require cable8mm/mma-scrapers
```

For local development:

```bash
composer install
```

## Supported Sources

| Source | Events | Event detail | Fights | Fighters | Notes |
| --- | --- | --- | --- | --- | --- |
| BlackCombat | Yes | Yes | Yes | Yes | Official source support |
| Sherdog | No | No | No | Yes | Fighter search and fighter detail support |
| Tapology | No | No | No | No | Planned source |

## Core Concepts

The library is organized around a small pipeline:

```text
HTTP client -> Scraper -> Parser -> DTO
```

Scrapers fetch HTML and delegate extraction to parsers. Parsers are deterministic and return DTOs. Aggregators and services are available when a consuming app needs to compare, merge, or deduplicate parsed results.

## Project Structure

```text
src/
  Aggregators/      Merge related event, fight, and fighter DTOs
  Contracts/        Scraper and HTTP interfaces
  DTO/              EventDTO, FightDTO, FighterDTO
  Enums/            Source, fight status, fight method, weight class
  Http/             Guzzle HTTP client implementation
  Matchers/         Fighter matching helpers
  Normalizers/      Text-to-enum normalization helpers
  Services/         Sherdog ID resolution and fight deduplication
  Sources/
    BlackCombat/
      Parsers/
      Scrapers/
    Sherdog/
      Parsers/
      Scrapers/
```

## Usage

### Parse BlackCombat Events From HTML

```php
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvents;

$html = file_get_contents('blackcombat_events.html');

$parser = new ParseEvents();
$events = $parser($html);
```

### Scrape BlackCombat Events

```php
use Cable8mm\MmaScrapers\Http\DefaultHttpClient;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseEvents;
use Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers\EventsScraper;

$scraper = new EventsScraper(
    new DefaultHttpClient(),
    new ParseEvents()
);

$events = $scraper->scrape('https://www.blackcombat-official.com/event.php?page=10');
```

### Parse BlackCombat Fights

```php
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFights;

$html = file_get_contents('event_detail.html');

$parser = new ParseFights();
$fights = $parser($html);
```

### Scrape a Sherdog Fighter

```php
use Cable8mm\MmaScrapers\Http\DefaultHttpClient;
use Cable8mm\MmaScrapers\Sources\Sherdog\Parsers\ParseFighter;
use Cable8mm\MmaScrapers\Sources\Sherdog\Scrapers\FighterScraper;

$scraper = new FighterScraper(
    new DefaultHttpClient(),
    new ParseFighter()
);

$fighter = $scraper->scrapeById(12345);
```

### Resolve a Sherdog Fighter ID

```php
use Cable8mm\MmaScrapers\Http\DefaultHttpClient;
use Cable8mm\MmaScrapers\Services\SherdogIdResolver;
use Cable8mm\MmaScrapers\Sources\Sherdog\Parsers\ParseSearchResults;
use Cable8mm\MmaScrapers\Sources\Sherdog\Scrapers\SearchFighterScraper;

$search = new SearchFighterScraper(new DefaultHttpClient());
$parser = new ParseSearchResults();
$resolver = new SherdogIdResolver();

$html = $search->search('Chan Sung Jung');
$candidates = $parser($html);

$sherdogId = $resolver->resolve('Chan Sung Jung', $candidates);
```

### Deduplicate Fights

```php
use Cable8mm\MmaScrapers\Aggregators\FightAggregator;
use Cable8mm\MmaScrapers\Aggregators\FighterAggregator;
use Cable8mm\MmaScrapers\Services\FightDeduplicator;

$deduplicator = new FightDeduplicator(
    new FightAggregator(new FighterAggregator())
);

$deduplicatedFights = $deduplicator->deduplicate($fights);
```

## DTOs

### `EventDTO`

```php
new EventDTO(
    name: 'Black Combat 16',
    location: 'Incheon, South Korea',
    date: new DateTimeImmutable('2026-01-31'),
    url: '/eventDetail.php?eventSeq=285',
    externalId: '285'
);
```

### `FighterDTO`

```php
new FighterDTO(
    name: 'Chan Sung Jung',
    nickname: 'The Korean Zombie',
    instagram: 'koreanzombiemma',
    teamname: 'Korean Zombie MMA',
    height: '170cm',
    win: 17,
    lose: 8,
    draw: 0,
    sherdogId: 36155
);
```

### `FightDTO`

```php
use Cable8mm\MmaScrapers\Enums\FightMethod;
use Cable8mm\MmaScrapers\Enums\FightStatus;
use Cable8mm\MmaScrapers\Enums\Source;
use Cable8mm\MmaScrapers\Enums\WeightClass;

new FightDTO(
    redFighter: $redFighter,
    blueFighter: $blueFighter,
    source: Source::OFFICIAL,
    status: FightStatus::FINISHED,
    weightClass: WeightClass::FEATHERWEIGHT,
    method: FightMethod::KO,
    round: 1,
    time: '3:14',
    winner: $redFighter,
    fightDate: new DateTimeImmutable('2026-01-31')
);
```

## Design Rules

- Keep source implementations isolated under `src/Sources/{SourceName}`.
- Put HTTP access in scrapers, not parsers.
- Keep parsers deterministic: raw HTML in, DTOs out.
- Test parsers with static HTML fixtures.
- Keep storage, API delivery, and application workflows outside this package.

## Development

Run tests:

```bash
composer test
```

Run Pint:

```bash
composer lint
```

Generate API documentation:

```bash
composer apidoc
```

## Testing

Parser and scraper tests use HTML fixtures from `tests/Fixtures`.

```php
$html = file_get_contents(__DIR__.'/../../Fixtures/BlackCombat/event_detail.html');

$parser = new ParseFights();
$fights = $parser($html);

$this->assertNotEmpty($fights);
```

Avoid real HTTP calls in tests. Inject a mocked `HttpClientInterface` when testing scrapers.

## Contributing

1. Keep the existing source/parser/scraper boundaries.
2. Add or update fixtures for parser changes.
3. Add unit tests for new behavior.
4. Run `composer test` and `composer lint` before opening a pull request.

## License

MMA Scrapers is open-sourced software licensed under the [MIT license](LICENSE).
