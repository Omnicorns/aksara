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

use html_writer;
use popup_action;
use single_button;

/**
 * Quiz renderer for theme_aksara: clearer label on the link to review an attempt.
 *
 * @package    theme_aksara
 * @copyright  2026 Aksara
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_quiz_renderer extends \mod_quiz\output\renderer {

    /**
     * Label for the review link: the admin's own text, or the theme's default.
     *
     * @return string
     */
    protected function aksara_review_label(): string {
        $label = trim((string) get_config('theme_aksara', 'quizreviewlabel'));
        return $label !== '' ? format_string($label) : get_string('quizreview', 'theme_aksara');
    }

    /**
     * Link (or popup button) to the review page of an attempt.
     *
     * @param \moodle_url $url
     * @param bool $reviewinpopup
     * @param array $popupoptions
     * @return string
     */
    public function review_link($url, $reviewinpopup, $popupoptions) {
        $label = $this->aksara_review_label();
        if ($reviewinpopup) {
            $button = new single_button($url, $label);
            $button->add_action(new popup_action('click', $url, 'quizpopup', $popupoptions));
            return $this->render($button);
        }
        return html_writer::link($url, $label, [
            'title' => get_string('reviewthisattempt', 'quiz'),
            'class' => 'aksara-quiz-review',
        ]);
    }
}
