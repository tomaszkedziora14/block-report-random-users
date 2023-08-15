<?php

defined('MOODLE_INTERNAL') || die();

class block_report_random_users extends report {

    public function __construct() {
        $this->reportname = get_string('pluginname', 'report_random_users');
        $this->capability = 'moodle/site:config';
        $this->settings = null;
    }

    public function has_config() {
        return false;
    }

    public function display() {
        global $CFG, $DB;

        require_once($CFG->dirroot . '/enrol/locallib.php'); // Include enrol functions.

        $this->set_heading($this->reportname);

        // Get 10 random users.
        $random_users = $DB->get_records_sql("SELECT * FROM {user} ORDER BY RAND() LIMIT 10");

        $this->output_table($random_users);
    }

    protected function output_table($users) {
        global $DB;

        $table = new html_table();
        $table->head = array(get_string('username'), get_string('enrolledcourses', 'report_random_users'));
        $table->data = array();

        foreach ($users as $user) {
            $sql = "SELECT c.shortname FROM {user_enrolments} ue
                    JOIN {enrol} e ON ue.enrolid = e.id
                    JOIN {course} c ON e.courseid = c.id
                    WHERE ue.userid = ?";
            $course_names = '';
            $courses = $DB->get_records_sql($sql, array($user->id));
            foreach ($courses as $course) {
                $course_names .= $course->shortname . '<br>';
            }
            $table->data[] = array($user->username, $course_names);
        }

        echo html_writer::table($table);
    }
}


