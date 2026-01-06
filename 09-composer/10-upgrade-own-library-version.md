# Upgrading Your Library Version

## Overview

As your library evolves, you'll release new versions with bug fixes, new features, and improvements. This chapter covers semantic versioning, managing versions, updating libraries, and communicating changes to users.

---

## Table of Contents

1. Semantic Versioning
2. Version Planning
3. Creating Releases
4. Breaking Changes
5. Changelog Management
6. Updating Versions
7. Pre-releases
8. Backward Compatibility
9. Complete Examples

---

## Semantic Versioning

### Version Format

```
MAJOR.MINOR.PATCH

1.2.3
│ │ └─ Patch version
│ └─── Minor version
└───── Major version

Changes:
- Patch: Bug fixes, no API changes (1.2.3 → 1.2.4)
- Minor: New features, backward compatible (1.2.0 → 1.3.0)
- Major: Breaking changes (1.0.0 → 2.0.0)
```

### Version Examples

```
0.1.0   Initial development version
0.5.0   Development progressing
1.0.0   First stable release
1.0.1   Patch: Bug fix
1.1.0   Minor: New feature
1.2.0   Minor: Another feature
2.0.0   Major: Breaking changes

Pre-releases:
1.0.0-alpha.1   Early development
1.0.0-beta.1    Feature complete, testing
1.0.0-rc.1      Release candidate
```

### Backward Compatibility (BC)

```
Backward Compatible:
- Adding new public method ✓
- Adding optional parameter ✓
- Deprecating (with notice) ✓
- Bug fixes ✓

Breaking Changes:
- Removing method ✗
- Changing method signature ✗
- Changing return type ✗
- Removing constant ✗
```

---

## Version Planning

### Semantic Versioning Strategy

```
1. Version 0.x (Development)
   0.1.0  First features
   0.5.0  Major features complete
   0.9.0  Feature complete, testing

2. Version 1.0 (First Stable)
   1.0.0  Stable release
   API is stable, breaking changes avoided

3. Version 1.x (Stable with Features)
   1.1.0  New features (backward compatible)
   1.2.0  More features
   1.n.0  Latest stable

4. Version 2.0 (Major Rewrite)
   2.0.0  Breaking changes allowed
   New API, cleanup, improvements
```

### Release Timeline

```
Development Branch
├── feature/new-feature
├── bugfix/issue-123
├── feature/improvement
└── Release Preparation
    ├── Code review
    ├── Testing
    ├── Changelog update
    ├── Version bump
    ├── Tag v1.1.0
    └── Release announcement
```

---

## Creating Releases

### Update Version in composer.json

```json
{
    "name": "mycompany/my-library",
    "version": "1.1.0",
    "description": "My library",
    "type": "library"
}
```

### Update Changelog

```
CHANGELOG.md

## [1.1.0] - 2024-01-15

### Added
- New slug validation method
- Support for custom separators
- Performance improvements

### Changed
- Updated dependencies
- Improved error messages

### Fixed
- Bug in validation logic
- Memory leak in processing

### Deprecated
- Old generateSlug method (use generate instead)

### Removed
- Support for PHP 7.4

[Previous versions...]
```

### Commit and Tag

```bash
# Update version
# Update CHANGELOG.md

# Commit
git add composer.json CHANGELOG.md
git commit -m "Version 1.1.0"

# Create tag
git tag -a v1.1.0 -m "Version 1.1.0: New slug validation"

# Push to GitHub
git push origin main
git push origin v1.1.0
```

### Create GitHub Release

```
GitHub > Releases > Create new release:

Tag version: v1.1.0
Release title: Version 1.1.0 - Slug Validation

Description:
## What's New

### Added
- Slug validation method
- Custom separator support

### Fixed
- Performance issues
- Memory leaks

### Changed
- Updated dependencies

## Installation

composer require mycompany/my-library:^1.1

## Migration Guide

No breaking changes. Update normally.

## Contributors

Thanks to all contributors!
```

---

## Breaking Changes

### When to Make Breaking Changes

```
Good reasons:
- API is truly broken
- Prevents security issues
- Significant improvement
- Library is in development (0.x)

Bad reasons:
- Easier to implement
- Personal preference
- Lack of planning
- Avoid deprecation warnings
```

### Handling Breaking Changes

```
Step 1: Release stable 1.0 (if 0.x)
       Gather feedback, stabilize API

Step 2: Plan 2.0
       Document breaking changes
       Create migration guide
       Plan deprecation (1.x versions)

Step 3: Deprecate in 1.x
       Add deprecation warnings
       Update documentation
       Give time (2-3 versions)

Step 4: Release 2.0
       Remove deprecated code
       Update migration guide
       Announce breaking changes

Example Timeline:
1.0.0  Initial stable release
1.1.0  Add deprecation warnings for method X
1.2.0  More features, warning still there
1.3.0  Last 1.x release
2.0.0  Remove deprecated method X
```

### Deprecation Warnings

```php
<?php
// src/Calculator.php

namespace MyCompany\MyLibrary;

class Calculator {
    /**
     * @deprecated 1.1.0 Use calculate() instead
     */
    public function add($a, $b) {
        trigger_error(
            'add() is deprecated, use calculate() instead',
            E_USER_DEPRECATED
        );
        return $this->calculate($a, $b);
    }
    
    public function calculate($a, $b) {
        return $a + $b;
    }
}
```

---

## Changelog Management

### Changelog Format (Keep a Changelog)

```markdown
# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/),
and this project adheres to [Semantic Versioning](https://semver.org/).

## [Unreleased]

### Added
- (Changes not yet released)

### Changed
- (Changes not yet released)

### Removed
- (Changes not yet released)

## [1.1.0] - 2024-01-15

### Added
- New feature X
- New method Y

### Changed
- Updated dependency Z
- Improved performance

### Fixed
- Bug with A

## [1.0.0] - 2024-01-01

### Added
- Initial release

[Unreleased]: https://github.com/vendor/lib/compare/v1.1.0...HEAD
[1.1.0]: https://github.com/vendor/lib/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/vendor/lib/releases/tag/v1.0.0
```

### Automated Changelog

```bash
# Using github-changelog-generator
gem install github_changelog_generator

# Generate changelog
github_changelog_generator -u vendor -p library

# Merge with CHANGELOG.md
```

---

## Updating Versions

### Composer require Update

```bash
# Check for available updates
composer outdated mycompany/my-library

# Update to latest stable
composer update mycompany/my-library

# Update to specific version
composer update mycompany/my-library:^1.1
```

### Handling Dependencies

```
Library Update Strategy:
1. Minor update (1.0 → 1.1)
   - New features
   - Backward compatible
   - Safe to update

2. Major update (1.0 → 2.0)
   - Breaking changes
   - May need code updates
   - Review migration guide

3. Development update (dev-main)
   - For testing
   - May be unstable
   - Lock with commit hash
```

---

## Pre-releases

### Alpha, Beta, RC

```
Pre-release versions:
1.0.0-alpha.1   Design complete, early development
1.0.0-beta.1    Feature complete, testing phase
1.0.0-rc.1      Ready for release, final testing
1.0.0           Stable release

Tagging:
git tag -a v1.0.0-alpha.1 -m "Version 1.0.0-alpha.1"
git tag -a v1.0.0-beta.1 -m "Version 1.0.0-beta.1"
git tag -a v1.0.0-rc.1 -m "Version 1.0.0-rc.1"
git tag -a v1.0.0 -m "Version 1.0.0"
```

### Using Pre-releases

```json
{
    "require": {
        "mycompany/my-library": "1.0.0-rc.1"
    },
    "minimum-stability": "rc"
}
```

### Testing Pre-releases

```bash
# Install pre-release
composer require mycompany/my-library:^1.0.0-rc

# Test thoroughly
./vendor/bin/phpunit

# Report issues on GitHub
# Fixed issues? Test again
```

---

## Backward Compatibility

### BC Guarantees

```
Our Library Promises:
- Version 1.x will not break your code
- New features are additive
- Deprecated features have notices
- Migration guides provided
- 2+ versions warning before removal

What you can rely on:
- Public API doesn't change unexpectedly
- Can safely update to 1.x.x
- No surprise breaking changes
```

### BC Testing

```php
// Test backward compatibility

// Old API still works
$lib = new MyCompany\MyLibrary\MyClass();
$lib->oldMethod();  // Still works

// New API is available
$lib->newMethod();  // New feature

// Deprecated gets warning
trigger_error(
    'oldMethod() is deprecated, use newMethod()',
    E_USER_DEPRECATED
);
```

---

## Complete Examples

### Example 1: Simple Patch Release (1.0.0 → 1.0.1)

```bash
# Bug fix in src/Calculator.php
# No API changes

# Update version
sed -i 's/"version": "1.0.0"/"version": "1.0.1"/' composer.json

# Update changelog
cat >> CHANGELOG.md << 'EOF'

## [1.0.1] - 2024-01-10

### Fixed
- Bug in add() method calculation
- Memory issue in process()
EOF

# Commit
git add -A
git commit -m "Version 1.0.1: Bug fixes"

# Tag and push
git tag -a v1.0.1 -m "Version 1.0.1"
git push origin main v1.0.1

# Create GitHub release
# Title: Version 1.0.1 - Bug Fixes
# Description: Fixed critical bugs
```

### Example 2: Feature Release (1.0.0 → 1.1.0)

```bash
# New features in src/
# No breaking changes
# Backward compatible

# Update version
sed -i 's/"version": "1.0.0"/"version": "1.1.0"/' composer.json

# Update CHANGELOG.md
# Document: Added, Changed, Fixed

# Commit and tag
git add -A
git commit -m "Version 1.1.0: New validation features"
git tag -a v1.1.0 -m "Version 1.1.0: New validation features"
git push origin main v1.1.0
```

### Example 3: Major Release (1.x.x → 2.0.0)

```bash
# Breaking changes in API
# Removed deprecated methods
# New structure

# Update version
sed -i 's/"version": "1.9.0"/"version": "2.0.0"/' composer.json

# Update changelog with "BREAKING CHANGES" section
cat >> CHANGELOG.md << 'EOF'

## [2.0.0] - 2024-02-01

### Added
- New simplified API
- Performance improvements

### Changed
- Completely restructured library
- Updated to PHP 8.1+ only

### Removed
- Old addslug() method (use add() instead)
- Legacy validator class
- Support for PHP 7.x

### Migration Guide

See MIGRATION.md for upgrade instructions.
EOF

# Create MIGRATION.md
cat > MIGRATION.md << 'EOF'
# Migrating from 1.x to 2.x

## API Changes

### Renamed Methods
- addslug() → add()
- validateSlug() → validate()

### New Requirements
- Requires PHP 8.1+

### Example Migration

```php
// Old (1.x)
$slug = new MyLib();
$slug->addslug('My Title');

// New (2.x)
$slug = new MyLib();
$slug->add('My Title');
```

## Complete Upgrade Guide
...
EOF

# Commit
git add -A
git commit -m "Version 2.0.0: Major API redesign"

# Tag
git tag -a v2.0.0 -m "Version 2.0.0: Major release"
git push origin main v2.0.0
```

---

## Key Takeaways

**Version Management Checklist:**

1. ✅ Follow semantic versioning (MAJOR.MINOR.PATCH)
2. ✅ Update composer.json version field
3. ✅ Maintain detailed CHANGELOG.md
4. ✅ Commit version bumps
5. ✅ Create annotated git tags
6. ✅ Push tags to GitHub
7. ✅ Create GitHub releases
8. ✅ Avoid breaking changes in minor/patch versions
9. ✅ Provide migration guides for major versions
10. ✅ Use pre-releases for testing (alpha, beta, rc)

---

## See Also

- [Creating Your Own Library](7-create-own-library.md)
- [Uploading to GitHub](8-upload-own-library-to-github.md)
- [Submitting to Packagist](11-submit-own-library-to-packagist.md)
