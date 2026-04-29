# Copilot Instructions

This is a PHP scraping library.

## Important

- Do NOT add application logic
- Do NOT add database code

## Architecture

Scraper → Parser → DTO

## Rules

- Parsers must be pure
- Scrapers must be mockable
- DTOs must be immutable

## Testing

- Always use fixture HTML
- Avoid network calls

## Sources

Each source (BlackCombat, Sherdog, etc.) must be independent.

## Avoid

- Shared parsing logic
- Business logic
- Aggregation logic
