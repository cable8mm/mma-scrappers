# MMA Scrapers 🥊

A lightweight, extensible PHP library for scraping MMA data from multiple sources.

> Built for reliability, testability, and clean architecture.

[![build & tests](https://github.com/cable8mm/mma-scrapers/actions/workflows/run-tests.yml/badge.svg)](https://github.com/cable8mm/mma-scrapers/actions/workflows/run-tests.yml)
[![coding style](https://github.com/cable8mm/mma-scrapers/actions/workflows/code-style.yml/badge.svg)](https://github.com/cable8mm/mma-scrapers/actions/workflows/code-style.yml)
[![minimum PHP version](https://img.shields.io/badge/php-%5E8.4-8892BF?logo=php)](https://github.com/cable8mm/mma-scrapers)

## 🚀 Features

- 🔌 Multi-source scraping (BlackCombat, Sherdog, Tapology*)
- 🧱 Clean architecture (Scraper → Parser → DTO)
- 🧪 Fully testable with HTML fixtures
- 🧩 Extensible source-based design
- ⚡ No database dependency (pure library)

## 📦 Installation

```bash
composer require cable8mm/mma-scrapers
```

## 🧠 Philosophy

This library is designed to be:

- **Dumb but reliable**
- Source-independent
- Free of business logic

```text
Scraper → Parser → DTO
```

❗ This library does NOT:

- Deduplicate fights
- Merge fighters
- Store data

👉 Those responsibilities belong to the main MMA application.

## 🏗 Architecture

```text
Sources/
 ├ BlackCombat/
 │   ├ Scrapers/
 │   ├ Parsers/
 │
 ├ Sherdog/
 │   ├ Scrapers/
 │   ├ Parsers/
```

## ✨ Usage

### 1. Scrape Events

```php
use Cable8mm\MmaScrapers\Sources\BlackCombat\Scrapers\EventsScraper;

$scraper = new EventsScraper($httpClient);
$events = $scraper->scrape();
```

### 2. Parse Fights

```php
use Cable8mm\MmaScrapers\Sources\BlackCombat\Parsers\ParseFights;

$parser = new ParseFights();

$fights = $parser->parse($html);
```

### 3. Fighter Data

```php
use Cable8mm\MmaScrapers\Sources\Sherdog\Parsers\ParseFighter;

$parser = new ParseFighter();

$fighter = $parser->parse($html);
```

## 🧱 DTO Example

```php
new FightDTO(
    redFighter: FighterDTO,
    blueFighter: FighterDTO,
    status: FightStatus::FINISHED,
    method: FightMethod::KO,
    round: 1,
    time: '3:14',
    winner: WinnerCorner::RED,
    source: Source::SHERDOG
);
```

## 🧪 Testing

All parsers must be tested using fixtures.

```bash
composer test
```

Example:

```php
$html = file_get_contents('tests/Fixtures/sherdog_fighter.html');

$parser = new ParseFighter();

$fighter = $parser->parse($html);

$this->assertEquals('Dalton Rosta', $fighter->name);
```

## 📌 Supported Sources

| Source      | Status         |
| ----------- | -------------- |
| BlackCombat | ✅ Implemented |
| Sherdog     | 🚧 In Progress |
| Tapology    | 📝 Planned     |

## 🛑 Rules

### Scrapers

- Only fetch HTML
- Must use HttpClientInterface

### Parsers

- HTML → DTO
- No HTTP logic
- Deterministic output

### DTOs

- Immutable
- No logic

## ❌ What NOT to do

- Do not add database logic
- Do not merge fighters across sources
- Do not deduplicate fights
- Do not assume any source is “truth”

## 🔗 Related Project

👉 MMA Platform (Data aggregation & API)

- <https://github.com/cable8mm/mma>

## 🤝 Contributing

Contributions are welcome!

1. Fork the repo
2. Create a feature branch
3. Add tests with fixtures
4. Submit PR

## 📄 License

MIT License
