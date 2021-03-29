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
    "install-hooks": "sh ./vendor/26b/php-pre-commit/setup-hooks.sh",
    "post-install-cmd": "@install-hooks",
    "post-update-cmd": "@install-hooks"
}
```

With `composer install` or `composer update` the `pre-commit` hook will be moved into `.git/hooks`.
