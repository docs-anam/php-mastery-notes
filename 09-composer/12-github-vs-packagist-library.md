# GitHub vs Packagist for Libraries

## Overview

When hosting and distributing PHP libraries, you have choices between GitHub and Packagist. Both serve different purposes. This chapter compares them, discusses when to use each, and how they work together.

---

## Table of Contents

1. GitHub Overview
2. Packagist Overview
3. Key Differences
4. Use Case Comparison
5. Advantages and Disadvantages
6. How They Work Together
7. Choosing the Right Platform
8. Best Practices
9. Complete Examples

---

## GitHub Overview

### What is GitHub

```
GitHub = Code hosting and version control platform

Features:
- Git repository hosting
- Version control and history
- Collaboration tools
- Issue tracking
- Pull requests
- Discussions
- Actions (CI/CD)
- Security scanning
- Releases and tags
- Documentation (wiki)

Purpose:
- Store source code
- Track changes over time
- Collaborate with developers
- Manage project
```

### GitHub for PHP Libraries

```
GitHub Role:
1. Source code repository
2. Version control
3. Collaboration
4. Issue tracking
5. Documentation

GitHub as Distribution:
composer require git@github.com:vendor/package.git
composer require github.com/vendor/package:dev-main

Use case:
- Private libraries
- Early development
- Direct from source
- VCS repository option
```

---

## Packagist Overview

### What is Packagist

```
Packagist = Central PHP package registry

Features:
- Package index and registry
- Package search and discovery
- Version management
- Automatic updates
- Download statistics
- Package metadata
- Dependency resolution
- Mirror/CDN integration

Purpose:
- Discover packages
- Install packages easily
- Share with PHP community
```

### Packagist for PHP Libraries

```
Packagist Role:
1. Package discovery
2. Package registry
3. Download statistics
4. Package metadata
5. Integration with Composer

Packagist Distribution:
composer require vendor/package
composer require vendor/package:^1.0

Use case:
- Public libraries
- Community sharing
- Easy discovery
- Standard distribution
```

---

## Key Differences

### Hosting vs Registry

```
GitHub:
- Hosts your code
- Stores files and history
- Manages versions (git tags)
- Provides collaboration
- Stores actual source code

Packagist:
- Indexes packages
- Doesn't store code
- Lists available versions
- Points to GitHub
- Metadata and search
```

### Discovery

```
GitHub:
- Search for projects
- Browse profile
- View commits and history
- See contributors
- Check issues

Packagist:
- Search by keywords
- Keyword-based discovery
- Package-specific pages
- Statistics and ratings
- Version information
```

### Installation Methods

```
GitHub (Direct):
composer require git@github.com:vendor/package.git
composer require github.com/vendor/package.git:dev-main

Composer requirement:
{
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/vendor/package.git"
        }
    ],
    "require": {
        "vendor/package": "dev-main"
    }
}

Packagist (Registry):
composer require vendor/package
composer require vendor/package:^1.0

Composer requirement:
{
    "require": {
        "vendor/package": "^1.0"
    }
}

Easier and preferred: Packagist
```

---

## Use Case Comparison

### Use GitHub When

```
1. Private libraries
   - Company internal code
   - Not for public use
   - Paid/proprietary

2. Early development
   - Pre-release versions (0.x)
   - Unstable API
   - Still being designed

3. Internal tools
   - Company tools
   - Team-only libraries
   - No external users

4. Direct integration
   - Clone and work locally
   - Custom repository setup
   - Special cases

5. Hosting only
   - Just need code repository
   - Don't need discovery
   - Manual user acquisition
```

### Use Packagist When

```
1. Public libraries
   - Open source projects
   - Shared with community
   - Want widespread adoption

2. Stable releases
   - Version 1.0+ released
   - Stable API
   - Ready for public use

3. Community packages
   - Solve common problems
   - Benefit many developers
   - Foster collaboration

4. Easy discovery
   - Users can find easily
   - Search engine friendly
   - Packagist directory

5. Standard installation
   - Simple composer require
   - No special configuration
   - Best practices
```

---

## Advantages and Disadvantages

### GitHub Advantages

```
✓ Free hosting for public/private
✓ Full source code history
✓ Issue tracking
✓ Pull request workflow
✓ Collaboration tools
✓ CI/CD (GitHub Actions)
✓ Security scanning
✓ No registration needed for public repos
✓ Large community
✓ Web interface for browsing
```

### GitHub Disadvantages

```
✗ Not a package registry
✗ Users must know repository URL
✗ Requires VCS configuration in composer.json
✗ Version management more complex
✗ No built-in package discovery
✗ Not in composer require path
✗ Slower installation (git clone)
✗ Requires git knowledge
```

### Packagist Advantages

```
✓ Automatic package discovery
✓ Simple composer require
✓ Integrated with Composer
✓ Download statistics
✓ Fast installation from mirror
✓ Version management
✓ SEO friendly
✓ Community visibility
✓ No special configuration needed
✓ Automated dependency resolution
```

### Packagist Disadvantages

```
✗ No code hosting (needs GitHub)
✗ No issue tracking
✗ No pull requests
✗ Doesn't store source code
✗ Requires Packagist account
✗ Depends on GitHub availability
✗ Manual updates if webhook fails
✗ Metadata-only registry
```

---

## How They Work Together

### The Standard Workflow

```
GitHub = Source Code Repository
├── Code and history
├── Version control
├── Issue tracking
└── Release tags (v1.0.0)

        ↓ (Push code)

Packagist = Package Registry
├── Indexes versions
├── Lists metadata
├── Provides statistics
└── enables composer require

        ↓ (Install)

Composer
├── Searches Packagist
├── Finds on GitHub
├── Downloads from mirror
└── Installs in vendor/

Developer
├── composer require vendor/package
└── Uses library
```

### Webhook Connection

```
1. Push to GitHub
   git push origin main
   git push origin v1.0.0

2. GitHub webhook triggers
   Notifies Packagist

3. Packagist fetches composer.json
   From GitHub repository

4. Packagist updates index
   New version available

5. Composer can require
   composer require vendor/package:^1.0
```

---

## Choosing the Right Platform

### Decision Tree

```
Is library public and stable?
├─ Yes → Use BOTH
│        GitHub (hosting) + Packagist (discovery)
│
└─ No → Use GitHub only
        Public but unstable → Wait for 1.0
        Private → GitHub with SSH
        Internal → GitHub only
```

### Platform Recommendation

```
RECOMMENDED: Use Both

GitHub:
- Host source code
- Manage versions (git tags)
- Track issues
- Accept pull requests
- CI/CD testing

Packagist:
- Register public package
- Enable composer require
- Gather statistics
- Share with community

Together = Complete solution
```

### Timeline

```
Development Phase:
- GitHub only
- Version 0.x
- VCS repository

Beta Phase:
- GitHub only or Packagist
- Pre-release versions
- Test with users

Release Phase:
- Both GitHub and Packagist
- Version 1.0+
- Community adoption

Maintenance Phase:
- Both platforms
- Regular updates
- Monitor usage statistics
```

---

## Best Practices

### Optimal Setup

```
Step 1: Develop on GitHub
- Create repository
- Push code
- Manage versions

Step 2: Release 1.0
- Stable API
- Comprehensive tests
- Good documentation

Step 3: Submit to Packagist
- Register account
- Submit package
- Enable webhook

Step 4: Maintain Both
- Update GitHub
- Packagist syncs automatically
- Monitor statistics

Result:
- Users can discover on Packagist
- Install with composer require
- Report issues on GitHub
- Contribute via pull requests
```

### GitHub Best Practices

```
✓ Use semantic versioning (v1.0.0)
✓ Create annotated tags
✓ Write clear commit messages
✓ Keep README up-to-date
✓ Use GitHub releases
✓ Set up CI/CD
✓ Enable security scanning
✓ Respond to issues promptly
✓ Accept good pull requests
```

### Packagist Best Practices

```
✓ Keep composer.json valid
✓ Include proper metadata
✓ Use relevant keywords
✓ Write good description
✓ Link to documentation
✓ Update frequently
✓ Monitor download stats
✓ Respond to feedback
✓ Maintain stability
```

---

## Complete Examples

### Example 1: Public Library (Recommended Setup)

```bash
# Step 1: Create on GitHub
# Create repository: github.com/mycompany/my-library

# Step 2: Develop locally
git clone https://github.com/mycompany/my-library.git
cd my-library

# Step 3: Push code
git push origin main

# Step 4: Create release
git tag -a v1.0.0 -m "Version 1.0.0"
git push origin v1.0.0

# Step 5: Submit to Packagist
# https://packagist.org/submit
# Enter: https://github.com/mycompany/my-library
# Click Submit

# Step 6: Users can now use
# composer require mycompany/my-library
```

### Example 2: Private Library

```bash
# Step 1: Create private repo on GitHub
# Settings > Private

# Step 2: Configure access
# Use SSH key for authentication
# Add to ssh-agent

# Step 3: Use in projects
# Configure VCS repository in composer.json

{
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:mycompany/private-lib.git"
        }
    ],
    "require": {
        "mycompany/private-lib": "dev-main"
    }
}

# Step 4: Install
composer install

# Note: NOT on Packagist (private)
```

### Example 3: Early Development Library

```bash
# Phase 0: Development
# Version 0.x
# GitHub only
# No Packagist submission yet

git push origin develop

# Phase 1: Release 1.0 (Stable)
# API finalized
# Tests complete
# Ready for public

git tag -a v1.0.0 -m "Version 1.0.0"
git push origin v1.0.0

# Phase 2: Submit to Packagist
# https://packagist.org/submit
# Now public users can discover and use

# Phase 3: Maintain
# Regular updates on GitHub
# Packagist auto-syncs via webhook
```

---

## Key Takeaways

**Platform Selection Checklist:**

1. ✅ GitHub = Always use for hosting
2. ✅ Packagist = Use for public, stable libraries
3. ✅ Private libraries = GitHub only
4. ✅ Public + unstable = GitHub only (wait for 1.0)
5. ✅ Public + stable = GitHub + Packagist
6. ✅ Set up webhook for automatic sync
7. ✅ Use semantic versioning for releases
8. ✅ Keep documentation in GitHub
9. ✅ Monitor statistics on Packagist
10. ✅ Maintain both platforms actively

---

## See Also

- [Uploading to GitHub](8-upload-own-library-to-github.md)
- [Submitting to Packagist](11-submit-own-library-to-packagist.md)
- [Upgrading Your Library](10-upgrade-own-library-version.md)
