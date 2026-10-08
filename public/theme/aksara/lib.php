<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Library functions for theme_aksara.
 *
 * @package    theme_aksara
 * @copyright  2026 Aksara
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Files that make up the Aksara layer, compiled after Bootstrap and Moodle core styles.
 */
const THEME_AKSARA_PARTIALS = [
    'fonts',
    'base',
    'navbar',
    'drawers',
    'header',
    'banner',
    'components',
    'forms',
    'dashboard',
    'course',
    'tables',
    'people',
    'login',
    'footer',
];

/**
 * Returns the main SCSS: Boost's default preset followed by the Aksara partials.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_aksara_get_main_scss_content($theme) {
    global $CFG;

    $scss = file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    foreach (THEME_AKSARA_PARTIALS as $partial) {
        $scss .= "\n" . file_get_contents(__DIR__ . "/scss/aksara/_{$partial}.scss");
    }
    return $scss;
}

/**
 * Variables placed before everything else, so they win over Bootstrap's !default values.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_aksara_get_pre_scss($theme) {
    $scss = '';
    $configurable = [
        'brandcolor' => ['primary'],
        'accentcolor' => ['aksara-accent'],
    ];

    foreach ($configurable as $configkey => $targets) {
        $value = $theme->settings->{$configkey} ?? null;
        if (empty($value)) {
            continue;
        }
        foreach ($targets as $target) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }
    }

    if (defined('BEHAT_SITE_RUNNING')) {
        $scss .= "\$behatsite: true;\n";
    }

    $scss .= file_get_contents(__DIR__ . '/scss/aksara/_variables.scss');

    if (!empty($theme->settings->scsspre)) {
        $scss .= "\n" . $theme->settings->scsspre;
    }
    return $scss;
}

/**
 * SCSS appended last: login side image and the admin's own Raw SCSS.
 *
 * @param theme_config $theme
 * @return string
 */
function theme_aksara_get_extra_scss($theme) {
    $content = '';

    $loginimage = $theme->setting_file_url('loginimage', 'loginimage');
    if (!empty($loginimage)) {
        $content .= ".aksara-login__aside { background-image: url('{$loginimage}'); }\n";
        $content .= ".aksara-login__aside::after { opacity: .82; }\n";
    }

    if (!empty($theme->settings->scss)) {
        $content .= "\n" . $theme->settings->scss;
    }
    return $content;
}

/**
 * Fallback CSS used while the real stylesheet is being built.
 *
 * @return string
 */
function theme_aksara_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/style/moodle.css');
}

/**
 * Serves files uploaded through the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_aksara_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = []) {
    if ($context->contextlevel == CONTEXT_SYSTEM && $filearea === 'loginimage') {
        $theme = theme_config::load('aksara');
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    }
    send_file_not_found();
}
