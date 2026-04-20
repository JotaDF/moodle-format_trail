
### Changes Version 5.0.0.0 for Moodle 4.5 and 5.x onwards (2026042001)

* 2026-04-20 - Added `supports_components()` for full reactive course editor compatibility (Moodle 5.x).
* 2026-04-20 - Added `uses_course_index()` to enable the course index sidebar drawer.
* 2026-04-20 - Replaced deprecated `section_add_cm_controls()` call; updated section lookup to modern API.
* 2026-04-20 - Replaced `imagecopybicubic()` (removed in PHP 8.1+) with `imagecopyresampled()`.
* 2026-04-20 - Fixed float-to-int precision warnings in image upload and resize paths (PHP 8.1+).
* 2026-04-20 - Declared legacy properties in custom form element to suppress PHP 8.2 dynamic property deprecations.
* 2026-04-20 - Rebuilt `controlmenu` output component for dual Bootstrap 4/5 compatibility (Moodle 4.5 and 5.x).
* 2026-04-20 - Restored shadebox modal via AMD module; fixed overlay z-index and pointer-events for Moodle 5.1 Boost.
* 2026-04-20 - Fixed section card edit UI: collapse toggle, reactive data attributes, and border/gap styling.
* 2026-04-20 - Passed PHPCS Moodle coding standards; fixed `@license` tags, lang file comments, and JSHint config.
* 2026-04-20 - Added GitHub Actions CI workflow covering Moodle 4.5, 5.0, 5.1 and main branch.
* 2026-04-20 - Added PHPUnit test suite covering core class, observer, and privacy provider.
* 2026-04-20 - Updated `version.php` requirements to Moodle 4.4 (build 2024042200) and set release to 5.1.0.0.

### Changes Version 3.8.1 for Moodle 3.2 onwards (2021050727)

* 2021-07-09 - Initial version
* 2021-09-06 - **Fixes for PHP 8**
* 2021-09-06 - **New style of hunting or treasure trail**

### Changes Version 4.0.0.1 for Moodle 4.0 onwards (2022041900)
* 2022-07-02 - Adaptations to support Moodle 4.0
* 2022-07-02 - Subtle visual changes

### Changes Version 4.1.0.0 for Moodle 4.1 onwards (2023053109)
* 2023-05-31 - Adaptations to support Moodle 4.1
* 2023-05-31 - Visual changes to display end of track

### Changes Version 4.2.0.0 for Moodle 4.1 onwards (2023053109)
* 2023-06-05 - Adaptations to support Moodle 4.2
