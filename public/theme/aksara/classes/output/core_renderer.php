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

namespace theme_aksara\output;

use core_course\external\course_summary_exporter;
use core_course_category;

/**
 * Core renderer for theme_aksara.
 *
 * Wraps the page header of the main landing pages (dashboard, my courses,
 * site home and the course page) in a coloured banner.
 *
 * @package    theme_aksara
 * @copyright  2026 Aksara
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer extends \theme_boost\output\core_renderer {

    /** @var string[] Page layouts that get the banner. */
    private const BANNER_LAYOUTS = ['mydashboard', 'mycourses', 'frontpage', 'course'];

    /**
     * Page header, wrapped in a banner on the landing pages.
     *
     * @return string
     */
    public function full_header() {
        $header = parent::full_header();

        $layout = $this->page->pagelayout;
        if (!in_array($layout, self::BANNER_LAYOUTS) || get_config('theme_aksara', 'showbanner') === '0') {
            return $header;
        }
        // Only the course home page, not the other pages that share the course layout.
        if ($layout === 'course' && $this->page->pagetype !== 'course-view-' . $this->page->course->format) {
            return $header;
        }

        $context = [
            'header' => $header,
            'variant' => $layout,
            'eyebrow' => '',
            'greeting' => '',
            'meta' => [],
            'image' => '',
        ];

        try {
            if ($layout === 'course') {
                $context = $this->course_banner($context);
            } else if ($layout === 'frontpage') {
                $tagline = trim((string) get_config('theme_aksara', 'logintagline'));
                $context['eyebrow'] = $tagline;
            } else {
                $context = $this->personal_banner($context);
            }
        } catch (\Throwable $e) {
            // The banner is decoration only: never break the page because of it.
            debugging('theme_aksara banner: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }

        $context['hasmeta'] = !empty($context['meta']);
        return $this->render_from_template('theme_aksara/banner', $context);
    }

    /**
     * Banner details for the course home page.
     *
     * @param array $context
     * @return array
     */
    private function course_banner(array $context): array {
        $course = $this->page->course;

        if ($category = core_course_category::get($course->category, IGNORE_MISSING, true)) {
            $context['eyebrow'] = $category->get_formatted_name();
        }

        $context['meta'][] = [
            'icon' => $this->pix_icon('i/course', ''),
            'text' => format_string($course->shortname, true, ['context' => $this->page->context]),
        ];

        $students = count_enrolled_users($this->page->context, 'mod/assign:submit', 0, true);
        if ($students > 0) {
            $context['meta'][] = [
                'icon' => $this->pix_icon('i/users', ''),
                'text' => $students === 1 ? get_string('bannerstudent', 'theme_aksara')
                    : get_string('bannerstudents', 'theme_aksara', $students),
            ];
        }

        $image = course_summary_exporter::get_course_image($course);
        if ($image) {
            $context['image'] = $image;
        }
        return $context;
    }

    /**
     * Banner details for the dashboard and My courses: greeting and today's date.
     *
     * @param array $context
     * @return array
     */
    private function personal_banner(array $context): array {
        global $USER;

        $context['eyebrow'] = userdate(time(), get_string('strftimedaydate', 'langconfig'));

        if (isloggedin() && !isguestuser()) {
            $hour = (int) usergetdate(time())['hours'];
            if ($hour >= 4 && $hour < 11) {
                $key = 'greetingmorning';
            } else if ($hour >= 11 && $hour < 15) {
                $key = 'greetingday';
            } else if ($hour >= 15 && $hour < 18) {
                $key = 'greetingafternoon';
            } else {
                $key = 'greetingevening';
            }
            $context['greeting'] = get_string($key, 'theme_aksara', $USER->firstname);

            $count = count(enrol_get_my_courses('id'));
            $context['meta'][] = [
                'icon' => $this->pix_icon('i/course', ''),
                'text' => $count === 1 ? get_string('bannercourse', 'theme_aksara')
                    : get_string('bannercourses', 'theme_aksara', $count),
            ];
        }
        return $context;
    }
}
