# Candidakuur copy style

- **No em dashes** (Unicode U+2014 / `—`). Use a comma, colon, or period instead.
- Page titles may use a middot (`·`) as separator.
- En dashes in numeric ranges (`1–2`) are OK when needed; prefer `1-2` if unsure.

Enforced by:

- `tests/Unit/NoEmDashesTest` (scans repo copy)
- `App\Support\WithoutEmDashes` on content models (strips on save, including Filament admin)
