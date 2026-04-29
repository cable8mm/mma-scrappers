# AI Context

This project is a scraping library.

## Purpose

Extract MMA data from websites and convert to DTOs.

## Pipeline

HTML → Parser → DTO

## Key Principle

This library does NOT:

- Deduplicate data
- Merge fighters
- Store data

Those responsibilities belong to the main MMA project.

## Responsibilities

✔ Scraping
✔ Parsing
✔ DTO creation

## Non-Responsibilities

❌ Aggregation
❌ Database
❌ Business logic
