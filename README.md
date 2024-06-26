# php-pre-commit

Pre-commit git hook for enforcing php standards.

Currently uses PHP_CodeSniffer (PHPCS) and corrects .php staged files according to a phpcs config file (usually a phpcs.xml).

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

> [!IMPORTANT]
> This package **does not require** PHPCS because it is up to you whether you want to use a local or global `phpcs`. Local `phpcs` takes precedence when the pre-commit runs. You can require them in the following way:
>
> ```bash
> # Local
> composer require --dev squizlabs/php_codesniffer
> # Or global
> composer global require squizlabs/php_codesniffer
> ```

## Use Cases

### Local PHPCS

If you intend to use different sets of standards in different repositories we advise you to install `php_codesniffer` locally, this will make sure that there will be no conflicts when configuring `phpcs --config-set ...` later on. In case you choose the local approach make sure to correct the PHPCS path on the examples below.

Usually it would be something like changing all `phpcs` references to `./vendor/bin/phpcs` (or any other local path).

### WordPress

#### Setup WordPress Standards (Manual)

First we need to clone the WordPress standards repository. It should be placed in a directory that `phpcs` can access. We placed ours in the home directory `wpcs` directory in root. Clone the repository into the `wpcs` folder via:

```bash
git clone git@github.com:WordPress/WordPress-Coding-Standards.git --depth=1 --branch 3.1.0 ~/wpcs
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

### Laravel

#### Automatic Setup

Here, you can also use [PHP_CodeSniffer Standards Composer Installer Plugin](https://github.com/Dealerdirect/phpcodesniffer-composer-installer) to automatically link the Laravel standards to phpcs. Again, make sure `phpcs` is installed locally and so our `pre-commit`. We just need to require [emielmolenaar/phpcs-laravel](https://github.com/emielmolenaar/phpcs-laravel) package and we are done:

```bash
composer require --dev emielmolenaar/phpcs-laravel
```

Finally, add the same scripts to configure `phpcs` correctly upon `composer install`:

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

#### Manual Setup

A Laravel's coding standard repository that is being updated consistently is [emielmolenaar/phpcs-laravel](https://github.com/emielmolenaar/phpcs-laravel). We will now proceed to the configuration (very similar to the *WordPress configuration*). First clone [emielmolenaar/phpcs-laravel](https://github.com/emielmolenaar/phpcs-laravel) repository:

```bash
git clone https://github.com/emielmolenaar/phpcs-laravel.git --branch 2.0
```

Again, tell to `phpcs` where the standards are.

```bash
phpcs --config-set installed_paths /full/path/to/phpcs-laravel
```

Once more, make sure `phpcs` recognises and uses the installed standards:

```bash
$ phpcs -i
The installed coding standards are PEAR, Zend, PSR2, MySource, Squiz, PSR1, PSR12 and phpcs-laravel
```

The `--config-show` will give the following output:

```bash
$ phpcs --config-show
Using config file: /full/path/to/composer/vendor/squizlabs/php_codesniffer/CodeSniffer.conf

installed_paths: /full/path/to/phpcs-laravel
```

## Troubleshooting

- If the script is not executable, run the following, where the path is to the composer executable. (If installed globally it should be in `~/.composer/vendor/bin`, otherwise it's in the folder that contains `composer.json`.)

    ```bash
    chmod +x vendor/bin/php-pre-commit
    ```
