# Political Spectrum Bar

A WordPress plugin that adds a horizontal Liberal ↔ Conservative spectrum bar — arrows on each end, a positioned marker ball, and a filled pointer triangle below the ball. Build bars on a dedicated admin page and drop them into posts, pages, or widgets with a shortcode.

## Installation

1. Download `political-spectrum-bar.zip`.
2. In WordPress admin, go to **Plugins → Add New → Upload Plugin**.
3. Select the zip, install, and activate.

## Creating a bar

1. In the admin sidebar, click **Spectrum Bars** (the new menu with a left-right arrow icon).
2. Click **Add New Bar**.
3. Give it a title (for your own reference — not displayed on the front end).
4. Configure:
   - **Position** — use the slider or type a number from 0–100 (decimals allowed).
   - **Left label** / **Right label** — defaults to "Liberal" / "Conservative".
   - **Marker label** — optional text that appears under the pointer triangle (e.g. "You are here" or a candidate name).
   - **Bar colors** — use a blue→red gradient by default, or toggle it off for a solid color.
   - **Colors for** arrows, end labels, marker ball (fill + border), and pointer triangle.
   - **Show numeric scale** — optional 0 / 25 / 50 / 75 / 100 marks below the bar.
5. Click **Publish** (or **Update**).
6. Copy the shortcode from the sidebar meta box.

## Using the shortcode

Paste the shortcode into any post, page, widget, or template where shortcodes are processed:

```
[spectrum_bar id="123"]
```

### Inline overrides

Any saved setting can be overridden per-instance without editing the bar. Great for embedding the same bar in multiple places with different positions or labels.

```
[spectrum_bar id="123" position="72" pointer_label="Senator X"]
[spectrum_bar id="123" position="15" bar_gradient="0" bar_color="#333"]
[spectrum_bar id="123" left_label="Dove" right_label="Hawk" show_scale="1"]
```

**Available attributes:**

| Attribute | Type | Example |
|-----------|------|---------|
| `id` | integer (required) | `id="5"` |
| `position` | 0–100 | `position="42.5"` |
| `left_label` | text | `left_label="Progressive"` |
| `right_label` | text | `right_label="Traditional"` |
| `pointer_label` | text | `pointer_label="Me"` |
| `bar_gradient` | 0 or 1 | `bar_gradient="0"` |
| `bar_color` | hex | `bar_color="#222"` |
| `bar_color_left` | hex | `bar_color_left="#1E66F5"` |
| `bar_color_right` | hex | `bar_color_right="#D93025"` |
| `marker_color` | hex | `marker_color="#FFF"` |
| `marker_border` | hex | `marker_border="#000"` |
| `arrow_color` | hex | `arrow_color="#555"` |
| `label_color` | hex | `label_color="#1C1C1C"` |
| `pointer_color` | hex | `pointer_color="#C85A2E"` |
| `show_scale` | 0 or 1 | `show_scale="1"` |

## Features

- **Custom post type** — manage multiple bars from one admin screen; list view shows each bar's position and shortcode
- **Live preview** — rendered in the editor so you can see changes on save
- **Shared stylesheet** — admin preview matches the front-end exactly
- **Responsive** — adapts down to small phones (scales marker, bar height, and fonts)
- **Accessible** — marker has an `aria-label` announcing its position percentage
- **Fully self-contained** — no JavaScript required on the front end, no external dependencies
- **Clean uninstall-safe** — uses standard WordPress meta fields; removing the plugin doesn't leave orphaned custom tables

## License

GPL v2
