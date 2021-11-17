<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Cassandra\Custom;
use Exception;

final class ZoomKitMeetings extends ZoomKit {
    /**
     * ZoomKit for the Meetings Section of Zoom API
     *
     * MIT License
     * Code Copyright (c) 2021 Homeschool Connections
     *
     * Permission is hereby granted, free of charge, to any person obtaining a copy
     * of this software and associated documentation files (the "Software"), to deal
     * in the Software without restriction, including without limitation the rights
     * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
     * copies of the Software, and to permit persons to whom the Software is
     * furnished to do so, subject to the following conditions:
     *
     * The above copyright notice and this permission notice shall be included in all
     * copies or substantial portions of the Software.
     *
     * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
     * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
     * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
     * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
     * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
     * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
     * SOFTWARE.
     *
     * The Zoom API Descriptions are Copyright (c) 2021 Zoom Video Communications Inc.
     * These descriptions are not covered by the MIT license, only the functional code is.
     * I believe including Zoom's descriptions, considering they are publicly available,
     * is fair-use, but if you were distributing this code as purely MIT you may need
     * to do your own research on the legality of including the explanatory text.
     */

    /**
     * GET /users/{userId}/meetings
     *
     * List all the meetings that were scheduled for a user (meeting host).
     * For user-level apps, pass the `me` value instead of the userId parameter.
     *
     * This API only supports scheduled meetings.
     * This API does not return information about instant meetings.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Medium
     *
     * @param string $user_id ID or Email Address of the User. For user-level apps, pass the `me` value instead.
     * @param string $type The type of meeting to query, either live (default), upcoming, or scheduled.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param int|null $page_number The page number of the current page in the returned results.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetings(
        string $user_id,
        string $type = 'live',
        int $page_size = 30,
        int $page_number = null,
        string $next_page_token = null,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        if($type !== 'live' && $type !== 'upcoming' && $type !== 'scheduled') throw new Exception('Invalid meeting type.');

        return ZoomKit::returnResponse(
            'GET',
            '/users/'.$user_id.'/meetings',
            [
                'type' => $type,
                'page_size' => $page_size,
                'page_number' => $page_number,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * POST /users/{userId}/meetings
     *
     * Use this API to create a meeting for a user.
     * For user-level apps, pass the `me` value instead of the userId parameter.
     *
     * A meeting’s start_url value is the URL a host or an alternative host can use to start a meeting.
     * The expiration time for the start_url value is two hours for all regular users.
     * For custCreate meeting hosts (users created with the custCreate parameter via the Create Users API), the expiration time of the start_url parameter is 90 days from the generation of the start_url.
     *
     * Note:
     * For security reasons, the recommended way to programmatically (after expiry) get the updated start_url value is to call the Retrieve a Meeting API.
     * Refer to the start_url value in the response.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Medium
     *
     * This API has a daily rate limit of 100 requests per day.
     * The rate limit is applied against the userId of the meeting host used to make the request.
     *
     * I personally recommend that you call createMeeting() with PHP 8's new named arguments feature
     * due to how variable the fields Zoom wants are for this API. You should also check out the
     * ZoomKitMeetingsExtras class, which adds additional functions that are abstractions of this API
     * but with simpler functions with the proper amount of compatible arguments. For example,
     * calling ZoomKitMeetingsExtras::createScheduledMeeting() is more elegant than figuring out the
     * createMeeting() API it relies on directly.
     *
     * @param string $user_id ID or Email Address of the User. For user-level apps, pass the `me` value instead.
     * @param string $topic The meeting's topic.
     * @param string|null $agenda The meeting's agenda. Maximum length 2000 characters.
     * @param int $type Type of meeting: 1 for Instant Meeting, 2 for Scheduled Meeting, 3 for Recurring No Fixed Time, 8 for Recurring Fixed Time
     * @param Carbon|null $start_time The meeting's start time. Only for Types 2 and 8. Provided to Zoom in GMT (we don't implement the specific timezone option, and let Carbon handle this).
     * @param int|null $duration Scheduled meeting duration in minutes. Only for meeting Type 2. Default 30 minutes.
     * @param string|null $password The password to join the meeting. Max 10 chars, only alphanumeric and @-_* characters permitted. If minimum password settings are set on the tenant, this password must comply. You can use "Get User Settings" or "Get Account Settings" APIs to retrieve those requirements.
     * @param bool $default_password Whether to generate a default password with the user's settings. Default false. If true and user has the PMI setting enabled with a password, then the user's meetings will use the PMI password. It will not use a default password.
     * @param array|null $tracking_fields Information about the meeting's tracking fields.
     * @param array|null $recurrence Recurrence object->array used only for Meeting Type 8.
     * @param array|null $settings Information about the meeting's settings.
     * @param bool $pre_schedule Whether to create a prescheduled meeting via the GSuite app. This only supports Type 2 meetings.
     * @param string|null $schedule_for Email address or User ID to schedule the meeting for. If undefined, re-uses the current user ID.
     * @param string|null $template_id The account admin meeting template ID with which to schedule a meeting using a template.
     * @return array|Exception
     * @throws Exception
     */
    public static function createMeeting(
        string $user_id,
        string $topic,
        ?string $agenda = null,
        int $type = 2,
        ?Carbon $start_time = null,
        ?int $duration = 30,
        ?string $password = null,
        bool $default_password = false,
        ?array $tracking_fields = null,
        ?array $recurrence = null,
        ?array $settings = null,
        bool $pre_schedule = false,
        ?string $schedule_for = null,
        ?string $template_id = null,
    ): array|Exception
    {
        if($type !== 1 && $type !== 2 && $type !== 3 && $type !== 8) throw new Exception('Invalid meeting type.');
        if(!$schedule_for) {
            $schedule_for = $user_id;
        }
        if($start_time) {
            $start_time = $start_time->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        }

        $data = [
            'topic' => $topic,
            'type' => $type,
            'pre_schedule' => $pre_schedule,
            'start_time' => $start_time,
            'duration' => $duration,
            'schedule_for' => $schedule_for,
            'password' => $password,
            'default_password' => $default_password,
            'agenda' => $agenda,
            'tracking_fields' => $tracking_fields,
            'recurrence' => $recurrence,
            'settings' => $settings,
            'template_id' => $template_id,
        ];

        return ZoomKit::returnResponse(
            'POST',
            '/users/'.$user_id.'/meetings',
            [],
            [],
            $data,
        );
    }

    /**
     * GET /meetings/{meetingId}
     *
     * Retrieve the details of a meeting.
     *
     * Scopes: meeting:read:admin meeting:read
     * Rate Limit Label: Light
     *
     * @param int $meeting_id The meeting ID. WARNING: This could be greater than 10 digits, requiring saving as an int64/long. Do not store this as an int in your database.
     * @param string|null $occurrence_id Meeting Occurrence ID. Provide to view meeting details of a particular occurrence of a recurring meeting.
     * @param bool $show_previous_occurrences Set to true if you would like to view the meeting details of all previous occurrences of a recurring meeting.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeeting(
        int $meeting_id,
        string $occurrence_id = null,
        bool $show_previous_occurrences = false,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id,
            [
                'occurrence_id' => $occurrence_id,
                'show_previous_occurrences' => $show_previous_occurrences,
            ],
        );
    }

    /**
     * PATCH /meetings/{meetingId}
     *
     * Use this API to update a meeting’s details.
     *
     * Note:
     * This API has a rate limit of 100 requests per day.
     * Because of this, a meeting can only be updated for a maximum of 100 times within a 24-hour period.
     * The start_time value must be a future date.
     * If the value is omitted or a date in the past, the API ignores this value and will not update any recurring meetings.
     * If the start_time value is a future date, the recurrence object is required.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * You can call this API in the cleanest way possible using PHP 8's new named arguments.
     *
     * @param int $meeting_id ID of the meeting to update.
     * @param string|null $occurrence_id Meeting occurrence ID.
     * @param string|null $schedule_for Email address or User ID to schedule the meeting for.
     * @param string|null $topic The meeting's topic.
     * @param string|null $agenda The meeting's agenda. Maximum length 2000 characters.
     * @param int|null $type Type of meeting: 1 for Instant Meeting, 2 for Scheduled Meeting, 3 for Recurring No Fixed Time, 8 for Recurring Fixed Time
     * @param bool $pre_schedule Whether to create a prescheduled meeting via the GSuite app. This only supports Type 2 meetings.
     * @param Carbon|null $start_time The meeting's start time. Only for Types 2 and 8. Provided to Zoom in GMT (we don't implement the specific timezone option, and let Carbon handle this).
     * @param int|null $duration Scheduled meeting duration in minutes. Only for meeting Type 2.
     * @param string|null $password The password to join the meeting. Max 10 chars, only alphanumeric and @-_* characters permitted. If minimum password settings are set on the tenant, this password must comply. You can use "Get User Settings" or "Get Account Settings" APIs to retrieve those requirements.
     * @param string|null $template_id The account admin meeting template ID with which to schedule a meeting using a template.
     * @param array|null $tracking_fields Information about the meeting's tracking fields.
     * @param array|null $recurrence Recurrence object->array used only for Meeting Type 8.
     * @param array|null $settings Information about the meeting's settings.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateMeeting(
        int $meeting_id,
        ?string $occurrence_id = null,
        ?string $schedule_for = null,
        ?string $topic = null,
        ?string $agenda = null,
        ?int $type = null,
        ?bool $pre_schedule = null,
        ?Carbon $start_time = null,
        ?int $duration = null,
        ?string $password = null,
        ?string $template_id = null,
        ?array $tracking_fields = null,
        ?array $recurrence = null,
        ?array $settings = null,
    ): array|Exception
    {
        if($type !== 1 && $type !== 2 && $type !== null && $type !== 3 && $type !== 8) throw new Exception('Invalid meeting type.');

        $data = [];
        if($schedule_for) $data['schedule_for'] = $schedule_for;
        if($topic) $data['topic'] = $topic;
        if($agenda) $data['agenda'] = $agenda;
        if($type) $data['type'] = $type;
        if($pre_schedule) $data['pre_schedule'] = $pre_schedule;
        if($start_time) $data['start_time'] = $start_time->setTimezone('UTC')->format('Y-m-d\TH:i:s\Z');
        if($duration) $data['duration'] = $duration;
        if($password) $data['password'] = $password;
        if($template_id) $data['template_id'] = $template_id;
        if($tracking_fields) $data['tracking_fields'] = $tracking_fields;
        if($recurrence) $data['recurrence'] = $recurrence;
        if($settings) $data['settings'] = $settings;

        return ZoomKit::returnResponse(
            'PATCH',
            '/meetings/'.$meeting_id,
            [
                'occurrence_id' => $occurrence_id,
            ],
            [],
            $data,
        );
    }

    /**
     * DELETE /meetings/{meetingId}
     * Delete a meeting.
     *
     * Scopes: meeting:write:admin meeting:write
     * Rate Limit Label: Light
     *
     * @param int $meeting_id ID of the meeting to update.
     * @param string|null $occurrence_id Meeting occurrence ID.
     * @param bool $schedule_for_reminder Notify host and alternative host about meeting cancellation by email. Default true.
     * @param bool $cancel_meeting_reminder Notify registrants about meeting cancellation by email. Default false.
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteMeeting(
        int $meeting_id,
        ?string $occurrence_id = null,
        bool $schedule_for_reminder = true,
        bool $cancel_meeting_reminder = false,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'DELETE',
            '/meetings/'.$meeting_id,
            [
                'occurrence_id' => $occurrence_id,
                'schedule_for_reminder' => $schedule_for_reminder,
                'cancel_meeting_reminder' => $cancel_meeting_reminder,
            ],
        );
    }

    /**
     * PUT /meetings/{meetingId}/status
     *
     * Update the status of a meeting.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * @param int $meeting_id ID of the meeting to update.
     * @param string $action Either end (end meeting) or recover (recover a deleted meeting).
     * @return array|Exception
     * @throws Exception
     */
    public static function updateMeetingStatus(
        int $meeting_id,
        string $action,
    ): array|Exception
    {
        if($action !== 'end' && $action !== 'recover') throw new Exception ('Unsupported action.');

        $data = [
            'action' => $action,
        ];

        return ZoomKit::returnResponse(
            'PUT',
            '/meetings/'.$meeting_id.'/status',
            [],
            [],
            $data,
        );
    }

    /**
     * GET /meetings/{meetingId}/registrants
     *
     * A host or a user with admin permission can require registration for a Zoom meeting.
     * Use this API to list users that have registered for a meeting.
     *
     * Scopes: meeting:read:admin meeting:read
     * Rate Limit Label: Medium
     *
     * @param int $meeting_id ID of the meeting to update.
     * @param string|null $occurrence_id Meeting occurrence ID.
     * @param string $status Registrant status to check: pending, approved (default), or denied.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetingRegistrants(
        int $meeting_id,
        ?string $occurrence_id = null,
        string $status = 'approved',
        int $page_size = 30,
        ?string $next_page_token = null,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        if($status !== 'pending' && $status !== 'approved' && $status !== 'denied') throw new Exception('Unsupported status type.');

        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/registrants',
            [
                'occurrence_id' => $occurrence_id,
                'status' => $status,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ]
        );
    }

    /**
     * POST /meetings/{meetingId}/registrants
     *
     * Register a participant for a meeting.
     * Note that there is a maximum limit of 4999 registrants per meeting and users will see an error if the capacity has reached.
     *
     * Prerequisite:
     * Host user type must be “Licensed”.
     *
     * Scopes: meeting:write:admin meeting:write
     * Rate Limit Label: Light
     *
     * WARNING: There are validations on this data required that this API implementation does not attempt to handle.
     *
     * @param int $meeting_id ID of the meeting to register the registrant for.
     * @param string $email A valid email address for the registrant.
     * @param string $first_name First name
     * @param array|null $occurrence_ids Meeting occurrence IDs.
     * @param string|null $last_name Last name
     * @param string|null $address Address
     * @param string|null $city City
     * @param string|null $country Country (two letters)
     * @param string|null $zip ZIP / Postal Code
     * @param string|null $state State / Province
     * @param string|null $phone Phone Number
     * @param string|null $industry Industry
     * @param string|null $org Organization
     * @param string|null $job_title Job Title
     * @param string|null $purchasing_time_frame Purchasing Time Frame. 5 valid options: Within a month, 1-3 months, 4-6 months, More than 6 months, No timeframe
     * @param string|null $role_in_purchase_process Role in Purchase Process. 4 valid options: Decision Maker, Evaluator/Recommender, Influencer, Not involved
     * @param string|null $no_of_employees Number of employees. 8 valid options: 1-20, 21-50, 51-100, 101-500, 501-1,000, 1,001-5,000, 5,001-10,000, More than 10,000
     * @param string|null $comments Registrants can provide any questions or comments they have in this field
     * @param array|null $custom_questions Custom questions in the form of an array
     * @param string|null $language Language for confirmation emails. Valid options: en-US, de-DE, es-ES, fr-FR, jp-JP, pt-PT, ru-RU, zh-CN, zh-TW, ko-KO, it-IT, vi-VN
     * @param bool|null $auto_approve No official documentation available on what this does, just that it exists.
     * @return array|Exception
     * @throws Exception
     */
    public static function addMeetingRegistrant(
        int $meeting_id,
        string $email,
        string $first_name,
        ?array $occurrence_ids = null,
        ?string $last_name = null,
        ?string $address = null,
        ?string $city = null,
        ?string $country = null,
        ?string $zip = null,
        ?string $state = null,
        ?string $phone = null,
        ?string $industry = null,
        ?string $org = null,
        ?string $job_title = null,
        ?string $purchasing_time_frame = null,
        ?string $role_in_purchase_process = null,
        ?string $no_of_employees = null,
        ?string $comments = null,
        ?array $custom_questions = null,
        ?string $language = null,
        ?bool $auto_approve = null,
    ): array|Exception
    {
        $data = [
            'email' => $email,
            'first_name' => $first_name
        ];
        if($last_name) $data['last_name'] = $last_name;
        if($address) $data['address'] = $address;
        if($city) $data['city'] = $city;
        if($country) $data['country'] = $country;
        if($zip) $data['zip'] = $zip;
        if($state) $data['state'] = $state;
        if($phone) $data['phone'] = $phone;
        if($industry) $data['industry'] = $industry;
        if($org) $data['org'] = $org;
        if($job_title) $data['job_title'] = $job_title;
        if($purchasing_time_frame) $data['purchasing_time_frame'] = $purchasing_time_frame;
        if($role_in_purchase_process) $data['role_in_purchase_process'] = $role_in_purchase_process;
        if($no_of_employees) $data['no_of_employees'] = $no_of_employees;
        if($comments) $data['comments'] = $comments;
        if($custom_questions) $data['custom_questions'] = $custom_questions;
        if($language) $data['language'] = $language;
        if($auto_approve) $data['auto_approve'] = $auto_approve;

        return ZoomKit::returnResponse(
            'POST',
            '/meetings/'.$meeting_id.'/registrants',
            [
                'occurrence_ids' => implode(',', $occurrence_ids),
            ],
            [],
            $data
        );
    }

    /**
     * DELETE /meetings/{meetingId}/registrants/{registrantId}
     *
     * Delete a meeting registrant.
     *
     * Scopes: meeting:write:admin meeting:write
     * Rate Limit Label: Light
     *
     * @param int $meeting_id ID of the meeting to delete the registrant from.
     * @param string $registrant_id ID of the registrant to delete from the meeting.
     * @param string|null $occurrence_id The meeting occurrence ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteMeetingRegistrant(
        int $meeting_id,
        string $registrant_id,
        ?string $occurrence_id = null
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'DELETE',
            '/meetings/'.$meeting_id.'/registrants/'.$registrant_id,
            [
                'occurrence_id' => $occurrence_id,
            ]
        );
    }

    /**
     * GET /meetings/{meetingId}/registrants/{registrantId}
     *
     * Use this API to get details on a specific user who has registered for the meeting.
     * A host or a user with administrative permissions can require registration for Zoom meetings.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Light
     *
     * @param int $meeting_id ID of the meeting to get the registrant from.
     * @param string $registrant_id ID of the registrant get from the meeting.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingRegistrant(
        int $meeting_id,
        string $registrant_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/registrants/'.$registrant_id,
        );
    }
}
