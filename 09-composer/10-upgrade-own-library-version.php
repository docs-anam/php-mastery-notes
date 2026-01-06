<?php
/**
 *
 * Detailed Guide: Updating Your Own Library Version in GitHub (for Composer)
 *
 * 1. Make Code Changes:
 *    - Implement new features, bug fixes, or improvements in your library code.
 *    - Update documentation and tests as needed.
 *    - Adjust the "version" field in composer.json (optional; Packagist uses git tags for versioning).
 *
 * 2. Update CHANGELOG and Documentation:
 *    - Add a summary of changes in CHANGELOG.md.
 *    - Update README.md or other docs to reflect new features or changes.
 *
 * 3. Commit Changes Locally:
 *    - Stage all modified files:
 *      git add .
 *    - Commit with a descriptive message:
 *      git commit -m "Release v1.2.0: Added new feature X, fixed bug Y"
 *
 * 4. Tag a New Release:
 *    - Create a new annotated git tag matching your version:
 *      git tag -a v1.2.0 -m "Release v1.2.0"
 *    - Push commits and tags to GitHub:
 *      git push origin main
 *      git push origin v1.2.0
 *
 * 5. Create a GitHub Release (Recommended):
 *    - Go to your repository on GitHub.
 *    - Click "Releases" > "Draft a new release".
 *    - Select the new tag (e.g., v1.2.0).
 *    - Add release notes (copy from CHANGELOG.md).
 *    - Publish the release.
 *
 * 6. Update Packagist (if published):
 *    - If your library is registered on Packagist, it will auto-detect new tags.
 *    - If not, log in to Packagist and click "Update" on your package page.
 *
 * 7. Update Dependent Projects:
 *    - In projects using your library, update the version constraint in composer.json:
 *      "vendor/library-name": "^1.2"
 *    - If you use a specific version, change it to the new version:
 *      "require": {
 *          "monolog/monolog": "^3.9",
 *          "mukhoiran/own-library": "3.0.0"
 *      },
 *    - Or, run Composer to fetch the new version:
 *      composer update vendor/library-name
 *
 * 8. Best Practices:
 *    - Use semantic versioning: MAJOR.MINOR.PATCH.
 *    - Document all changes in CHANGELOG.md.
 *    - Test your library before tagging a release.
 *    - Use GitHub Releases for visibility and release notes.
 *    - Consider setting up CI/CD for automated testing and deployment.
 */

require __DIR__ . '/vendor/autoload.php';

$people = new \Mukhoiran\OwnLibrary\People("Anam");
echo $people->sayHello("Khoirul") . PHP_EOL;
echo $people->sayHello() . PHP_EOL; // Output: Hello Khoirul, my name is Khoirul Anam!