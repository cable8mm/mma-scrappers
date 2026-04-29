# MMA Scrapers Library

## Overview

This project is a reusable PHP library for scraping MMA data from multiple sources.

It is designed to be:

- Source-agnostic
- Testable
- Extensible

---

## Supported Sources

- BlackCombat (implemented)
- Sherdog (in progress)
- Tapology (planned)

---

## Architecture

Each source must be isolated.

### Structure

Sources/
 ├ BlackCombat/
 │   ├ Scrapers/
 │   ├ Parsers/
 │
 ├ Sherdog/
 │   ├ Scrapers/
 │   ├ Parsers/

---

## Core Flow

Scraper → Parser → DTO

No database logic allowed.

---

## DTO Rules

- DTOs must be immutable
- DTOs must not contain logic
- DTOs represent normalized data

---

## Parser Rules

- Parsers convert raw HTML → DTO
- Parsers must be deterministic
- Do not include HTTP logic

---

## Scraper Rules

- Scrapers fetch HTML only
- Use HttpClientInterface
- Must be mockable

---

## Testing Rules

- All parsers must have fixture-based tests
- Use static HTML fixtures
- No real HTTP calls in tests

---

## Important Constraints

- Do not couple sources together
- Do not assume one source is truth
- Do not add business logic

---

## When Generating Code

Always:

- Keep sources isolated
- Follow existing DTO structure
- Write tests with fixtures

Never:

- Add DB logic
- Merge fighters across sources
- Implement aggregation logic
