---
name: database-design
description: >-
  Relational database design, MySQL 8.0 schema optimization, foreign key constraints, migration rollback safety, indexing strategies, and eCommerce data modeling.
---

# Database Design & Schema Architecture Standards

This skill provides guidelines for designing efficient relational schemas in MySQL 8.0 for eCommerce platforms.

## 1. Schema Design Principles
- **Primary Keys**: Always use unsigned 64-bit auto-incrementing integers (`$table->id()`) or UUIDs.
- **Foreign Keys & Integrity**:
  - Always enforce explicit foreign key constraints: `$table->foreignId('category_id')->constrained()->onDelete('cascade')`.
  - Use `nullOnDelete()` when child records should be preserved upon parent deletion.
- **Data Types Selection**:
  - Financials/Prices: Always use `DECIMAL(12, 2)` or `DECIMAL(15, 2)`, NEVER `FLOAT` or `DOUBLE`.
  - Quantities: Use `INTEGER UNSIGNED`.
  - Short text/Names: Use `VARCHAR(255)`.
  - Descriptions/Articles: Use `TEXT` or `LONGTEXT`.
  - Booleans/Flags: Use `BOOLEAN` (TINYINT(1)).

## 2. Indexing Strategy
- Add indexes on columns frequently used in filtering: `$table->index('status')`.
- Add unique indexes on unique constraints: `$table->unique('email')`, `$table->unique('name')`.
- Add composite indexes for multi-column search queries.

## 3. Migration Safety
- Every `up()` method in a migration must have a corresponding, symmetric `down()` method (`Schema::dropIfExists('table_name')`).
- Keep migrations immutable once committed to production/version control. Use new migration files for alter operations.
