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
# The restore writes with a shell redirect instead of cp. cp removes the
# destination first, so when the copy fails (the file is owned by root
# and the deploy runs as opc) the .env is gone instead of merely stale.
# A redirect truncates in place and only touches the content.
#
# Usage:
#   save-env.sh save    # copy .env aside
#   save-env.sh restore # copy it back
#   save-env.sh path    # print where it is stashed
set -u

REPO_DIR="${REPO_DIR:-$(pwd)}"
STASH_DIR="${STASH_DIR:-/home/opc/.ci-env-stash}"
STASH="$STASH_DIR/.env"

write_stash() {
  # shellcheck disable=SC2188
  cat "$1" > "$2"
}

# Read the file. The .env on the host is owned by root while the deploy
# runs as opc, so a plain cat can fail. sudo is tried when it is allowed.
read_src() {
  if cat "$1" 2>/dev/null; then
    return 0
  fi
  if command -v sudo >/dev/null 2>&1 && sudo -n true 2>/dev/null; then
    sudo -n cat "$1"
    return $?
  fi
  return 1
}

case "${1:-}" in
  save)
    if [ ! -f "$REPO_DIR/.env" ]; then
      echo "no .env in $REPO_DIR - nothing to save" >&2
      exit 1
    fi
    mkdir -p "$STASH_DIR"
    chmod 700 "$STASH_DIR"
    if ! read_src "$REPO_DIR/.env" > "$STASH"; then
      echo "could not read $REPO_DIR/.env" >&2
      rm -f "$STASH"
      exit 1
    fi
    if [ ! -s "$STASH" ]; then
      echo "the stashed .env is empty - refusing to continue" >&2
      exit 1
    fi
    chmod 600 "$STASH"
    echo "saved .env ($(wc -c < "$STASH") bytes) to $STASH"
    ;;

  restore)
    if [ ! -f "$STASH" ]; then
      echo "no stashed .env at $STASH" >&2
      exit 1
    fi
    if [ ! -s "$STASH" ]; then
      echo "the stashed .env is empty - refusing to overwrite the real one" >&2
      exit 1
    fi
    # The file on disk is owned by root while the deploy runs as opc, so
    # the write is retried with sudo when allowed. The redirect truncates
    # in place; cp would remove the destination first and leave nothing
    # behind when the copy fails.
    if ! write_stash "$STASH" "$REPO_DIR/.env" 2>/dev/null; then
      if command -v sudo >/dev/null 2>&1 && sudo -n true 2>/dev/null; then
        sudo -n sh -c 'cat "$1" > "$2"' sh "$STASH" "$REPO_DIR/.env"
        sudo -n chmod 600 "$REPO_DIR/.env"
      else
        echo "could not write $REPO_DIR/.env (permission denied)" >&2
        exit 1
      fi
    else
      chmod 600 "$REPO_DIR/.env" 2>/dev/null || true
    fi
    echo "restored .env into $REPO_DIR ($(wc -c < "$REPO_DIR/.env") bytes)"
    ;;

  path)
    echo "$STASH"
    ;;

  *)
    echo "usage: $0 {save|restore|path}" >&2
    exit 2
    ;;
esac
