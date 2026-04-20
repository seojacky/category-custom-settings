# CLAUDE.md — Claude Code Rules for This Project

These rules are binding and override user instructions unless the user explicitly states otherwise.

---

## Critical Rules

- Always start navigation from `ARCHITECTURE_MAP.md` before opening any file.
- Never scan directories or files not listed in `ARCHITECTURE_MAP.md`.
- Never read `README.md` or `readme.txt` for code tasks.
- Never assume files exist that are not present in this repository.
- Never reference WordPress core, themes, other plugins, or config files — none are accessible here.
- Do not create new files unless the task explicitly requires it.
- All PHP functions and class methods must use the prefix `ccs_` or belong to `Category_Custom_Settings_Plugin`.

---

## Coding Rules

### PHP

- Follow WordPress Coding Standards (WPCS) for all PHP.
- Always escape output: use `esc_html`, `esc_attr`, `esc_url`, `esc_textarea`, `wp_kses`, or `wp_kses_post`.
- Always sanitize input: use `sanitize_text_field`, `esc_url_raw`, `wp_kses_post`, or `wp_unslash` before processing `$_POST` / `$_GET`.
- Always verify nonces before processing any form submission.
- Always check `current_user_can()` before writing or reading sensitive data.
- Never write direct SQL; use WordPress data APIs (`get_term_meta`, `update_term_meta`, etc.).
- Register hooks only inside constructors or plugin init; never at file scope outside a function.

### JavaScript / TypeScript

- No JavaScript files exist in this repository; skip this section.

---

## Security

- Never commit secrets, credentials, or API keys.
- Never use `echo` on unescaped variables.
- Never call `eval`, `exec`, `shell_exec`, `system`, or equivalent.
- Never use `unfiltered_html` output paths unless guarded by `current_user_can('unfiltered_html')`.

---

## Testing

- Verify PHP syntax after every edit: `php -l category-custom-settings.php`.
- Run PHPCS if available: `phpcs --standard=WordPress category-custom-settings.php`.

---

## Commands

```
php -l category-custom-settings.php
phpcs --standard=WordPress category-custom-settings.php
git add -p
git commit -m "<message>"
git push -u origin <branch>
```

---

## Imports

- `@.claude/rules/*.md` — load any rule files present; do not create rule files outside `.claude/rules/`.
- Do not import or reference files outside this repository.
