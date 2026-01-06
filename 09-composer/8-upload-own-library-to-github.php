<?php
/*
    Step-by-Step Guide: Uploading Your Own PHP Library to GitHub and Creating a Release Note

    1. Organize Your PHP Library:
        - Place all source code in a dedicated folder (e.g., "own-library/").
        - Include a "src/" directory for your PHP classes/functions.
        - Add a "composer.json" file with metadata (name, description, autoload, etc.).
        - Create a "README.md" file explaining usage, installation, and features.
        - Add a ".gitignore" file to exclude files/folders like "vendor/", ".DS_Store", "node_modules/", etc.

    2. Initialize a Local Git Repository:
        - Open your terminal/command prompt.
        - Navigate to your library folder:
            cd /path/to/own-library
        - Initialize git:
            git init
        - Add all files to staging:
            git add .
        - Commit your changes:
            git commit -m "Initial commit of my PHP library"

    3. Create a New Repository on GitHub:
        - Log in to https://github.com.
        - Click the "+" icon (top right) > "New repository".
        - Enter a repository name (e.g., "sayhello-library").
        - Optionally add a description.
        - Choose public or private visibility.
        - DO NOT initialize with README, .gitignore, or license (since you already have them locally).
        - Click "Create repository".

    4. Link Local Repository to GitHub and Push:
        - Copy the repository URL from GitHub (e.g., https://github.com/mukhoiran/sayhello-library.git).
        - In your terminal, add the remote:
            git remote add origin https://github.com/mukhoiran/sayhello-library.git
        - Set the main branch name (if needed):
            git branch -M main
        - Push your code to GitHub:
            git push -u origin main

    5. Create a Release Note on GitHub:
        - Go to your repository page on GitHub.
        - Click "Releases" (right sidebar or top menu).
        - Click "Draft a new release".
        - Set a tag version (e.g., v1.0.0). If this is your first release, use "v1.0.0".
        - Give the release a title (e.g., "Initial Release").
        - Write detailed release notes:
            - List new features, bug fixes, and breaking changes.
            - Provide installation instructions (e.g., via Composer).
            - Mention usage examples or documentation links.
        - Click "Publish release".

    6. (Optional) Publish to Packagist for Composer Installation:
        - Go to https://packagist.org and log in.
        - Click "Submit" and enter your GitHub repository URL.
        - Follow instructions to publish your library for Composer users.

    // Your PHP library is now available on GitHub, and users can view your release notes.
    // If published to Packagist, it can be installed via Composer.
*/
?>