<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class ZoomKitMeetingsExtras extends ZoomKit {
    /**
     * ZoomKit Extra APIs for the Meetings Section of Zoom API
     * These APIs are not stock Zoom APIs but are abstractions on them.
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
     * POST /users/{userId}/meetings
     *
     * Use this API to create an Instant meeting for a user.
     * For user-level apps, pass the `me` value instead of the userId parameter.
     **
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
     * You can call this API in the cleanest way possible using PHP 8's new named arguments.
     *
     * @param string $user_id ID or Email Address of the User. For user-level apps, pass the `me` value instead.
     * @param string $topic The meeting's topic.
     * @param string|null $schedule_for Email address or User ID to schedule the meeting for. If undefined, re-uses the current user ID.
     * @param string|null $password The password to join the meeting. Max 10 chars, only alphanumeric and @-_* characters permitted. If minimum password settings are set on the tenant, this password must comply. You can use "Get User Settings" or "Get Account Settings" APIs to retrieve those requirements.
     * @param bool $default_password Whether to generate a default password with the user's settings. Default false. If true and user has the PMI setting enabled with a password, then the user's meetings will use the PMI password. It will not use a default password.
     * @param string|null $agenda The meeting's agenda. Maximum length 2000 characters.
     * @param array|null $tracking_fields Information about the meeting's tracking fields.
     * @param array|null $settings Information about the meeting's settings.
     * @param string|null $template_id The account admin meeting template ID with which to schedule a meeting using a template.
     * @return array|Exception
     * @throws Exception
     */
    public static function createInstantMeeting(
        string $user_id,
        string $topic,
        ?string $agenda = null,
        ?string $password = null,
        bool $default_password = false,
        ?array $tracking_fields = null,
        ?array $settings = null,
        ?string $schedule_for = null,
        ?string $template_id = null,
    ): array|Exception
    {
        return ZoomKitMeetings::createMeeting(
            user_id: $user_id,
            topic: $topic,
            agenda: $agenda,
            type: 1,
            password: $password,
            default_password: $default_password,
            tracking_fields: $tracking_fields,
            settings: $settings,
            schedule_for: $schedule_for,
            template_id: $template_id,
        );
    }

    /**
     * POST /users/{userId}/meetings
     *
     * Use this API to create a Scheduled meeting for a user.
     * For user-level apps, pass the `me` value instead of the userId parameter.
     **
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
     * You can call this API in the cleanest way possible using PHP 8's new named arguments.
     *
     * @param string $user_id ID or Email Address of the User. For user-level apps, pass the `me` value instead.
     * @param string $topic The meeting's topic.
     * @param string|null $agenda The meeting's agenda. Maximum length 2000 characters.
     * @param Carbon $start_time The meeting's start time. Provided to Zoom in GMT (we don't implement the specific timezone option, and let Carbon handle this).
     * @param int $duration Scheduled meeting duration in minutes.
     * @param string|null $password The password to join the meeting. Max 10 chars, only alphanumeric and @-_* characters permitted. If minimum password settings are set on the tenant, this password must comply. You can use "Get User Settings" or "Get Account Settings" APIs to retrieve those requirements.
     * @param bool $default_password Whether to generate a default password with the user's settings. Default false. If true and user has the PMI setting enabled with a password, then the user's meetings will use the PMI password. It will not use a default password.
     * @param array|null $tracking_fields Information about the meeting's tracking fields.
     * @param array|null $settings Information about the meeting's settings.
     * @param bool $pre_schedule Whether to create a prescheduled meeting via the GSuite app. Default false.
     * @param string|null $schedule_for Email address or User ID to schedule the meeting for. If undefined, re-uses the current user ID.
     * @param string|null $template_id The account admin meeting template ID with which to schedule a meeting using a template.
     * @return array|Exception
     * @throws Exception
     */
    public static function createScheduledMeeting(
        string $user_id,
        string $topic,
        ?string $agenda = null,
        Carbon $start_time,
        int $duration,
        ?string $password = null,
        bool $default_password = false,
        ?array $tracking_fields = null,
        ?array $settings = null,
        bool $pre_schedule = false,
        ?string $schedule_for = null,
        ?string $template_id = null,
    ): array|Exception
    {
        return ZoomKitMeetings::createMeeting(
            user_id: $user_id,
            topic: $topic,
            agenda: $agenda,
            start_time: $start_time,
            duration: $duration,
            password: $password,
            default_password: $default_password,
            tracking_fields: $tracking_fields,
            settings: $settings,
            pre_schedule: $pre_schedule,
            schedule_for: $schedule_for,
            template_id: $template_id
        );
    }

    /**
     * PUT /meetings/{meetingId}/status
     *
     * Use this API to End a meeting.
     *
     * @param int $meeting_id Meeting ID to End.
     * @return array|Exception
     * @throws Exception
     */
    public static function endMeeting(
        int $meeting_id,
    ): array|Exception
    {
        return ZoomKitMeetings::updateMeetingStatus(
            meeting_id: $meeting_id,
            action: 'end'
        );
    }
}
