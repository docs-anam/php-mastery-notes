# Uploading Your Library to GitHub

## Overview

GitHub is the primary platform for hosting PHP libraries and open-source projects. This chapter covers creating a repository, pushing your code to GitHub, setting up continuous integration, and managing your project on GitHub.

---

## Table of Contents

1. Creating GitHub Repository
2. Initialize Local Git Repository
3. Pushing Code to GitHub
4. GitHub Repository Settings
5. Branches and Workflow
6. Continuous Integration
7. Releases and Tags
8. Documentation on GitHub
9. Complete Examples

---

## Creating GitHub Repository

### Sign Up and Setup

```
1. Go to https://github.com
2. Sign up for free account
3. Verify email address
4. Configure profile
5. Generate SSH key (recommended)
```

### SSH Key Setup

```bash
# Generate SSH key
ssh-keygen -t ed25519 -C "you@example.com"

# Add to SSH agent (macOS/Linux)
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519

# Add to SSH agent (Windows PowerShell)
Start-Service ssh-agent
ssh-add $env:USERPROFILE\.ssh\id_ed25519

# Copy public key
cat ~/.ssh/id_ed25519.pub | pbcopy  # macOS
cat ~/.ssh/id_ed25519.pub            # Linux

# GitHub Settings > SSH and GPG keys > New SSH key
# Paste public key
```

### Create Repository on GitHub

```
1. Click "+" in top right
2. Select "New repository"
3. Enter repository name (e.g., "slug-builder")
4. Add description
5. Choose Public (for open-source)
6. Add .gitignore for PHP
7. Add MIT license
8. Click "Create repository"
```

---

## Initialize Local Git Repository

### Git Configuration

```bash
# Configure git identity
git config --global user.name "Your Name"
git config --global user.email "you@example.com"

# Verify configuration
git config --global --list
```

### Initialize Repository

```bash
# Navigate to project
cd /path/to/my-library

# Initialize git
git init

# Add files
git add .

# Create initial commit
git commit -m "Initial commit: Add library structure"

# List commits
git log --oneline
```

### Create .gitignore

```
# .gitignore for PHP project

# Composer
composer.lock
vendor/

# IDE
.vscode/
.idea/
*.swp
*.swo
*~

# OS
.DS_Store
Thumbs.db

# Build
build/
coverage/
dist/

# Testing
.phpunit.cache/
```

---

## Pushing Code to GitHub

### Add Remote Repository

```bash
# Add GitHub repository as remote
git remote add origin https://github.com/username/my-library.git

# Or using SSH (recommended)
git remote add origin git@github.com:username/my-library.git

# Verify remote
git remote -v
# origin  git@github.com:username/my-library.git (fetch)
# origin  git@github.com:username/my-library.git (push)
```

### Push to GitHub

```bash
# Push to main branch
git push -u origin main

# Or master if old repository
git push -u origin master

# Verify on GitHub
# https://github.com/username/my-library
```

### HTTPS vs SSH

```
HTTPS:
- Copy/paste authentication token
- Easier for beginners
- Less secure if token exposed

SSH:
- SSH key authentication
- More secure
- Requires key setup

Recommended: SSH
```

---

## GitHub Repository Settings

### Repository Settings

```
Settings > General:
- Rename repository
- Description
- Website URL
- Topics (tags)
- Visibility (Public/Private)

Settings > Branches:
- Default branch (main)
- Protection rules
```

### Branch Protection

```
Settings > Branches > Add Rule:

Protect matching branches:
- main
- require pull request reviews before merging
- require status checks to pass
- require branches to be up to date
- include administrators
```

### Collaborators

```
Settings > Collaborators > Add people:
- Add team members
- Set permissions (Pull, Push, Admin)
- Send invitation
```

### Topics/Tags

```
Add topics to help discovery:
- php
- library
- composer
- slug-generator
- utilities
```

---

## Branches and Workflow

### Main Branch

```
main (stable releases)
├── v1.0.0 (release tag)
├── v1.0.1 (release tag)
├── v1.1.0 (release tag)
└── ...
```

### Development Workflow

```
develop (development branch)
├── feature/new-validator
├── feature/optimization
└── fix/bug-123

Process:
1. Create feature branch from develop
2. Make changes
3. Create pull request
4. Review and merge to develop
5. Tag and merge to main for release
```

### Creating Feature Branch

```bash
# Update develop
git checkout develop
git pull origin develop

# Create feature branch
git checkout -b feature/slug-support

# Make changes
echo "New feature" >> src/Feature.php

# Commit changes
git add .
git commit -m "Add slug support"

# Push to GitHub
git push -u origin feature/slug-support
```

### Pull Requests

```
GitHub > Pull Requests > New:

Title: "Add slug support"
Description:
- What changes are made
- Why they're needed
- Testing instructions
- Screenshots (if UI)

Reviewers: Select team members
Assignees: Yourself
Labels: feature, documentation
```

---

## Continuous Integration

### GitHub Actions

```
Create: .github/workflows/tests.yml

name: Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    strategy:
      matrix:
        php-version: ['8.0', '8.1', '8.2', '8.3']
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: ${{ matrix.php-version }}
          extensions: json, mbstring
      
      - name: Install Composer
        run: composer install --no-dev
      
      - name: Run tests
        run: ./vendor/bin/phpunit
```

### Travis CI (Legacy)

```
.travis.yml:

language: php

php:
  - "8.0"
  - "8.1"
  - "8.2"
  - "8.3"

install:
  - composer install

script:
  - ./vendor/bin/phpunit

notifications:
  email: false
```

### Code Quality Checks

```
.github/workflows/quality.yml:

name: Code Quality

on: [push, pull_request]

jobs:
  quality:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      
      - name: Install dependencies
        run: composer install
      
      - name: Run PHPStan
        run: ./vendor/bin/phpstan analyse
      
      - name: Run PHP_CodeSniffer
        run: ./vendor/bin/phpcs
```

---

## Releases and Tags

### Creating a Release

```bash
# Create a tag
git tag -a v1.0.0 -m "Version 1.0.0: Initial release"

# Push tag to GitHub
git push origin v1.0.0

# List tags
git tag -l
```

### GitHub Release

```
GitHub > Releases > Create new release:

Tag version: v1.0.0
Release title: Version 1.0.0 - Initial Release

Description:
## What's new

- Feature 1
- Feature 2
- Bug fix 3

## Installation

composer require vendor/library:^1.0

## Changelog

See CHANGELOG.md

Attachments: (optional)
- .zip file
- .tar.gz file

Pre-release: No
Latest release: Yes
```

### Semantic Versioning Tags

```
v1.0.0 = MAJOR.MINOR.PATCH

- Major: Breaking changes (v1.0.0 → v2.0.0)
- Minor: New features (v1.0.0 → v1.1.0)
- Patch: Bug fixes (v1.0.0 → v1.0.1)

Examples:
v0.1.0    Development, not stable
v1.0.0    First stable release
v1.1.0    New feature added
v1.0.1    Bug fix
v2.0.0    Major breaking change
v1.0.0-rc.1   Release candidate
v1.0.0-beta.1  Beta version
```

---

## Documentation on GitHub

### README.md

```markdown
# My Library

Short description of library.

[![Build Status](...)]()
[![Coverage Status](...)()]

## Installation

composer require vendor/library

## Quick Start

Basic usage example

## Features

- Feature 1
- Feature 2
- Feature 3

## Usage

Detailed usage with examples

### Basic Usage

Code example

### Advanced Usage

More examples

## API Reference

Class and method documentation

## Configuration

Configuration options

## Testing

How to run tests

## Contributing

Guidelines for contributors

## License

License information

## Support

Support information
```

### CONTRIBUTING.md

```markdown
# Contributing

Thank you for interest in contributing!

## Getting Started

1. Fork the repository
2. Clone your fork: git clone ...
3. Create feature branch: git checkout -b feature/name
4. Make changes
5. Run tests: ./vendor/bin/phpunit
6. Commit: git commit -m "Description"
7. Push: git push origin feature/name
8. Create Pull Request

## Code Style

We follow PSR-12. Run:

./vendor/bin/phpcbf

## Testing

Please add tests for new features.

## License

By contributing, you agree to license your work under the same license as the project (MIT).
```

### CHANGELOG.md

```markdown
# Changelog

## [1.1.0] - 2024-01-15

### Added
- New feature X
- Support for Y

### Changed
- Updated dependency Z

### Fixed
- Bug with A
- Issue with B

## [1.0.0] - 2024-01-01

### Added
- Initial release
- Core functionality

[1.1.0]: https://github.com/user/repo/compare/v1.0.0...v1.1.0
[1.0.0]: https://github.com/user/repo/releases/tag/v1.0.0
```

---

## Complete Examples

### Example: Complete GitHub Setup

```bash
# Create local repository
mkdir my-library && cd my-library

# Initialize git
git init
git config user.name "Your Name"
git config user.email "you@example.com"

# Create composer.json
cat > composer.json << 'EOF'
{
    "name": "vendor/my-library",
    "description": "My awesome library",
    "type": "library",
    "license": "MIT",
    "authors": [{
        "name": "Your Name",
        "email": "you@example.com"
    }],
    "require": {"php": ">=8.0"},
    "require-dev": {"phpunit/phpunit": "^10.0"},
    "autoload": {
        "psr-4": {"MyVendor\\MyLibrary\\": "src/"}
    }
}
EOF

# Create source
mkdir -p src tests
touch src/MyClass.php
touch tests/MyClassTest.php

# Create documentation
touch README.md LICENSE CHANGELOG.md

# Create .gitignore
cat > .gitignore << 'EOF'
vendor/
composer.lock
.DS_Store
.idea/
.vscode/
build/
coverage/
EOF

# Commit
git add .
git commit -m "Initial commit"

# Add GitHub remote
git remote add origin git@github.com:username/my-library.git

# Push to GitHub
git push -u origin main
```

---

## Key Takeaways

**GitHub Setup Checklist:**

1. ✅ Create GitHub account and SSH key
2. ✅ Create repository on GitHub
3. ✅ Initialize local git repository
4. ✅ Create .gitignore file
5. ✅ Add GitHub as remote
6. ✅ Push initial commit
7. ✅ Configure branch protection
8. ✅ Set up GitHub Actions (CI/CD)
9. ✅ Create comprehensive README
10. ✅ Tag releases with semantic versioning

---

## See Also

- [Creating Your Own Library](7-create-own-library.md)
- [Using Libraries from GitHub](9-download-or-use-own-library-from-github.md)
- [Submitting to Packagist](11-submit-own-library-to-packagist.md)
