# CapstoneRemodel

The REMODEL (REsident MilestOne-baseD Educational Learning) system is designed to
allow residents to identify preferences for scheduled surgical cases in which to participate.

Most of the information in this README (and more) is covered with additional detail in the Complete Transition Document, available in the REMODEL Google Drive.

REMODEL uses the LAMP (Linux, Apache, MySQL and PHP) stack.
It is hosted using [OSU's OCIO web hosting service](https://web.osu.edu),
uses the [Laravel PHP framework](https://laravel.com/docs/),
and authenticates users with [Shibboleth@OSU](https://webauth.service.ohio-state.edu/~shibboleth/).

REMODEL writes output to a Google Sheet using [Google's PHP API](https://developers.google.com/sheets/api/quickstart/php).

## Editor Setup

Since you won't run the application locally, it's technically sufficient to simply clone and edit files.
However, by following these instructions, your code editor should provide helpful completions.

- Clone this repository.
- Install [PHP](https://www.php.net/manual/en/install.php) (OCIO uses version 7.4. We reccomend that you match this.)
- Install [Composer](https://getcomposer.org) for managing dependencies.
- Install dependencies by running `composer install` within the `laravel` directory.
- Install [VS Code](https://code.visualstudio.com).
- Install the [Laravel Extension Pack](https://marketplace.visualstudio.com/items?itemName=onecentlin).
- Optionally, install [GitLens](https://marketplace.visualstudio.com/items?itemName=eamodio.gitlens) and [Git Graph](https://marketplace.visualstudio.com/items?itemName=mhutchie.git-graph) to enhance VS Code's `git` capabilities.

## Different Servers

REMODEL developers have access to three servers. Each server has its own databse.

1. Prod/Production - The publicly available version of the application in use by residents.
2. Dev/Development - The server where most development should occur. Use this server to develop and use new functionality before deploying.
3. Test/Testing - Similar to development. The database can be reset to create a controlled environment for testing.

## Run Tests

Tests are to be run only on the test server so they do not interfere with production or development data.

See Laravel's documentation on running tests [here](https://laravel.com/docs/8.x/testing#running-tests).

In short, run either

```bash
./vendor/bin/phpunit # run "bare" phpunit
```

or

```bash
php artisan test # run laravel's test runner
```

from within the `laravel` directory. You can navigate there with the following commands.

```bash
ssh lastname.number@webssh.osu.edu # connect to OCIO's servers via ssh
# enter password and select which server you want to connect to (in this case, test)

cdweb # change directory to the server's files
cd htsdocs/laravel # change directory to the laravel application
```

## Code Format

We formatted PHP files with [Prettier PHP Plugin](https://github.com/prettier/plugin-php)
and blade templates with [blade-formatter](https://www.npmjs.com/package/blade-formatter).

## Other

If you have a question that is not answered here, consult the Transition Documentation available in the REMODEL Google Drive.
