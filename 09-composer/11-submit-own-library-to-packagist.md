# Submitting Your Library to Packagist

## Overview

Packagist is the central repository for PHP packages. Submitting your library makes it easily discoverable and installable via Composer. This chapter covers how to register on Packagist, submit your library, and manage it on the platform.

---

## Table of Contents

1. What is Packagist
2. Creating a Packagist Account
3. Preparing Your Library
4. Submitting Your Library
5. Library Visibility
6. Managing Your Library
7. Monitoring Downloads
8. Troubleshooting
9. Complete Examples

---

## What is Packagist

### Packagist Role

```
Packagist = Central PHP Package Registry

Functions:
1. Package registry
   - Browse all PHP packages
   - Search packages
   - View documentation

2. Discovery
   - Find packages by keywords
   - Review ratings and stats
   - Check maintenance status

3. Installation
   - Composer automatically finds packages
   - Downloads from mirror
   - Resolves dependencies

4. Statistics
   - Download counts
   - Trending packages
   - Popular packages
```

### Before vs After Packagist

```
Before Packagist:
- Register with VCS only
- composer require git@github.com:user/lib.git
- Only friends know about library
- Hard to discover
- Manual dependency resolution

After Packagist:
- Anyone can find via packagist.org
- composer require vendor/package
- Discoverable and popular
- Easy dependency resolution
- Community engagement
```

---

## Creating a Packagist Account

### Sign Up

```
1. Go to https://packagist.org
2. Click "Sign Up"
3. Enter email and password
4. Verify email address
5. Complete registration

Or use GitHub login:
- Click "Sign in with GitHub"
- Authorize Packagist
- Account created automatically
```

### Profile Setup

```
Account > Settings:
- Username
- Email
- Avatar
- Bio
- Company
- Location
- Website

Recommended:
- Use GitHub username for consistency
- Add professional photo
- Write meaningful bio
```

### API Token

```
Account > API Token:

- Used for submitting packages
- Keep secret (like password)
- Can regenerate if compromised

Usage:
composer config repositories.packagist composer \
    https://repo.packagist.org

composer config http-basic.repo.packagist.org \
    token \
    your_packagist_token
```

---

## Preparing Your Library

### Checklist Before Submission

```
Code Quality:
✓ Follows PSR-12 code style
✓ Has unit tests
✓ All tests passing
✓ ~80%+ code coverage
✓ No obvious bugs

Documentation:
✓ Comprehensive README.md
✓ Installation instructions
✓ Usage examples
✓ API documentation
✓ License file

Configuration:
✓ composer.json complete
✓ Valid package name
✓ License specified
✓ Keywords added
✓ Homepage URL correct

Repository:
✓ On GitHub (or GitLab, etc.)
✓ Public repository
✓ README in root
✓ composer.json in root
✓ Proper git tags (v1.0.0)

Release:
✓ Version 1.0.0 released
✓ Stable and tested
✓ Not v0.x pre-release
```

### Example composer.json

```json
{
    "name": "mycompany/my-library",
    "description": "A reusable PHP library for slug generation",
    "type": "library",
    "license": "MIT",
    "authors": [
        {
            "name": "John Doe",
            "email": "john@example.com",
            "homepage": "https://johndoe.com"
        }
    ],
    "require": {
        "php": ">=8.0"
    },
    "require-dev": {
        "phpunit/phpunit": "^10.0"
    },
    "autoload": {
        "psr-4": {
            "MyCompany\\MyLibrary\\": "src/"
        }
    },
    "keywords": [
        "slug",
        "url",
        "generator",
        "utilities"
    ],
    "homepage": "https://github.com/mycompany/my-library",
    "support": {
        "issues": "https://github.com/mycompany/my-library/issues",
        "source": "https://github.com/mycompany/my-library"
    }
}
```

### Example README.md

```markdown
# My Library

Short description of the library.

[![Build Status](badge-url)](link)
[![Latest Stable Version](badge-url)](link)
[![License](badge-url)](link)

## Features

- Feature 1
- Feature 2
- Feature 3

## Installation

composer require mycompany/my-library

## Quick Start

```php
<?php
require 'vendor/autoload.php';

$lib = new MyCompany\MyLibrary\MyClass();
$result = $lib->doSomething();
```

## Usage

Detailed examples...

## API Reference

Class and method documentation...

## Testing

How to run tests...

## License

MIT License

## Support

Issues: GitHub Issues
```

---

## Submitting Your Library

### Method 1: Automatic (Recommended)

```
1. Go to https://packagist.org/submit
2. Enter GitHub URL:
   https://github.com/mycompany/my-library
3. Click "Submit"
4. GitHub webhook created automatically
5. Your library appears on Packagist

Now:
- Automatic updates on GitHub push
- composer require works
- Appears in search results
```

### Method 2: Manual Submission

```
1. Go to https://packagist.org
2. Click "Submit Package"
3. Enter repository URL
4. Manual refresh needed on updates

Less convenient - use automatic method
```

### What Happens

```
Submission Process:

1. Parse composer.json from repository
2. Validate format and requirements
3. Extract package name
4. Extract version (from git tags)
5. Index package
6. Make searchable
7. Available via Composer

On GitHub Push:
1. Webhook triggered
2. Packagist notified
3. composer.json re-parsed
4. New versions indexed
5. Updates available
```

---

## Library Visibility

### Public vs Private

```
Public Library:
- Indexed on Packagist
- Searchable by anyone
- Anyone can require
- Available via composer

Private Library:
- Not on Packagist
- Use VCS repository
- composer require git@github.com:...
- Limited access

For open-source: Use Public (Packagist)
```

### Making Library Public

```
If submitted but want to remove:

https://packagist.org/packages/mycompany/my-library
> Edit > Delete This Package

Or:
Account > Submitted Packages > Edit > Delete
```

### Removing from Packagist

```
Reasons to remove:
- Replaced by newer library
- No longer maintained
- Security issues (deprecate instead)
- Merge with another package

Better than removing:
- Deprecate with redirect
- Archive on GitHub
- Recommend alternative
```

---

## Managing Your Library

### Package Page

```
https://packagist.org/packages/mycompany/my-library

Shows:
- Description
- Installation command
- Latest version
- Download statistics
- GitHub link
- Documentation
- Requirements
- Maintainers
- Keywords
```

### Statistics

```
Dashboard shows:
- Total downloads
- Downloads last month
- Daily download trend
- Trending ranking
- Latest releases
```

### Versions

```
Package > Versions tab:

Shows all released versions:
- v1.0.0 (stable)
- v1.0.1 (stable)
- v1.1.0 (stable)
- v2.0.0-beta.1 (pre-release)

Composer uses versions to:
- Resolve dependencies
- Suggest updates
- Prevent conflicts
```

### Webhooks

```
GitHub > Settings > Webhooks:

Packagist webhook automatically created
Triggers when you push

If not auto-created:
1. Go to your repository
2. Settings > Webhooks
3. Add webhook:
   URL: https://packagist.org/api/update-package?username=TOKEN&apiToken=TOKEN
   Events: push
4. Test webhook
```

---

## Monitoring Downloads

### View Statistics

```
PackagistPage > Details tab:

- Total downloads (all time)
- Monthly downloads
- Daily downloads
- Trending chart
- Popular versions
```

### Understanding Downloads

```
High downloads = Popular package
- Trusted by many developers
- Well-maintained
- Solves real problem
- Good documentation

Track growth:
- Check stats regularly
- Monitor trends
- Read feedback
- Respond to issues
```

### Improving Discoverability

```
To increase visibility:

1. Optimize keywords
   - Relevant and specific
   - Search-friendly
   - 5-10 keywords

2. Good documentation
   - Clear README
   - Usage examples
   - API docs

3. Active maintenance
   - Respond to issues
   - Release updates
   - Fix security bugs

4. Quality code
   - Tests included
   - Code coverage
   - Follows standards

5. Marketing
   - Blog posts
   - Conference talks
   - Social media
```

---

## Troubleshooting

### Library Not Appearing

```
Problem: Submitted but not showing on Packagist

Causes:
1. Repository is private
   → Make public on GitHub

2. No git tags
   → Create tags: git tag v1.0.0

3. Invalid composer.json
   → Validate: composer validate

4. Webhook not triggered
   → Manually trigger in Packagist

5. Processing delay
   → Wait a few minutes (sometimes hours)

Solution:
1. Check package name is valid
2. Ensure composer.json is in root
3. Create git tags
4. Make repository public
5. Submit again
```

### Updating Not Working

```
Problem: Pushed new version, not on Packagist

Causes:
1. Webhook not configured
   → Set up GitHub webhook

2. Tag not pushed
   → git push origin v1.0.1

3. Package not synchronized
   → Packagist > Edit > Request Re-Crawl

Solution:
1. Verify webhook is active
2. Check git tags: git tag -l
3. Push tags: git push --tags
4. Wait for webhook to trigger
5. Manual update if needed
```

### Validation Errors

```
Problem: composer.json validation error

Check:
1. Valid JSON format
   → https://jsonlint.com/

2. Required fields:
   - name (vendor/package)
   - description
   - license

3. Composer validate
   → composer validate

4. Fix errors and re-submit
```

---

## Complete Examples

### Example 1: First Submission

```bash
# Prepare library
cd my-library/

# Create version 1.0.0
git tag -a v1.0.0 -m "Version 1.0.0: Initial release"
git push origin v1.0.0

# Verify on GitHub
# https://github.com/mycompany/my-library/releases/tag/v1.0.0

# Go to Packagist
# https://packagist.org/submit

# Enter URL
# https://github.com/mycompany/my-library

# Click Submit

# Wait for processing
# https://packagist.org/packages/mycompany/my-library

# Done!
```

### Example 2: Publishing Updates

```bash
# Make changes to library
# src/NewFeature.php added
# tests/NewFeatureTest.php added

# All tests pass
./vendor/bin/phpunit

# Update version
sed -i 's/"version": "1.0.0"/"version": "1.1.0"/' composer.json

# Update changelog
# ...

# Commit
git add -A
git commit -m "Version 1.1.0: New features"

# Create tag
git tag -a v1.1.0 -m "Version 1.1.0: New features"

# Push to GitHub
git push origin main
git push origin v1.1.0

# Packagist webhook triggers automatically
# https://packagist.org/packages/mycompany/my-library
# Shows v1.1.0 within minutes
```

### Example 3: Managing Public Package

```
Post-Submission Activities:

1. Monitor Issues
   - Respond to GitHub issues
   - Fix bugs
   - Answer questions

2. Update Documentation
   - Improve README based on feedback
   - Add FAQ section
   - Update examples

3. Release Updates
   - Patch releases (bug fixes)
   - Minor releases (features)
   - Major releases (breaking changes)

4. Community Engagement
   - Acknowledge contributors
   - Share updates on social media
   - Blog about library usage

5. Maintenance
   - Keep dependencies updated
   - Security patches
   - Compatibility with new PHP
```

---

## Key Takeaways

**Packagist Submission Checklist:**

1. ✅ Create Packagist account
2. ✅ Prepare composer.json (name, description, license)
3. ✅ Write comprehensive README.md
4. ✅ Add git tags (v1.0.0)
5. ✅ Make repository public
6. ✅ Go to packagist.org/submit
7. ✅ Enter GitHub repository URL
8. ✅ Click Submit
9. ✅ Verify package appears
10. ✅ Monitor download statistics

---

## See Also

- [Creating Your Own Library](7-create-own-library.md)
- [Uploading to GitHub](8-upload-own-library-to-github.md)
- [Upgrading Your Library](10-upgrade-own-library-version.md)
