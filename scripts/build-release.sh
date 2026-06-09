#!/usr/bin/env bash
# Build a production-ready zip of a Zippy plugin.
#
# Output: dist/<slug>-<version>.zip
#   - Contains a single top-level folder `<slug>/` so it unpacks
#     correctly into wp-content/plugins/<slug>/.
#   - Excludes node_modules, source assets, dev tooling, tests, docs,
#     git metadata, and the dist folder itself.
#
# Usage:
#   ./scripts/build-release.sh                       # version from <slug>.php; slug = parent dir name
#   ./scripts/build-release.sh 1.2.3                 # explicit version
#   ./scripts/build-release.sh 1.2.3 --slug other    # explicit slug (when not running from the plugin dir name)
#
# Requires: node, yarn, zip, rsync.

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PLUGIN_DIR="$(cd "$SCRIPT_DIR/.." && pwd)"

# --- Parse args -------------------------------------------------------------
# Positional: first non-flag arg is VERSION.
# Flag:       --slug <name>  override the plugin slug (otherwise derived
#                            from the plugin directory name, which matches
#                            the WP convention <slug>/<slug>.php).
VERSION=""
PLUGIN_SLUG=""
while [[ $# -gt 0 ]]; do
	case "$1" in
		--slug)
			PLUGIN_SLUG="${2:-}"
			shift 2
			;;
		--slug=*)
			PLUGIN_SLUG="${1#--slug=}"
			shift
			;;
		*)
			if [[ -z "$VERSION" ]]; then
				VERSION="$1"
			else
				echo "ERROR: unexpected argument: $1" >&2
				exit 1
			fi
			shift
			;;
	esac
done

if [[ -z "$PLUGIN_SLUG" ]]; then
	PLUGIN_SLUG="$(basename "$PLUGIN_DIR")"
fi

cd "$PLUGIN_DIR"

# --- Resolve version --------------------------------------------------------
if [[ -z "$VERSION" ]]; then
	# Plugin header lives in <slug>.php. Match both docblock style
	# ("* Version: x") and bare style ("Version: x").
	VERSION="$(grep -E '^[[:space:]]*\*?[[:space:]]*Version:' "${PLUGIN_SLUG}.php" \
		| head -1 | sed -E 's/.*Version:[[:space:]]*([0-9A-Za-z._-]+).*/\1/')"
fi

if [[ -z "$VERSION" ]]; then
	echo "ERROR: could not resolve plugin version." >&2
	exit 1
fi

echo "==> Building $PLUGIN_SLUG v$VERSION"

# --- Sanity checks ----------------------------------------------------------
for bin in node yarn zip rsync; do
	command -v "$bin" >/dev/null 2>&1 || {
		echo "ERROR: '$bin' is required but not installed." >&2
		exit 1
	}
done

# --- Build assets -----------------------------------------------------------
echo "==> Installing yarn dependencies (frozen)"
yarn install --frozen-lockfile

echo "==> Running production build"
yarn dist

if [[ ! -d "assets/dist" ]]; then
	echo "ERROR: assets/dist not found after build." >&2
	exit 1
fi

# --- Stage release tree -----------------------------------------------------
DIST_DIR="$PLUGIN_DIR/dist"
STAGE_PARENT="$DIST_DIR/_stage"
STAGE_DIR="$STAGE_PARENT/$PLUGIN_SLUG"
ZIP_PATH="$DIST_DIR/${PLUGIN_SLUG}-${VERSION}.zip"

rm -rf "$STAGE_PARENT" "$ZIP_PATH"
mkdir -p "$STAGE_DIR"

echo "==> Staging files into $STAGE_DIR"
# rsync handles excludes better than find/cp combos.
rsync -a \
	--exclude '.git/' \
	--exclude '.github/' \
	--exclude '.gitignore' \
	--exclude '.gitattributes' \
	--exclude '.claude/' \
	--exclude '.vscode/' \
	--exclude '.idea/' \
	--exclude '.DS_Store' \
	--exclude '/node_modules/' \
	`# vendor/ is intentionally tracked + shipped (Guzzle autoload is required at runtime).` \
	--exclude '/dist/' \
	--exclude '/tests/' \
	`# Keep docs/guide/** (the in-product Documentation page reads from it)` \
	`# but strip the dev-facing top-level markdown + api/ + charts/ + database/.` \
	`# rsync rule: include parents BEFORE excluding siblings.` \
	--include '/docs/' \
	--include '/docs/guide/' \
	--include '/docs/guide/**' \
	--exclude '/docs/*' \
	--exclude '/scripts/' \
	--exclude '/assets/src/' \
	--exclude 'CLAUDE.md' \
	--exclude 'README.md' \
	--exclude 'package.json' \
	--exclude 'package-lock.json' \
	--exclude 'vite.config.js' \
	--exclude 'tailwind.config.js' \
	--exclude 'postcss.config.js' \
	--exclude 'composer.json' \
	--exclude 'composer.lock' \
	--exclude 'phpunit.xml' \
	--exclude 'phpcs.xml' \
	--exclude 'webpack.config.js' \
	--exclude 'yarn.lock' \
	--exclude '*.log' \
	./ "$STAGE_DIR/"

# --- Sanity-check the staged tree -------------------------------------------
if [[ ! -f "$STAGE_DIR/${PLUGIN_SLUG}.php" ]]; then
	echo "ERROR: ${PLUGIN_SLUG}.php missing from stage." >&2
	exit 1
fi
if [[ ! -d "$STAGE_DIR/assets/dist" ]]; then
	echo "ERROR: assets/dist missing from stage." >&2
	exit 1
fi
if [[ ! -d "$STAGE_DIR/src" ]]; then
	echo "ERROR: src/ missing from stage." >&2
	exit 1
fi
# Guzzle autoload (vendor/autoload.php) is required at runtime — the
# Guzzle/dompdf classes are not available otherwise and API calls fail.
if [[ ! -f "$STAGE_DIR/vendor/autoload.php" ]]; then
	echo "ERROR: vendor/autoload.php missing from stage." >&2
	exit 1
fi
# Some plugins ship an in-product Documentation page reading from
# docs/guide/*.md. When present, ensure it has content; if the plugin
# doesn't use that feature, skip the check.
if [[ -d "$STAGE_DIR/docs/guide" ]]; then
	if [[ -z "$(ls "$STAGE_DIR/docs/guide/"*.md 2>/dev/null)" ]]; then
		echo "ERROR: docs/guide/ has no .md files in stage." >&2
		exit 1
	fi
fi

# --- Zip --------------------------------------------------------------------
echo "==> Creating $ZIP_PATH"
( cd "$STAGE_PARENT" && zip -rq "$ZIP_PATH" "$PLUGIN_SLUG" )

rm -rf "$STAGE_PARENT"

SIZE="$(du -h "$ZIP_PATH" | cut -f1)"
echo
echo "Done."
echo "  File: $ZIP_PATH"
echo "  Size: $SIZE"
echo
echo "Install on the production site:"
echo "  WP Admin -> Plugins -> Add New -> Upload Plugin -> choose the zip"
