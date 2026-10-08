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
 * Settings for theme_aksara.
 *
 * @package    theme_aksara
 * @copyright  2026 Aksara
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingaksara', get_string('configtitle', 'theme_aksara'));

    // General tab.
    $page = new admin_settingpage('theme_aksara_general', get_string('generalsettings', 'theme_aksara'));

    $setting = new admin_setting_configcolourpicker('theme_aksara/brandcolor',
        get_string('brandcolor', 'theme_aksara'), get_string('brandcolor_desc', 'theme_aksara'), '#17594f');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker('theme_aksara/accentcolor',
        get_string('accentcolor', 'theme_aksara'), get_string('accentcolor_desc', 'theme_aksara'), '#b8741a');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcheckbox('theme_aksara/showbanner',
        get_string('showbanner', 'theme_aksara'), get_string('showbanner_desc', 'theme_aksara'), 1);
    $page->add($setting);

    $settings->add($page);

    // Login tab.
    $page = new admin_settingpage('theme_aksara_login', get_string('loginsettings', 'theme_aksara'));

    $setting = new admin_setting_configtextarea('theme_aksara/logintagline',
        get_string('logintagline', 'theme_aksara'), get_string('logintagline_desc', 'theme_aksara'),
        get_string('logintagline_default', 'theme_aksara'), PARAM_TEXT, 60, 3);
    $page->add($setting);

    $setting = new admin_setting_configstoredfile('theme_aksara/loginimage',
        get_string('loginimage', 'theme_aksara'), get_string('loginimage_desc', 'theme_aksara'), 'loginimage', 0,
        ['maxfiles' => 1, 'accepted_types' => ['.jpg', '.jpeg', '.png', '.webp']]);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);

    // Advanced tab.
    $page = new admin_settingpage('theme_aksara_advanced', get_string('advancedsettings', 'theme_aksara'));

    $setting = new admin_setting_scsscode('theme_aksara/scsspre',
        get_string('rawscsspre', 'theme_aksara'), get_string('rawscsspre_desc', 'theme_aksara'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_scsscode('theme_aksara/scss',
        get_string('rawscss', 'theme_aksara'), get_string('rawscss_desc', 'theme_aksara'), '', PARAM_RAW);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
