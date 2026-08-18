#!/usr/bin/env bash
# Wrapper: reset integration DB (2moons_test) from inside the web container.
set -euo pipefail
php "$(dirname "$0")/reset_test_database.php"
