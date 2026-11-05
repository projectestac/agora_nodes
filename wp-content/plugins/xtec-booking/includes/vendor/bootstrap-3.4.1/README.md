# Bootstrap 3.4.1 for xtec-booking

Local copy of Bootstrap, used by the booking calendar (bootstrap-calendar). It replaces Bootstrap 3.3.7, which was
loaded from maxcdn.bootstrapcdn.com and has known XSS vulnerabilities in tooltip and collapse (CVE-2018-14040,
CVE-2018-14042, CVE-2018-20676, CVE-2019-8331).

## Source

npm package `bootstrap@3.4.1` (https://registry.npmjs.org/bootstrap/-/bootstrap-3.4.1.tgz), integrity
`sha512-yN5oZVmRCwe5aKwzRj6736nSmKDX7pLYwsXiCj/EYmo16hODaBiT4En5btW/jhBF/seV+XMx3aYwukYC3A49DA==`.

## Files

- `js/bootstrap-custom.js`: concatenation, without changes, of `js/transition.js`, `js/tooltip.js`, `js/modal.js` and
  `js/collapse.js` from the package, in this order. These are the only Bootstrap JS components used by the plugin.
  `button` and `carousel` are deliberately excluded: Bootstrap 3.4.1 still has known vulnerabilities in them
  (CVE-2024-6485, CVE-2024-6484), and `button` also conflicts with the jQuery UI button used in wp-admin.
- `css/bootstrap.min.css` and `css/bootstrap.min.css.map`: `dist/css/` from the package, without changes.
- `fonts/`: `dist/fonts/` from the package (Glyphicons), without changes. The calendar uses them in the month view
  (`glyphicon-chevron-down` in the box that opens the day events, created in `calendar.js`).
- `LICENSE`: MIT license of Bootstrap.

## Update

If more components are needed, add their files from `js/` of the package to `bootstrap-custom.js`, keeping
`transition.js` first, and update the version in `includes/calendar.php` (`xtec_booking_calendar_libraries()`).
