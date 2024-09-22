#!/usr/bin/env bash

set -eu

REPO_ROOT_DIR=$(cd "$(dirname "$0")" && git rev-parse --show-toplevel)
declare -rx REPO_ROOT_DIR
declare -rx KTCMS_DIR="$REPO_ROOT_DIR/src"
