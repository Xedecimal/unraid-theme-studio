# UNRAID Theme Studio

A native UNRAID 7.2+ plugin for visually creating webGUI themes. Pick colors,
see a dashboard preview immediately, check text contrast, then apply the theme
without editing CSS by hand.

## Development disclosure

AI coding tools helped design, write, and review this project. I chose the
requirements and remain responsible for the published code.

## What is included

- Live dashboard preview with desktop and mobile sizes
- Color controls for the page, panels, text, secondary text, borders, header,
  links/accent, success, warning, and danger states
- Corner-radius control for dashboard sections, title bars, data tables,
  dialogs, and fields while preserving Unraid's native button styling
- Master enable/disable switch that preserves the saved palette while removing
  all generated Theme Studio overrides
- Advanced effects for panel transparency, accent gradients and glow, plus an
  optional reduced-motion-aware animated background
- Twelve built-in presets—including Dracula, Catppuccin Mocha, Oblivion, Nord,
  Tokyo Night, Gruvbox, Solarized, Everforest, Rosé Pine, and Plex Inspired—plus
  undo/redo, JSON import/export, and CSS export
- Server-side validation and CSRF-protected saves
- Persistent configuration at `/boot/config/plugins/theme.studio/`
- A generated override stylesheet loaded after UNRAID's stock theme
- Consistent Dynamix tables and components across Docker, Plugins, and third-party pages
- Native Settings page and header shortcut; no Docker socket or extra port

## Try the editor locally

Serve the repository root with any static server and open `/preview/`:

```bash
python -m http.server 8080
```

The preview uses a mock save adapter and does not touch an UNRAID server.

## Build a plugin package

On Linux, WSL, or an UNRAID terminal:

```bash
chmod +x scripts/build.sh
GITHUB_REPOSITORY=rafzzzzzz/unraid-theme-studio ./scripts/build.sh
```

Artifacts are written to `dist/` and `archive/`. Publish the generated
`theme.studio.plg` and matching `archive/*.txz` together on `main`, since the
installer downloads its package from that branch. Use a new `VERSION` for every
published package.

Run `node scripts/test.mjs` and `php scripts/test.php` before building. The PHP
checks use temporary files and never touch an installed theme. Browser CSS
exports mirror the PHP generator. After changing its CSS, `node scripts/sync-css.mjs`
prints an `apply_patch` patch to update the browser copy; the tests reject drift.

For a manual test install, copy the `.txz` to an UNRAID server and run:

```bash
installpkg theme.studio-*.txz
```

Then open **Settings → User Preferences → Theme Studio**.

## Community Applications publication

The plugin wrapper is in `community-applications/theme-studio.xml` and currently
marks the plugin as beta. Its URLs point to this repository. The `PluginURL`
must exactly match the `pluginURL` in `theme.studio.plg`.

Before submitting to Community Applications:

- Keep the root `ca_profile.xml` repository profile and plugin wrapper current.
- Test a fresh installation, an update, a reboot, disabling and re-enabling the
  theme, and uninstalling on an Unraid server. Uninstall currently removes saved
  Theme Studio settings, so back them up before testing it.
- Check Dashboard, Main, Docker, Plugins, and Settings with dark and light
  presets, including a narrow browser window. Check for conflicts with other
  custom CSS plugins.
- Run Validate and Scan in the submission portal and submit for review.

Follow the current [submission guide](https://ca.unraid.net/submit/help).
Passing the local tests does not establish full Unraid compatibility or CA approval.

## Development layout

- `source/theme.studio/` — files packaged onto the UNRAID root filesystem
- `preview/` — standalone browser test harness
- `scripts/build.sh` — creates the Slackware `.txz`, checksum, and `.plg`
- `plugin/theme.studio.plg.in` — install manifest template
- `community-applications/` — CA plugin listing
- `ca_profile.xml` — CA repository profile

## Compatibility note

The plugin targets UNRAID 7.2 and later. It uses the public CSS custom properties
present in the 7.2 webGUI and an ordinary `Buttons` page stylesheet, so no stock
UNRAID files are patched. A future webGUI release may rename individual CSS
variables; the editor's saved palette remains portable and can be regenerated.
