# ddd-workshops

## Project Overview

A PHP workshop project demonstrating Domain-Driven Design (DDD) and CQRS patterns. The project implements a simple User management domain with registration, activation, enable/disable, password change, and unregistration use cases.

## Architecture

- **DDD** (Domain-Driven Design) with a layered architecture
- **CQRS** (Command Query Responsibility Segregation)
- **Event-driven** patterns (see `event-driven` branch)
- **Event Sourcing** (see `event-sourcing` branch)

## Directory Structure

```
src/
├── Application/User/         # Application layer (UserService, Commands, Event Handlers)
│   ├── Command/              # Command objects (RegisterUserCommand, etc.)
│   └── Event/                # Domain event handlers
├── DomainModel/User/         # Domain model (User entity, Value Objects, Events, Repository interface)
│   ├── Event/                # Domain events
│   ├── Exception/            # Domain exceptions
│   └── Password/             # UserPassword value object
├── Infrastructure/           # Infrastructure layer (InMemory implementations)
│   └── User/                 # InMemoryUserRepository, InMemoryUserReadModelRepository
├── ReadModel/User/           # Read model (UserDTO, UserReadModelRepository interface)
└── SharedKernel/User/        # Shared kernel (UserId, interfaces, base exceptions)

tests/
├── Unit/                     # Unit tests (mock dependencies)
│   ├── Application/User/     # UserService unit tests
│   ├── DomainModel/User/     # Domain model unit tests
│   └── Infrastructure/User/ # Repository unit tests
└── Integration/              # Integration tests (real in-memory storage)
    └── Application/User/     # UserService integration tests
```

## Environment

- **PHP**: ^8.4
- **PHPUnit**: ^11.0
- **No external runtime dependencies** (uses in-memory storage for all tests)

## Running Tests

```bash
# Install dependencies
composer install

# Run all tests
vendor/bin/phpunit

# Run only unit tests
vendor/bin/phpunit --testsuite unit

# Run only integration tests
vendor/bin/phpunit --testsuite integration
```

## Coding Conventions

- `declare(strict_types=1)` in every file
- PSR-4 autoloading under `TSwiackiewicz\AwesomeApp` namespace
- Constructor property promotion with `readonly` for immutable Value Objects and Commands
- PHP 8 attributes for PHPUnit: `#[Test]`, `#[DataProvider]`, `#[CoversClass]`
- Data provider methods must be `static`
- No `@var`, `@param`, `@return` PHPDoc — use native PHP type declarations instead

## Branches

| Branch | Description |
|--------|-------------|
| `master` | Basic DDD/CQRS implementation |
| `event-driven` | Event-driven variant with domain events |
| `event-sourcing` | Event sourcing variant |
