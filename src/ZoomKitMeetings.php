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
     * @param string $meeting_id The meeting ID.
     * @param string|null $occurrence_id Meeting Occurrence ID. Provide to view meeting details of a particular occurrence of a recurring meeting.
     * @param bool $show_previous_occurrences Set to true if you would like to view the meeting details of all previous occurrences of a recurring meeting.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeeting(
        string $meeting_id,
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
     * @param string $meeting_id ID of the meeting to update.
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
        string $meeting_id,
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
     * @param string $meeting_id ID of the meeting to update.
     * @param string|null $occurrence_id Meeting occurrence ID.
     * @param bool $schedule_for_reminder Notify host and alternative host about meeting cancellation by email. Default true.
     * @param bool $cancel_meeting_reminder Notify registrants about meeting cancellation by email. Default false.
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteMeeting(
        string $meeting_id,
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
     * @param string $meeting_id ID of the meeting to update.
     * @param string $action Either end (end meeting) or recover (recover a deleted meeting).
     * @return array|Exception
     * @throws Exception
     */
    public static function updateMeetingStatus(
        string $meeting_id,
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
     * @param string $meeting_id ID of the meeting to update.
     * @param string|null $occurrence_id Meeting occurrence ID.
     * @param string $status Registrant status to check: pending, approved (default), or denied.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetingRegistrants(
        string $meeting_id,
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
     * @param string $meeting_id ID of the meeting to register the registrant for.
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
     * @param int|null $auto_approve No official documentation available on what this does, just that it exists. Reverse engineeering suggests 1 requires participants to be manually approved, 0 auto-approves.
     * @return array|Exception
     * @throws Exception
     */
    public static function addMeetingRegistrant(
        string $meeting_id,
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
        ?int $auto_approve = null,
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
     * @param string $meeting_id ID of the meeting to delete the registrant from.
     * @param string $registrant_id ID of the registrant to delete from the meeting.
     * @param string|null $occurrence_id The meeting occurrence ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteMeetingRegistrant(
        string $meeting_id,
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
     * @param string $meeting_id ID of the meeting to get the registrant from.
     * @param string $registrant_id ID of the registrant get from the meeting.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingRegistrant(
        string $meeting_id,
        string $registrant_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/registrants/'.$registrant_id,
        );
    }

    /**
     * PUT /meetings/{meetingId}/registrants/status
     *
     * Update a meeting registrant’s status by either approving, cancelling or denying a registrant from joining the meeting.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Medium
     *
     * @param string $meeting_id ID of the meeting to delete the registrant from.
     * @param string $action Either end (end meeting) or recover (recover a deleted meeting).
     * @param array $registrants List of registrants with name and email to apply action on.
     * @param string|null $occurrence_id The meeting occurrence ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateRegistrantStatus(
        string $meeting_id,
        string $action,
        array $registrants,
        ?string $occurrence_id = null
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'PUT',
            '/meetings/'.$meeting_id.'/registrants/status',
            [
                'occurrence_id' => $occurrence_id,
            ],
            [],
            [
                'action' => $action,
                'registrants' => $registrants,
            ]
        );
    }

    /**
     * GET /past_meetings/{meetingUUID}
     *
     * Use this API to get information about a past meeting.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Light
     *
     * @param string $meetingUUID The meeting's universally unique identifier. Each meeting instance generates a new UUID. For example, after a meeting ends, a new UUID is generated for the next instance. If the UUID begins with / or contains a //, you must double-encode the meeting UUID when using the meeting UUID for other calls.
     * @return array|Exception
     * @throws Exception
     */
    public static function getPastMeetingDetails(
        string $meetingUUID,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/past_meetings/'.$meetingUUID,
        );
    }

    /**
     * GET /past_meetings/{meetingUUID}/participants
     *
     * Retrieve information on participants from a past meeting.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Medium
     *
     * @param string $meetingUUID The meeting's universally unique identifier. Each meeting instance generates a new UUID. For example, after a meeting ends, a new UUID is generated for the next instance. If the UUID begins with / or contains a //, you must double-encode the meeting UUID when using the meeting UUID for other calls.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getPastMeetingParticipants(
        string $meetingUUID,
        int $page_size = 30,
        ?string $next_page_token = null,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/past_meetings/'.$meetingUUID.'/participants',
            [
                'page_size' => $page_size,
                'next_page_token' => $next_page_token
            ]
        );
    }

    /**
     * GET /past_meetings/{meetingId}/instances
     *
     * Get a list of ended meeting instances.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Medium
     *
     * @param string $meeting_id The meeting ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function listEndedMeetingInstance(
        string $meeting_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/past_meetings/'.$meeting_id.'/instances',
        );
    }

    /**
     * GET /meetings/{meetingId}/polls
     *
     * Polls allow the meeting host to survey attendees. Use this API to list polls of a meeting.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @param bool $anonymous Whether to query for polls with the anonymous option enabled. Default true.
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetingPolls(
        string $meeting_id,
        bool $anonymous = true,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/polls',
            [
                'anonymous' => $anonymous,
            ]
        );
    }

    /**
     * POST /meetings/{meetingId}/polls
     *
     * Polls allow the meeting host to survey attendees. Use this API to create a poll for a meeting.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * Meeting must be a scheduled meeting. Instant meetings do not have polling features enabled.
     *
     * @param string $meeting_id The meeting ID.
     * @param string $title The poll's title, up to 64 characters.
     * @param int $poll_type The poll's type, 1 for Poll, 2 for Advanced Poll (if enabled), 3 for Quiz (if enabled). Defaults to 1.
     * @param bool $anonymous Whether to query for polls with the anonymous option enabled. Default false.
     * @param array $questions Questions for the poll - See Zoom docs for the Question array format.
     * @return array|Exception
     * @throws Exception
     */
    public static function createMeetingPoll(
        string $meeting_id,
        string $title,
        array $questions,
        int $poll_type = 1,
        bool $anonymous = false,
    ): array|Exception
    {
        if($poll_type !== 1 && $poll_type !== 2 && $poll_type !== 3) throw new Exception ('Not a supported poll type.');

        return ZoomKit::returnResponse(
            'POST',
            '/meetings/'.$meeting_id.'/polls',
            [],
            [],
            [
                'title' => $title,
                'poll_type' => $poll_type,
                'anonymous' => $anonymous,
                'questions' => $questions,
            ]
        );
    }

    /**
     * GET /meetings/{meetingId}/polls/{pollId}
     *
     * Polls allow the meeting host to survey attendees.
     * Use this API to get information about a specific meeting poll.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @param string $poll_id The poll ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingPoll(
        string $meeting_id,
        string $poll_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'POST',
            '/meetings/'.$meeting_id.'/polls/'.$poll_id
        );
    }

    /**
     * PUT /meetings/{meetingId}/polls/{pollId}
     *
     * Polls allow the meeting host to survey attendees.
     * Use this API to update information of a specific meeting poll.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @param string $poll_id The poll ID.
     * @param string|null $title The poll's title, up to 64 characters.
     * @param array|null $questions Questions for the poll - See Zoom docs for the Question array format.
     * @param int|null $poll_type The poll's type, 1 for Poll, 2 for Advanced Poll (if enabled), 3 for Quiz (if enabled). Defaults to 1.
     * @param bool $anonymous Whether to query for polls with the anonymous option enabled. Default false.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateMeetingPoll(
        string $meeting_id,
        string $poll_id,
        ?string $title = null,
        ?array $questions = null,
        ?int $poll_type = null,
        ?bool $anonymous = null,
    ): array|Exception
    {
        $data = array();

        if($poll_type) {
            if($poll_type !== 1 && $poll_type !== 2 && $poll_type !== 3) throw new Exception ('Not a supported poll type.');
        }

        if($title) $data['title'] = $title;
        if($questions) $data['questions'] = $questions;
        if($poll_type) $data['poll_type'] = $poll_type;
        if($anonymous) $data['anonymous'] = $anonymous;

        return ZoomKit::returnResponse(
            'PUT',
            '/meetings/'.$meeting_id.'/polls/'.$poll_id,
            [],
            [],
            $data
        );
    }

    /**
     * DELETE /meetings/{meetingId}/polls/{pollId}
     *
     * Polls allow the meeting host to survey attendees. Use this API to delete a meeting poll.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * Meeting must be a scheduled meeting. Instant meetings do not have polling features enabled.
     *
     * @param string $meeting_id The meeting ID.
     * @param string $poll_id The poll ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteMeetingPoll(
        string $meeting_id,
        string $poll_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'DELETE',
            '/meetings/'.$meeting_id.'/polls/'.$poll_id
        );
    }

    /**
     * GET /meetings/{meetingId}/registrants/questions
     *
     * List registration questions that will be displayed to users while registering for a meeting.
     *
     * Scopes: meeting:read, meeting:read:admin
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function listRegistrationQuestions(
        string $meeting_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/registrants/questions'
        );
    }

    /**
     * PATCH /meetings/{meetingId}/registrants/questions
     *
     * Update registration questions that will be displayed to users while registering for a meeting.
     *
     * Scopes: meeting:write, meeting:write:admin
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @param array|null $questions Array of registrant questions
     * @param array|null $custom_questions Array of registrant custom questions
     * @return array|Exception
     * @throws Exception
     */
    public static function updateRegistrationQuestions(
        string $meeting_id,
        ?array $questions = null,
        ?array $custom_questions = null
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'PATCH',
            '/meetings/'.$meeting_id.'/registrants/questions',
            [],
            [],
            [
                'questions' => $questions,
                'custom_questions' => $custom_questions
            ]
        );
    }

    /**
     * GET /meetings/{meetingId}/invitation
     *
     * Retrieve the meeting invite note that was sent for a specific meeting.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingInvitation(
        string $meeting_id
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/invitation'
        );
    }

    /**
     * PATCH /meetings/{meetingId}/livestream
     *
     * Use this API to update a meeting’s live stream information.
     * Zoom allows users to live stream a meeting to a custom platform.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @param string $stream_url Streaming URL (whatever that means Zoom docs!)
     * @param string $stream_key Stream Name and Key
     * @param string $page_url The livestream page URL
     * @return array|Exception
     * @throws Exception
     */
    public static function updateLiveStream(
        string $meeting_id,
        string $stream_url,
        string $stream_key,
        string $page_url
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'PATCH',
            '/meetings/'.$meeting_id.'/livestream',
            [],
            [],
            [
                'stream_url' => $stream_url,
                'stream_key' => $stream_key,
                'page_url' => $page_url
            ]
        );
    }

    /**
     * GET /meetings/{meetingId}/livestream
     *
     * Zoom allows users to live stream a meeting to a custom platform.
     * Use this API to get a meeting’s live stream configuration details such as Stream URL, Stream Key and Page URL.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function getLiveStreamDetails(
        string $meeting_id
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/livestream'
        );
    }

    /**
     * PATCH /meetings/{meetingId}/livestream/status
     *
     * Zoom allows users to live stream a meeting to a custom platform.
     * Use this API to update the status of a meeting’s live stream.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @param string $action Update the status of a live stream. This can be either `start` to start the stream or `stop` to stop an ongoing stream.
     * @param array $settings Update the settings of a live stream session. The settings can only be updated for a stopped live stream and cannot be updated if the stream is ongoing.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateLiveStreamStatus(
        string $meeting_id,
        string $action,
        array $settings
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'PATCH',
            '/meetings/'.$meeting_id.'/livestream/status',
            [],
            [],
            [
                'action' => $action,
                'settings' => $settings
            ]
        );
    }

    /**
     * GET /past_meetings/{meetingId}/polls
     *
     * Polls allow the meeting host to survey attendees.
     * Use this API to list poll results of a meeting.
     *
     * Scopes: meeting:read:admin, meeting:read
     * Rate Limit Label: Medium
     *
     * Meeting must be a scheduled meeting.
     * Instant meetings do not have polling features enabled.
     *
     * @param string $meeting_id The meeting ID.
     * @return array|Exception
     * @throws Exception
     */
    public static function listPastMeetingPollResults(
        string $meeting_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/past_meetings/'.$meeting_id.'/polls'
        );
    }

    /**
     * POST /meetings/{meetingId}/batch_registrants
     *
     * Register up to 30 registrants at once for a meeting that requires registration.
     * The meeting must require registration and should be of type 2, i.e., they should be scheduled meetings.
     * Instant meetings and Recurring meetings are not supported by this API.
     *
     * Scope: meeting:write, meeting:write:admin
     * Rate Limit Label: Heavy
     *
     * @param string $meeting_id The meeting ID.
     * @param int $auto_approve If a meeting was scheduled with approval_type 1 (manual approval), but you would like to automatically approve the registrants added by this API, set this to true. You cannot use this field to change the approval setting for a meeting that was originally scheduled with approval_type 0 (automatic).
     * @param array $registrants Array of registrants to batch-register. Can contain first_name, last_name, and email. Only email is marked as required.
     * @return array|Exception
     * @throws Exception
     */
    public static function performBatchRegistration(
        string $meeting_id,
        int $auto_approve,
        array $registrants,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'POST',
            '/meetings/'.$meeting_id.'/batch_registrants',
            [],
            [],
            [
                'auto_approve' => $auto_approve,
                'registrants' => $registrants
            ]
        );
    }

    /**
     * PATCH /live_meetings/{meetingId}/events
     *
     * Use this API to control the in-meeting recording features such as starting a recording, stopping a recording, pausing a recording, and resuming a recording.
     * This API only works for Cloud Recordings and not for local recordings.
     *
     * The meeting must be a live meeting.
     * Cloud Recording must be enabled.
     * The user using this API must either be the host or alternative host of the meeting.
     *
     * Scopes: meeting:write, meeting:write:admin, meeting:master
     * Rate Limit Label: Undefined (missing from Zoom Docs)
     *
     * @param string $meeting_id The meeting ID.
     * @param string $method The method that you would like to control. You can use `recording.start`, `recording.stop`, `recording.pause`, and `recording.resume`.
     * @return array|Exception
     * @throws Exception
     */
    public static function useInMeetingRecordingControl(
        string $meeting_id,
        string $method,
    ): array|Exception
    {
        if($method !== 'recording.start' && $method !== 'recording.stop' && $method !== 'recording.pause' && $method !== 'recording.resume') throw new Exception ('Unknown in-meeting recording control method.');
        return ZoomKit::returnResponse(
            'PATCH',
            '/live_meetings/'.$meeting_id.'/events',
            [],
            [],
            [
                'method' => $method,
            ]
        );
    }

    /**
     * POST /meetings/{meetingId}/batch_polls
     *
     * Polls allow the meeting host to survey attendees.
     * Use this API to create batch polls for a meeting.
     *
     * Scopes: meeting:write:admin meeting:write
     * Rate Limit Label: Light
     *
     * Meeting must be a scheduled meeting. Instant meetings do not have polling features enabled.
     *
     * @param string $meeting_id The meeting ID.
     * @param array $polls Information about the meeting's polls as an array. See docs page for schema.
     * @return array|Exception
     * @throws Exception
     */
    public static function performBatchPollCreation(
        string $meeting_id,
        array $polls,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'POST',
            '/meetings/'.$meeting_id.'/batch_polls',
            [],
            [],
            [
                'polls' => $polls,
            ]
        );
    }

    /**
     * GET /users/{userId}/meeting_templates
     *
     * Use this API to list meeting templates that are available to be used by a user. For user-level apps, pass the me value instead of the userId parameter.
     *
     * Scopes: meeting:read, meeting:read:admin
     * Rate Limit Label: Medium
     *
     * @param string $user_id ID of the User. For user-level apps, pass the `me` value instead.
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetingTemplates(
        string $user_id
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/users/'.$user_id.'/meeting_templates'
        );
    }

    /**
     * POST /meetings/{meetingId}/invite_links
     *
     * Use this API to create a batch of invitation links for a meeting.
     *
     * Scopes: meeting:write:admin, meeting:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID.
     * @param array $attendees The attendees list as an array of... only names? I'm not sure if that's right Zoom but that's what the Docs say.
     * @param int $ttl Invite link's expiration time in seconds. Default 7200 seconds or 120 minutes or 2 hours.
     * @return array|Exception
     * @throws Exception
     */
    public static function createMeetingInviteLinks(
        string $meeting_id,
        array $attendees,
        int $ttl = 7200,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'POST',
            '/meetings/'.$meeting_id.'/invite_links',
            [],
            [],
            [
                'ttl' => $ttl,
                'attendees' => $attendees
            ]
        );
    }

}
