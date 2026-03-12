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
├── Application/User/         # Application layer (Command Handlers, Event Handlers, Queries)
│   ├── Command/              # Command objects (RegisterUserCommand, etc.)
│   ├── Handler/              # Command and Event handlers (RegisterUserHandler, etc.)
│   └── Query/                # Read model (UserDTO, UserReadModel, UserReadModelRepository)
├── Domain/User/              # Domain model
│   ├── Entity/               # User aggregate root
│   ├── Event/                # Domain events
│   ├── Exception/            # Domain exceptions
│   ├── Repository/           # Repository and Notifier interfaces
│   ├── Service/              # UserPasswordService (domain service)
│   └── ValueObject/          # UserId, UserLogin, UserPassword, UserStatus (enum)
├── Infrastructure/           # Infrastructure layer
│   ├── Notification/         # StdOutUserNotifier
│   └── Persistence/          # InMemoryStorage, InMemoryUserRepository, InMemoryUserReadModelRepository
└── SharedKernel/             # Shared kernel
    └── Exception/            # Base exceptions (InvalidArgumentException, RuntimeException, etc.)

tests/
├── Application/User/         # Application layer tests
│   ├── Handler/              # Command/Event handler tests
│   └── Query/                # Read model tests
├── Domain/User/              # Domain model tests
│   ├── Entity/               # User entity tests
│   ├── Service/              # Domain service tests
│   └── ValueObject/          # Value object tests
└── Infrastructure/User/      # Infrastructure and integration tests
```

## Environment

- **PHP**: ^8.5
- **PHPUnit**: ^11.0
- **No external runtime dependencies** (uses in-memory storage for all tests)

## Running Tests

```bash
# Install dependencies
composer install

# Run all tests
vendor/bin/phpunit

# Run only domain tests
vendor/bin/phpunit --testsuite domain

# Run only application tests
vendor/bin/phpunit --testsuite application

# Run only infrastructure tests
vendor/bin/phpunit --testsuite infrastructure
```

## Coding Conventions

- `declare(strict_types=1)` in every file
- PSR-4 autoloading under `TSwiackiewicz\AwesomeApp` namespace
- Constructor property promotion with `readonly` for immutable Value Objects and Commands
- PHP 8 attributes for PHPUnit: `#[Test]`, `#[DataProvider]`, `#[CoversClass]`
- Data provider methods must be `static`
- No `@var`, `@param`, `@return` PHPDoc — use native PHP type declarations instead
- PHP 8.5 features: `abstract readonly class` for events, `final readonly class` for value objects, enums for domain status
- Command handlers use `__invoke()` pattern instead of service methods

## Branches

| Branch | Description |
|--------|-------------|
| `master` | Basic DDD/CQRS implementation |
| `event-driven` | Event-driven variant with domain events |
| `event-sourcing` | Event sourcing variant |
