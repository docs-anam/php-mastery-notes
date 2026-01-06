# Composer Repositories

## Overview

Repositories are sources where Composer looks for packages. By default, Composer uses Packagist, but you can configure custom repositories for private packages, alternative sources, or specific versions. This chapter covers different repository types and how to configure them.

---

## Table of Contents

1. Understanding Repositories
2. Packagist Repository
3. VCS (Git) Repositories
4. Artifact Repositories
5. Composer Type Repositories
6. Custom Repository Configuration
7. Repository Priority and Searching
8. Private Repositories
9. Complete Examples

---

## Understanding Repositories

### Repository Types

```
Packagist        Official PHP package registry
                 public.packagist.org

VCS              Version Control System (Git, SVN, etc.)
                 Direct from repositories

Artifact         Local directory with .zip files
                 Offline packages

Composer-Type    Composer-based registry
                 Alternative to Packagist

Pear             PEAR package repository (legacy)

Path             Local filesystem directory
                 For development
```

### How Composer Searches

```
1. Check configured repositories in order
2. Download package metadata
3. Resolve dependencies
4. Download packages
5. Install in vendor/

If not found in first repo, try next
If not found in any, error
```

---

## Packagist Repository

### Default Repository

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://repo.packagist.org"
        }
    ]
}
```

### Packagist Mirror

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://mirrors.aliyun.com/composer/"
        }
    ]
}
```

### Searching Packagist

```bash
# Command line search
composer search monolog

# Shows available packages:
# monolog/monolog  Sends your logs to files, sockets and more
# monolog/handlers Additional handlers for monolog
```

### Browsing Packagist

```
Web: https://packagist.org/

Search packages
View documentation
Check versions
Read requirements
```

---

## VCS (Git) Repositories

### Using Git Repositories

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/user/my-package.git"
        }
    ],
    "require": {
        "user/my-package": "dev-main"
    }
}
```

### GitHub Repository

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/package.git"
        }
    ],
    "require": {
        "vendor/package": "dev-master"
    }
}
```

### GitLab Repository

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://gitlab.com/vendor/package.git"
        }
    ],
    "require": {
        "vendor/package": "dev-main"
    }
}
```

### Private Git Repository

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mycompany/private-package.git"
        }
    ],
    "require": {
        "mycompany/private-package": "dev-main"
    }
}
```

### Using Branches and Tags

```json
{
    "require": {
        "vendor/package": "dev-master",       // master branch
        "vendor/package": "dev-develop",      // develop branch
        "vendor/package": "dev-feature/name", // feature branch
        "vendor/package": "1.0.x-dev",        // release branch
        "vendor/package": "v1.0.0"            // tag
    }
}
```

---

## Artifact Repositories

### Artifact Repository Configuration

```json
{
    "repositories": [
        {
            "type": "artifact",
            "directory": "vendor-packages"
        }
    ]
}
```

### Directory Structure

```
my-project/
├── vendor-packages/
│   ├── myvendor-mypackage-1.0.0.zip
│   ├── myvendor-another-2.0.0.zip
│   ├── myvendor-package-dev.zip
│   └── ...
├── composer.json
└── vendor/
```

### Creating Artifact Packages

```bash
# Package your library
cd my-package
zip -r ../vendor-packages/myvendor-mypackage-1.0.0.zip \
    src/ composer.json vendor/

# Now Composer can use it
composer require myvendor/mypackage:1.0.0
```

### Use Cases

```
- Offline deployments
- Internal packages
- Legacy packages
- Air-gapped environments
- Package caching
```

---

## Composer Type Repositories

### Alternative Packagist

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://packages.example.com"
        }
    ]
}
```

### Self-Hosted Composer Registry

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://internal-packagist.company.com"
        }
    ],
    "require": {
        "company/internal-package": "^1.0"
    }
}
```

---

## Custom Repository Configuration

### Disabling Packagist

```json
{
    "repositories": [
        {
            "packagist.org": false
        }
    ]
}
```

### Using Packagist Conditionally

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://private-packagist.company.com"
        },
        {
            "type": "composer",
            "url": "https://repo.packagist.org"
        }
    ]
}
```

### Repository Authentication

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/private-repo.git"
        }
    ]
}
```

```bash
# Configure git credentials
git config --global github.user "username"
git config --global github.token "token"

# Or use SSH
git config core.sshCommand "ssh -i ~/.ssh/id_rsa"
```

---

## Repository Priority and Searching

### Repository Order

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://high-priority.com"
        },
        {
            "type": "composer",
            "url": "https://medium-priority.com"
        },
        {
            "type": "composer",
            "url": "https://repo.packagist.org"
        }
    ]
}
```

**Composer searches in order:**
1. First repository
2. Second repository
3. Third repository
4. Stops at first match

### Force Custom Repo

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/package.git"
        },
        {
            "packagist.org": false
        }
    ]
}
```

---

## Private Repositories

### GitHub Personal Access Token

```bash
# Create token at: https://github.com/settings/tokens

# Configure Composer
composer config --auth github-oauth.github.com "your-token-here"

# Or add to composer.json
```

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/private-package.git"
        }
    ],
    "require": {
        "vendor/private-package": "dev-main"
    }
}
```

### Using SSH Keys

```bash
# SSH key must be configured
ssh-add ~/.ssh/id_rsa

# Composer will use SSH for git@github.com
```

```json
{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:vendor/private-package.git"
        }
    ]
}
```

### Private Packagist

```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "https://repo.packagist.com"
        }
    ],
    "config": {
        "http-basic": {
            "repo.packagist.com": {
                "username": "token",
                "password": "secret"
            }
        }
    }
}
```

---

## Complete Examples

### Example 1: Simple Project

```json
{
    "name": "my-company/my-app",
    "repositories": [
        {
            "type": "composer",
            "url": "https://repo.packagist.org"
        }
    ],
    "require": {
        "php": ">=8.0",
        "symfony/framework-bundle": "^6.0"
    }
}
```

### Example 2: Mixed Repositories

```json
{
    "name": "my-company/project",
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/mycompany/private-lib.git"
        },
        {
            "type": "composer",
            "url": "https://repo.packagist.org"
        }
    ],
    "require": {
        "mycompany/private-lib": "dev-main",
        "symfony/console": "^6.0",
        "monolog/monolog": "^2.0"
    }
}
```

### Example 3: Private and Artifact

```json
{
    "name": "my-company/enterprise-app",
    "repositories": [
        {
            "type": "artifact",
            "directory": "vendor-packages"
        },
        {
            "type": "vcs",
            "url": "git@bitbucket.org:mycompany/private.git"
        },
        {
            "type": "composer",
            "url": "https://repo.packagist.org"
        }
    ],
    "require": {
        "mycompany/legacy": "1.0",
        "mycompany/private": "dev-main",
        "vendor/public": "^2.0"
    }
}
```

### Example 4: Development Setup

```json
{
    "name": "developer/testing",
    "repositories": [
        {
            "type": "path",
            "url": "../../projects/my-local-package",
            "options": {
                "symlink": true
            }
        },
        {
            "type": "vcs",
            "url": "https://github.com/vendor/package.git"
        }
    ],
    "require": {
        "local/package": "dev-main",
        "vendor/package": "dev-develop"
    }
}
```

---

## Key Takeaways

**Repository Configuration Checklist:**

1. ✅ Understand default Packagist repository
2. ✅ Use VCS for development branches
3. ✅ Configure private repositories securely
4. ✅ Set repository priority correctly
5. ✅ Use authentication tokens for private access
6. ✅ Test repository access with `composer update`
7. ✅ Document custom repositories for team
8. ✅ Use artifact repositories for offline deployments

---

## See Also

- [Creating Composer Projects](3-create-composer-project.md)
- [Adding Dependencies](6-add-dependency-library.md)
- [Submitting to Packagist](11-submit-own-library-to-packagist.md)
