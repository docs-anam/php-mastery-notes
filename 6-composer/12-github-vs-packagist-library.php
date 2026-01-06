<?php

/**
 * Note: Difference Between Using a GitHub Library and a Packagist Library in composer.json
 *
 * - Using a Packagist Library:
 *     - Simply require the package by its name:
 *         "require": {
 *             "vendor/package": "^1.0"
 *         }
 *     - Composer automatically downloads from Packagist.
 *
 * - Using a GitHub Library (not published on Packagist):
 *     - You must specify the repository in "repositories":
 *         "repositories": [
 *             {
 *                 "type": "vcs",
 *                 "url": "https://github.com/username/repository"
 *             }
 *         ]
 *     - Then require the package by its name:
 *         "require": {
 *             "vendor/package": "dev-main"
 *         }
 *     - Composer fetches directly from GitHub.
 *
 * Summary:
 * - Packagist libraries are easier to use and update, requiring only the package name.
 * - GitHub libraries need manual repository configuration in composer.json.
 */