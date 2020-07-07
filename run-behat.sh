#!/usr/bin/env bash
set -eux

USE_THEME=${THEME:-false}

if [[ "$USE_THEME" = true ]]; then
	vendor/bin/behat --tags=@theme_setup --strict -vvv --no-interaction "$@"|| vendor/bin/behat --tags=@theme_setup --strict -vvv --no-interaction --rerun "$@"
	(cd tests/Application && bin/console ca:cl)
	vendor/bin/behat --tags="@theme" --strict -vvv --no-interaction "$@" || vendor/bin/behat --tags="@theme" --strict -vvv --no-interaction --rerun "$@"
else
	vendor/bin/behat --tags="~@theme&&~@theme_setup" --strict -vvv --no-interaction "$@" || vendor/bin/behat --tags="~@theme&&~@theme_setup" --strict -vvv --no-interaction --rerun "$@"
fi
