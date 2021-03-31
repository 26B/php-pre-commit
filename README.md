# php-pre-commit
Pre-commit git hook for enforcing php standards.

Currently uses PHP_CodeSniffer and corrects .php staged files according to a phpcs config file (usually a phpcs.xml).

Install via the composer command:
```
composer require --dev 26b/php-pre-commit
```

In order for the hooks to be moved into the `.git/hooks` folder add the following to the project's `composer.json`:
```
"scripts": {
    "post-install-cmd": "php-pre-commit",
    "post-update-cmd": "php-pre-commit"
}
```

You will also need to update the execute access of the bin script:
```
chmod +x vendor/bin/php-pre-commit
```

With `composer install` or `composer update` the `pre-commit` hook will be moved into `.git/hooks`.
