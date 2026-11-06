# Font Awesome Free 6.5.1

Local copy of Font Awesome, used by the header, footer and social icons of astra-nodes and by the icon picker of the
Customizer. It replaces the copy loaded from cdnjs.cloudflare.com, so the front page doesn't connect to a third party
and the main font can be preloaded.

## Source

npm package `@fortawesome/fontawesome-free@6.5.1`
(https://registry.npmjs.org/@fortawesome/fontawesome-free/-/fontawesome-free-6.5.1.tgz), integrity
`sha512-CNy5vSwN3fsUStPRLX7fUYojyuzoEMSXPl7zSLJ8TgtRfjv24LOnOWKT2zYwaHZCJGkdyRnTmstR0P+Ah503Gw==`.
`css/all.min.css` is identical to https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css.

## Files

Without changes from the package:

- `css/all.min.css`: all the styles (solid, regular and brands are used: see `convert_header_icons()` in
  `agora-functions.php` and the social icons of the footer).
- `webfonts/`: the fonts referenced by `all.min.css` (woff2 and ttf).
- `LICENSE.txt`: icons CC BY 4.0, fonts SIL OFL 1.1, code MIT.

## Update

Replace the files with the ones of the new version, rename the directory and update the paths in
`astra-functions.php` and `astra-nodes/classes/WP_Customize_Font_Icon_Picker_Control.php`.
