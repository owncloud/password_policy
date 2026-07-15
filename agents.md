# agents.md -- Password Policy

## Repository Overview

ownCloud Server app for defining and enforcing password policies for user accounts and public link shares. Licensed under GPL-2.0.

## Architecture & Key Paths

- `lib/` -- PHP application logic
- `js/` -- Frontend JavaScript
- `css/` -- Stylesheets
- `templates/` -- Server-side templates
- `appinfo/` -- ownCloud app metadata
- `l10n/` -- Translation files
- `tests/` -- Unit and acceptance tests
- `Makefile` -- Build and test automation
- `composer.json` -- PHP dependencies

## Development Conventions

- PHP code follows ownCloud coding standards (phpcs)
- Static analysis with PHPStan and Phan

## Build & Test Commands

```bash
make dist                     # Build distribution package
make test-php-unit            # Run PHP unit tests
make test-php-style           # Check PHP code style
make test-php-phpstan         # Run PHPStan
make test-php-phan            # Run Phan
make test-acceptance-api      # Run API acceptance tests
make clean                    # Clean build artifacts
```

## Important Constraints

- Licensed under GPL-2.0 (copyleft). Apache 2.0 migration planned.
- Password policies apply to local accounts only, not LDAP-backed accounts.
- All contributions require a DCO sign-off.


## OSPO Policy Constraints

### GitHub Actions
- **Only** use actions owned by `owncloud`, created by GitHub (`actions/*`), verified on the GitHub Marketplace, or verified by the ownCloud Maintainers.
- Pin all actions to their full commit SHA (not tags): `uses: actions/checkout@<SHA> # vX.Y.Z`
- Never introduce actions from unverified third parties.

### Dependency Management
- Dependabot is configured for automated dependency updates.
- Review and merge Dependabot PRs as part of regular maintenance.
- Do not introduce new dependencies without discussion in an issue first.

### Git Workflow
- **Rebase policy**: Always rebase; never create merge commits. Use `git pull --rebase` and `git rebase` before pushing.
- **Signed commits**: All commits **must** be PGP/GPG signed (`git commit -S -s`).
- **DCO sign-off**: Every commit needs a `Signed-off-by` line (`git commit -s`).
- **Conventional Commits & Squash Merge**: Use the [Conventional Commits](https://www.conventionalcommits.org/) format where the repository enforces it. Many repos use squash merge, where the PR title becomes the commit message on the default branch — apply Conventional Commits format to PR titles as well. A reusable GitHub Actions workflow enforces this.

## Context for AI Agents

Configuration is via the Security section of ownCloud admin settings. The app tracks password history via hashed values, enforces expiration policies, and supports notification via email and web. Policies are defined in the lib/ classes.
