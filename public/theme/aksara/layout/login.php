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
 * Split-screen login layout for theme_aksara.
 *
 * @package    theme_aksara
 * @copyright  2026 Aksara
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$sitecontext = context_course::instance(SITEID);
$tagline = trim((string) get_config('theme_aksara', 'logintagline'));

$templatecontext = [
    'sitename' => format_string($SITE->shortname, true, ['context' => $sitecontext, 'escape' => false]),
    'sitefullname' => format_string($SITE->fullname, true, ['context' => $sitecontext, 'escape' => false]),
    'tagline' => $tagline !== '' ? nl2br(s($tagline)) : '',
    'year' => userdate(time(), '%Y'),
    'output' => $OUTPUT,
    'bodyattributes' => $OUTPUT->body_attributes(['aksara-loginpage']),
];

echo $OUTPUT->render_from_template('theme_aksara/login', $templatecontext);
