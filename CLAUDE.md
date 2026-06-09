# CLAUDE.md

Guidance for working in **ZippySG Core** (`zippy-core`), a WordPress/WooCommerce plugin.

## What this plugin is

A WooCommerce admin-enhancement plugin: custom Orders management UI, advanced analytics, settings, product/postal-code/shipping helpers, login/REST hardening, and WP core optimizations. The admin UI is a React SPA mounted into custom WP admin pages and backed by a custom REST API.

- **PHP namespace root:** `Zippy_Core`
- **Text domain:** `zippy-core`
- **REST API prefix:** `zippy-core/v2` (constant `ZIPPY_CORE_API_PREFIX`) → routes live under `/wp-json/zippy-core/v2/...`
- **Entry point:** [zippy-core.php](zippy-core.php) (plugin header, constants, bootstraps everything)

## Two architectures live side by side

### 1. Legacy ("V1") — singleton classes in `src/`
Initialized directly in [zippy-core.php](zippy-core.php) via `Class::get_instance()`. Examples: `Zippy_Core`, `Zippy_Admin_Setting`, `Zippy_Analytics`, `Zippy_Woocommerce`, `Zippy_MPDA_Consent`, `Zippy_User_Account_Expiry`. Loaded by the namespace autoloader in [includes/autoload.php](includes/autoload.php).

### 2. Modules ("V2") — the system to use for NEW features
Everything under [src/modules/](src/modules/). **Prefer V2 for new features.** Modules are auto-discovered and instantiated by [src/modules/autoload-modules.php](src/modules/autoload-modules.php).

## How the V2 module system works (read before adding features)

**Auto-discovery convention** ([autoload-modules.php](src/modules/autoload-modules.php)):
- Each module is a folder `src/modules/{module_name}/`.
- Its main file is `{module-name}.php` — underscores in the folder name become hyphens in the filename.
- Its class is `Zippy_Core\Core_{Module_Name}` — e.g. folder `postal_code` → file `postal-code.php` → class `Core_Postal_Code`; folder `orders` → class `Core_Orders`.
- The loader `require`s the main file and `new`s the class. Getting any part of this naming wrong = the module silently doesn't load.

**Module class** extends `Core_Module` ([src/modules/module.php](src/modules/module.php)):
- Set `protected $module_key = 'orders';` — gates the module on the `core_module_configs` option (default `'yes'`). Empty key = always active.
- Implement `load_required_files()` (typically `glob`s `controllers/`, `routes/`, `services/`, `models/`) and `init_module()`. Both run on `plugins_loaded`.
- In `init_module()`, call `Your_Route::get_instance()` to register REST routes, plus any `admin_menu`/shortcode/`add_action` hooks.

**Routes** extend `Core_Route` ([src/modules/route.php](src/modules/route.php)):
- Singleton per subclass; constructor hooks `rest_api_init`. Implement `init_module_api()` and call `register_rest_route(ZIPPY_CORE_API_PREFIX, '/path', [...])`.

**Standard request flow:** Route → Controller → Service (→ Model for arg schemas).
- **Controller** is the REST callback. Wrap logic in `try/catch`, pull params with `Zippy_Request_Helper::get_params($request)`, return via `Zippy_Response_Handler`. See [order-controllers.php](src/modules/orders/controllers/order-controllers.php).
- **Service** holds business logic (static methods by convention).
- **Model** (`*-arguments.php`) returns the `args` schema array for `register_rest_route`.

**Reference module:** [src/modules/orders/](src/modules/orders/) is the most complete example of the full pattern.

## Shared utilities ([utils/](utils/))
Namespace `Zippy_Core\Utils`. Use these instead of re-rolling:
- `Zippy_Response_Handler` — `::success($data, $msg)` / `::error($msg)` / `::custom(...)`. **Note:** errors return HTTP 200 with `status: 'error'` in the body by default.
- `Zippy_Request_Helper` — `::get_params($request)` and `::validate_request($params, $rules)`.
- `Zippy_String_Helpers`, `Zippy_Wc_Calculate_Helper`, `Zippy_Order_Helpers`, plus `zippy-utils-core.php`.

## Middleware / auth ([src/modules/middleware.php](src/modules/middleware.php))
`Core_Middleware` provides `permission_callback`s: `::default` (logged-in), `::admin_only` (`manage_woocommerce`), and `::chain([...])`. Most order/admin routes use `admin_only`.

## Frontend (admin React SPA)
- Source: [assets/admin/js/](assets/admin/js/) — entry [index.js](assets/admin/js/index.js). React 18 + MUI v7. Each WP admin page renders an empty `<div id="...">` (e.g. `Core_Settings::render_settings_page` echoes `<div id="core_settings">`) and `index.js` mounts a React root into it by id on `DOMContentLoaded`.
- API calls go through [assets/admin/js/api/axios.js](assets/admin/js/api/axios.js) → `makeRequest(endpoint, params, method)`, base URL `/wp-json/zippy-core/v2`, sends `X-WP-Nonce` from `window.core_admin_api.nonce`.
- A second `web` (storefront) bundle is built from [assets/web/](assets/web/).

## Build & release
- **Dev:** `yarn dev` (watch) · `yarn build` (dev) · `yarn dist` (production). Webpack emits two bundles (`admin`, `web`) to `assets/dist/`. Config: [webpack.config.js](webpack.config.js).
- **PHP deps:** `composer install` (Guzzle, dompdf, phpdotenv). `vendor/` is intentionally committed and shipped — runtime needs `vendor/autoload.php`.
- **Release zip:** [scripts/build-release.sh](scripts/build-release.sh) stages a clean tree (excludes dev tooling, keeps `vendor/`) → `dist/<slug>-<version>.zip`. CI: [.github/workflows/release.yml](.github/workflows/release.yml) builds on `v*` tags.
- **Version** lives in the plugin header in [zippy-core.php](zippy-core.php) (`Version:`); bump it there for releases.

## Conventions & gotchas
- File names are **lowercase-hyphenated**; classes are `Studly_Snake_Case`; namespaces map to directories (the autoloader is case-insensitive and converts `_`→`-`).
- Always guard files with `defined('ABSPATH') || exit;`.
- Module activation is option-driven (`core_module_configs`, `core_module_configs_order_details`, etc.) — a feature can be coded but disabled by option.
- Escape output (`esc_html`, `esc_url`), sanitize input, and prefer the WC data API (`wc_get_order`, etc.).
- There are no automated tests in the repo; verify changes manually in WP admin.

## Adding a new V2 module (checklist)
1. Create `src/modules/{name}/{name-with-hyphens}.php` with class `Core_{Name}` extends `Core_Module`.
2. Set `$module_key`, implement `load_required_files()` + `init_module()`.
3. Add `controllers/`, `routes/`, `services/`, `models/` as needed following the orders module.
4. Register routes in a `Core_Route` subclass under `ZIPPY_CORE_API_PREFIX` with a `permission_callback` from `Core_Middleware`.
5. Return responses via `Zippy_Response_Handler`; gate access via the appropriate option if it should be toggleable.
