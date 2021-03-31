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

# Use Cases
## WordPress

### (Optional) Install `phpcs` globally
This package already requires `phpcs` but you might want to configure it globally. To require it globally, do:
```
composer global require squizlabs/php_codesniffer
```

To make sure you're using the global package, use:
```
> which phpcs
/Users/26b/.composer/vendor/bin/phpcs
```

### Setup WordPress Standards
First we need to clone the WordPress standards repository. It should be placed in a directory that `phpcs` can access. We placed ours in a `wpcs` directory in root. Clone the repository into the `wpcs` folder via:
```
git clone git@github.com:WordPress/WordPress-Coding-Standards.git --branch 2.3.0 wpcs
```

Secondly, we need to tell `phpcs` where these standards are.
```
phpcs --config-set installed_paths /full/path/to/wpcs
```

Finally, in order to check that `phpcs` recognises and uses the standards, we can check it like this:
```
> phpcs -i
The installed coding standards are PEAR, Zend, PSR2, Squiz, PSR1, PSR12, WordPress, WordPress-Extra, WordPress-Docs and WordPress-Core
```

The output should resemble this, with the WordPress standards. If they are missing, `phpcs` might not be recognising the path. Check its paths via:
```
> phpcs --config-show
Using config file: /full/path/to/global/composer/vendor/squizlabs/php_codesniffer/CodeSniffer.conf

default_standard: WordPress-Extra
installed_paths:  /full/path/to/wpcs
```
