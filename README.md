# php-pre-commit

Pre-commit git hook for enforcing php standards.

Currently uses PHP_CodeSniffer and corrects .php staged files according to a phpcs config file (usually a phpcs.xml).

This package **does not require** PHP_CodeSniffer because it is up to you whether you want to use a local or global `phpcs`. Local `phpcs` takes precedence when the pre-commit runs. You can require them in the following way:

```bash
# Local
composer require --dev squizlabs/php_codesniffer
# Or global
composer require global squizlabs/php_codesniffer
```

Install via the composer command:

```bash
composer require --dev 26b/php-pre-commit
```

In order for the hooks to be moved into the `.git/hooks` folder add the following to the project's `composer.json`:

```json
"scripts": {
    "post-install-cmd": "php-pre-commit",
    "post-update-cmd": "php-pre-commit"
}
```

With `composer install` or `composer update` the `pre-commit` hook will be moved into `.git/hooks`.

If you want to skip the pre-commit execution, you can add the argument `--no-verify` to `git commit`.

## Use Cases

### WordPress

#### Setup WordPress Standards (Manual)

First we need to clone the WordPress standards repository. It should be placed in a directory that `phpcs` can access. We placed ours in a `wpcs` directory in root. Clone the repository into the `wpcs` folder via:

```bash
git clone git@github.com:WordPress/WordPress-Coding-Standards.git --branch 2.3.0 wpcs
```

Secondly, we need to tell `phpcs` where these standards are.

```bash
phpcs --config-set installed_paths /full/path/to/wpcs
```

Finally, in order to check that `phpcs` recognises and uses the standards, we can check it like this:

```bash
$ phpcs -i
The installed coding standards are PEAR, Zend, PSR2, Squiz, PSR1, PSR12, WordPress, WordPress-Extra, WordPress-Docs and WordPress-Core
```

The output should resemble this, with the WordPress standards. If they are missing, `phpcs` might not be recognising the path. Check its paths via:

```bash
$ phpcs --config-show
Using config file: /full/path/to/global/composer/vendor/squizlabs/php_codesniffer/CodeSniffer.conf

default_standard: WordPress-Extra
installed_paths:  /full/path/to/wpcs
```

#### Alternative (Automatic)

As an alternative you can use [PHP_CodeSniffer Standards Composer Installer Plugin](https://github.com/Dealerdirect/phpcodesniffer-composer-installer) which helps to automatically link the wanted standards to phpcs. Be aware that it requires `phpcs` locally and so our `pre-commit` hook will use that phpcs. We can then require this package and WPCS in the following manner:

```bash
composer require --dev dealerdirect/phpcodesniffer-composer-installer wp-coding-standards/wpcs
```

Or add them to the composer.json:

```json
"require-dev": {
    "dealerdirect/phpcodesniffer-composer-installer": "*",
    "wp-coding-standards/wpcs": "*"
}
```

And add scripts, to configure `phpcs` correctly upon `composer install`, like this:

```json
"scripts": {
    "install-codestandards": [
        "Dealerdirect\\Composer\\Plugin\\Installers\\PHPCodeSniffer\\Plugin::run"
    ],
    "post-install-cmd": [
        "php-pre-commit",
        "@install-codestandards"
    ]
}
```

## Troubleshooting

- If the script is not executable, run the following, where the path is to the composer executable. (If installed globally it should be in `~/.composer/vendor/bin`, otherwise it's in the folder that contains `composer.json`.)

    ```bash
    chmod +x vendor/bin/php-pre-commit
    ```
