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
        if ($layout === 'frontpage' && get_config('theme_aksara', 'frontpagehero') !== '0') {
            try {
                return $this->frontpage_hero($header);
            } catch (\Throwable $e) {
                debugging('theme_aksara hero: ' . $e->getMessage(), DEBUG_DEVELOPER);
            }
        }
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

    /**
     * Landing section for the site home: hero, statistics and feature boxes.
     *
     * @param string $header Standard page header, kept for its header actions.
     * @return string
     */
    private function frontpage_hero(string $header): string {
        global $CFG, $SITE, $DB;

        $config = get_config('theme_aksara');
        $sitecontext = \context_course::instance(SITEID);

        $title = trim((string) ($config->herotitle ?? ''));
        if ($title === '') {
            $title = format_string($SITE->fullname, true, ['context' => $sitecontext]);
        } else {
            $title = format_string($title, true, ['context' => $sitecontext]);
        }
        $text = trim((string) ($config->herotext ?? ''));
        if ($text === '') {
            $text = trim((string) ($config->logintagline ?? ''));
        }

        $buttons = [];
        if (!isloggedin() || isguestuser()) {
            $buttons[] = ['url' => get_login_url(), 'label' => get_string('login'), 'primary' => true];
        } else {
            $buttons[] = ['url' => (new \moodle_url('/my/courses.php'))->out(false),
                'label' => get_string('mycourses'), 'primary' => true];
        }
        $buttons[] = ['url' => (new \moodle_url('/course/index.php'))->out(false),
            'label' => get_string('herobrowse', 'theme_aksara'), 'primary' => false];

        $stats = [];
        if (($config->herostats ?? '1') !== '0') {
            $cache = \cache::make('theme_aksara', 'stats');
            if (!$counts = $cache->get('frontpage')) {
                $counts = [
                    'users' => $DB->count_records_select('user',
                        'deleted = 0 AND suspended = 0 AND confirmed = 1 AND id <> :guest', ['guest' => $CFG->siteguest]),
                    'courses' => $DB->count_records_select('course', 'id <> :site AND visible = 1', ['site' => SITEID]),
                    'categories' => $DB->count_records('course_categories', ['visible' => 1]),
                ];
                $cache->set('frontpage', $counts);
            }
            foreach (['users', 'courses', 'categories'] as $key) {
                $stats[] = [
                    'value' => number_format($counts[$key], 0, ',', '.'),
                    'label' => get_string('herostat_' . $key, 'theme_aksara'),
                ];
            }
        }

        $features = [];
        for ($i = 1; $i <= 4; $i++) {
            $ftitle = trim((string) ($config->{'feature' . $i . 'title'} ?? ''));
            $ftext = trim((string) ($config->{'feature' . $i . 'text'} ?? ''));
            if ($ftitle === '' && $ftext === '') {
                continue;
            }
            $features[] = [
                'number' => sprintf('%02d', count($features) + 1),
                'title' => format_string($ftitle, true, ['context' => $sitecontext]),
                'text' => format_string($ftext, true, ['context' => $sitecontext]),
            ];
        }

        $theme = \theme_config::load('aksara');
        $image = $theme->setting_file_url('heroimage', 'heroimage');

        return $this->render_from_template('theme_aksara/frontpage_hero', [
            'header' => $header,
            'title' => $title,
            'text' => $text,
            'buttons' => $buttons,
            'hasstats' => !empty($stats),
            'stats' => $stats,
            'hasfeatures' => !empty($features),
            'features' => $features,
            'image' => $image ? (string) $image : '',
        ]);
    }

    /**
     * Site footer shown at the bottom of every page (except login and pop-ups).
     *
     * @return string
     */
    public function aksara_footer(): string {
        global $SITE;

        $layout = $this->page->pagelayout;
        if (in_array($layout, ['login', 'popup', 'embedded', 'frametop', 'print', 'redirect', 'maintenance', 'secure'])) {
            return '';
        }
        if (get_config('theme_aksara', 'showfooter') === '0') {
            return '';
        }

        $config = get_config('theme_aksara');
        $sitecontext = \context_course::instance(SITEID);

        $contacts = [];
        foreach (['contactemail' => 'fa-envelope', 'contactphone' => 'fa-phone', 'contactaddress' => 'fa-location-dot'] as $key => $icon) {
            $value = trim((string) ($config->$key ?? ''));
            if ($value === '') {
                continue;
            }
            $item = ['icon' => $icon, 'text' => $value, 'url' => ''];
            if ($key === 'contactemail' && validate_email($value)) {
                $item['url'] = 'mailto:' . $value;
            } else if ($key === 'contactphone') {
                $item['url'] = 'tel:' . preg_replace('/[^0-9+]/', '', $value);
            }
            $contacts[] = $item;
        }

        $socials = [];
        $networks = ['website' => 'fa-globe', 'instagram' => 'fa-instagram', 'youtube' => 'fa-youtube',
            'facebook' => 'fa-facebook', 'whatsapp' => 'fa-whatsapp'];
        foreach ($networks as $key => $icon) {
            $url = clean_param(trim((string) ($config->{'social' . $key} ?? '')), PARAM_URL);
            if ($url === '') {
                continue;
            }
            $socials[] = ['url' => $url, 'icon' => $icon, 'label' => get_string('social_' . $key, 'theme_aksara')];
        }

        $about = trim((string) ($config->footerabout ?? ''));

        return $this->render_from_template('theme_aksara/site_footer', [
            'sitename' => format_string($SITE->fullname, true, ['context' => $sitecontext]),
            'about' => $about !== '' ? nl2br(s($about)) : '',
            'hascontacts' => !empty($contacts),
            'contacts' => $contacts,
            'hassocials' => !empty($socials),
            'socials' => $socials,
            'year' => userdate(time(), '%Y'),
            'links' => [
                ['url' => (new \moodle_url('/course/index.php'))->out(false), 'label' => get_string('fulllistofcourses')],
                ['url' => (new \moodle_url('/calendar/view.php'))->out(false), 'label' => get_string('calendar', 'calendar')],
            ],
        ]);
    }
}
