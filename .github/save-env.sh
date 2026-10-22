#!/bin/sh
# Preserve the production .env across a GitHub Actions checkout.
#
# WHY
# ---
# .env is gitignored, and actions/checkout does a clean checkout
# (git reset --hard + git clean -ffdx). The compose file needs it:
#
#   env_file:
#     - .env
#   volumes:
#     - ./.env:/var/www/.env
#
# Without it the deploy fails at "docker compose up" with a missing-file
# error, and even if it did not, the app would start with the wrong
# database credentials.
#
# So before the checkout the file is copied to a location outside the
# repository, and after the checkout it is copied back. Nothing about the
# secret is logged, and the copy keeps the original permissions.
#
# Usage:
#   save-env.sh save    # copy .env aside
#   save-env.sh restore # copy it back
#   save-env.sh path    # print where it is stashed
set -u

REPO_DIR="${REPO_DIR:-$(pwd)}"
STASH_DIR="${STASH_DIR:-/home/opc/.ci-env-stash}"
STASH="$STASH_DIR/.env"

case "${1:-}" in
  save)
    if [ ! -f "$REPO_DIR/.env" ]; then
      echo "no .env in $REPO_DIR - nothing to save" >&2
      exit 1
    fi
    mkdir -p "$STASH_DIR"
    chmod 700 "$STASH_DIR"
    cp -p "$REPO_DIR/.env" "$STASH"
    chmod 600 "$STASH"
    echo "saved .env ($(wc -c < "$STASH") bytes) to $STASH"
    ;;

  restore)
    if [ ! -f "$STASH" ]; then
      echo "no stashed .env at $STASH" >&2
      exit 1
    fi
    cp -p "$STASH" "$REPO_DIR/.env"
    chmod 600 "$REPO_DIR/.env"
    echo "restored .env into $REPO_DIR"
    ;;

  path)
    echo "$STASH"
    ;;

  *)
    echo "usage: $0 {save|restore|path}" >&2
    exit 2
    ;;
esac
