# Using Your Library from GitHub

## Overview

After uploading your library to GitHub, you can use it in other projects using Composer. This chapter covers how to use your own GitHub packages, specify versions from branches, manage development versions, and troubleshoot common issues.

---

## Table of Contents

1. Using GitHub Packages
2. VCS Repository Configuration
3. Development Versions
4. Version Constraints
5. SSH Authentication
6. Updating from GitHub
7. Troubleshooting
8. Local Development
9. Complete Examples

---

## Using GitHub Packages

### Basic Usage

```bash
# Your GitHub library
# https://github.com/mycompany/my-library

# Configure in composer.json
```

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
```

```bash
# Install
composer install

# Composer downloads from GitHub
# Uses branch/tag from GitHub
# No Packagist registration needed
```

### What Composer Does

```
1. Read repositories configuration
2. Clone GitHub repository
3. Read composer.json from repository
4. Resolve dependencies
5. Download/extract package
6. Generate autoloader
7. Update composer.lock
```

---

## VCS Repository Configuration

### HTTPS Access

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
```

**Pros:**
- Works without SSH key
- Simple setup

**Cons:**
- May need authentication for private repos
- Less secure for private access

### SSH Access

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
```

**Pros:**
- More secure
- Better for private repositories

**Cons:**
- Requires SSH key setup
- Requires SSH authentication

### GitHub Token (HTTPS Private)

```bash
# For private repositories, use GitHub token
composer config github-oauth.github.com "your_github_token"

# Token from: https://github.com/settings/tokens
# Permissions: repo (Full control)
```

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mycompany/private-library.git"
        }
    ],
    "require": {
        "mycompany/private-library": "dev-main"
    }
}
```

---

## Development Versions

### Using Branches

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main",
        "mycompany/my-library": "dev-develop",
        "mycompany/my-library": "dev-feature/new-validator"
    }
}
```

### Branch Naming

```
main              Main/master branch (stable)
develop           Development branch
feature/name      Feature branches
bugfix/name       Bug fix branches
release/1.0       Release branches

Composer syntax:
dev-main          Branch: main
dev-develop       Branch: develop
dev-feature/name  Branch: feature/name
```

### Development Stability

```json
{
    "require": {
        "mycompany/my-library": "dev-main@dev"
    },
    "minimum-stability": "dev"
}
```

**Note:** @dev means allow development version (not stable)

---

## Version Constraints

### Tag Versions

```json
{
    "require": {
        "mycompany/my-library": "v1.0.0"
    }
}
```

GitHub tags: `v1.0.0`, `v1.0.1`, `v1.1.0`, `v2.0.0`

### Branch as Version

```json
{
    "require": {
        "mycompany/my-library": "dev-main",
        "mycompany/my-library": "1.0.x-dev"
    }
}
```

### Caret Constraints

```json
{
    "require": {
        "mycompany/my-library": "^1.0"
    }
}
```

Matches: >= 1.0, < 2.0
Uses latest tags matching version

### Tilde Constraints

```json
{
    "require": {
        "mycompany/my-library": "~1.0"
    }
}
```

Matches: >= 1.0, < 2.0
Similar to caret for development

---

## SSH Authentication

### Setup SSH Key

```bash
# Generate SSH key (if not exists)
ssh-keygen -t ed25519 -C "you@example.com"

# Add to SSH agent
eval "$(ssh-agent -s)"
ssh-add ~/.ssh/id_ed25519

# Copy public key
cat ~/.ssh/id_ed25519.pub | pbcopy

# Add to GitHub: Settings > SSH and GPG keys > New SSH key
```

### Test SSH Connection

```bash
# Test SSH access to GitHub
ssh -T git@github.com

# Should output:
# Hi username! You've successfully authenticated, but GitHub does not provide shell access.
```

### Using SSH in composer.json

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
```

---

## Updating from GitHub

### Update Package

```bash
# Update single package
composer update mycompany/my-library

# Update with dependencies
composer update --with-all-dependencies mycompany/my-library

# Update all packages
composer update
```

### Update from Branch

```bash
# Update to latest from main
composer update mycompany/my-library:dev-main

# Update to latest from develop
composer update mycompany/my-library:dev-develop

# Update specific branch
composer update mycompany/my-library:dev-feature/new-feature
```

### Lock File

```bash
# composer.lock will contain:
# - Commit hash from GitHub
# - Branch name
# - Reference

# Lock keeps team in sync with same version
cat composer.lock
# "reference": "abc123def456" (commit hash)
```

---

## Troubleshooting

### Package Not Found

```
Error: Could not find package mycompany/my-library

Solutions:
1. Check repository URL is correct
2. Check repository is public (if using HTTPS without token)
3. Check branch/tag exists on GitHub
4. Clear cache: composer clear-cache

Debug:
composer diagnose
```

### Authentication Issues

```
Error: The git clone command failed

Solutions:
1. Check SSH key is added to agent
2. Verify GitHub token if using HTTPS
3. Test: ssh -T git@github.com
4. Check GitHub account has repo access

Debug:
git clone git@github.com:user/repo.git
```

### Dependency Conflicts

```
Error: Your requirements could not be resolved

Solution:
Check that package composer.json has compatible dependencies
Update to specific version:
"mycompany/my-library": "^1.0"

Or use:
composer update --with-all-dependencies
```

### Cache Issues

```bash
# Clear Composer cache
composer clear-cache

# Clear vendor directory
rm -rf vendor/

# Reinstall
composer install
```

---

## Local Development

### Path Repository (Local Development)

For developing your library locally while using it in another project:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../../projects/my-library",
            "options": {
                "symlink": true
            }
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
```

**How it works:**
1. Creates symlink to local library
2. Changes in library immediately visible
3. No git commits needed
4. Perfect for development

### Development Workflow

```bash
# Project structure
~/projects/
├── my-library/        (library development)
│   └── src/
│   └── tests/
│   └── composer.json
└── my-app/           (application)
    └── composer.json (uses my-library)

# Configure my-app/composer.json
{
    "repositories": [
        {
            "type": "path",
            "url": "../my-library",
            "options": {"symlink": true}
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}

# Install
cd my-app && composer install

# Now my-library/src/ changes immediately visible in my-app
```

### Switching from Local to GitHub

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
```

---

## Complete Examples

### Example 1: Simple GitHub Usage

```bash
# Create new project
mkdir my-project && cd my-project

# Create composer.json
cat > composer.json << 'EOF'
{
    "name": "mycompany/my-project",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
EOF

# Install
composer install

# Use library
php -r "require 'vendor/autoload.php'; new MyCompany\\MyLibrary\\MyClass();"
```

### Example 2: Using Stable Release

```bash
# Using tagged release
cat > composer.json << 'EOF'
{
    "name": "mycompany/production-app",
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:mycompany/my-library.git"
        }
    ],
    "require": {
        "mycompany/my-library": "^1.0"
    }
}
EOF

composer install
```

### Example 3: Development Setup

```bash
# Using local symlink while developing
cat > composer.json << 'EOF'
{
    "name": "mycompany/dev-project",
    "repositories": [
        {
            "type": "path",
            "url": "../my-library",
            "options": {"symlink": true}
        }
    ],
    "require": {
        "mycompany/my-library": "dev-main"
    }
}
EOF

composer install

# Test local changes
# Commit and push to GitHub
# Merge to main
# Tag release v1.1.0
# Update production app to use v1.1.0
```

---

## Key Takeaways

**Using GitHub Libraries Checklist:**

1. ✅ Configure VCS repository in composer.json
2. ✅ Specify branch or tag version
3. ✅ Set up SSH or HTTPS authentication
4. ✅ Run `composer install` to fetch from GitHub
5. ✅ Use path repositories for local development
6. ✅ Update with `composer update`
7. ✅ Test before deploying to production
8. ✅ Use stable tags for production
9. ✅ Use development branches for testing
10. ✅ Clear cache if experiencing issues

---

## See Also

- [Uploading to GitHub](8-upload-own-library-to-github.md)
- [Creating Your Own Library](7-create-own-library.md)
- [Submitting to Packagist](11-submit-own-library-to-packagist.md)
