# Cookie Consent 1.0.10 (Silktide) - theme

Local copy of the theme `dark-bottom.css` used by `../cookie-consent.min.js` (Cookie Consent 1.0.10, MIT license,
https://silktide.com/cookieconsent). Before, the script loaded it from cdnjs.cloudflare.com. Now `agora-functions.php`
passes the full URL of this file in the option `theme` of `window.cookieconsent_options`, which the script accepts
when the value contains ".css".

## Source

https://cdnjs.cloudflare.com/ajax/libs/cookieconsent2/1.0.10/dark-bottom.css, without changes. It matches the SRI
hash published by cdnjs: `sha512-tzw5smt99E3zkaNcV3muuK8EsAhBXU58sTeRqRPiutZXl2gxNsv85kkP+zT08fR3inQoyH8BriJHaYibcFdLQw==`.

The file still references `logo.png` on cdnjs, but the rule that uses it (`.cc_container .cc_logo`) has
`display: none`, so it is never downloaded.
