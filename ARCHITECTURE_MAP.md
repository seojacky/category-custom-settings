# ARCHITECTURE MAP

## Entry Points
- `category-custom-settings.php` — sole plugin file; instantiates the class and defines all functions

## Functional Areas

- **Admin UI & Save**
  - `category-custom-settings.php` (class `Category_Custom_Settings_Plugin`)

- **Frontend API**
  - `category-custom-settings.php` (functions `ccs_get_category_field`, `ccs_the_category_field`, `ccs_get_code_allowed_html`)

## Directory Roles
- `/` (root) — entire plugin lives here; no subdirectories

## Safe Modification Rules
- Safe to change: field labels, CSS colours inside `admin_assets`, allowed HTML in `ccs_get_code_allowed_html`
- Do NOT touch without explicit request: nonce constants, capability checks, sanitization switch in `sanitize_field_value`

## Navigation Rules for AI Agent
- All tasks start in `category-custom-settings.php`
- `README.md` — human docs only, skip unless task is documentation
- `readme.txt` — WordPress.org metadata only, skip unless task is readme/version bump
- `ARCHITECTURE_MAP.md` — this file, never scan for code tasks
- `.gitattributes` — repo config, ignore for all code tasks
- Do NOT scan for additional PHP files; there are none
