<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class ZoomKitCloudRecordings extends ZoomKit {
    /**
     * ZoomKit APIs for the Cloud Recordings Section of Zoom API
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
     * GET /users/{userId}/recordings
     *
     * Use this API to list all cloud recordings of a user.
     * For user-level apps, pass the me value instead of the userId parameter.
     *
     * Note: To access a user's password protected cloud recording, add an access_token parameter to the download URL and provide either the JWT or the user's OAuth access token as the value of the access_token parameter.
     *
     * When a user records a meeting or a webinar by choosing the Record to the Cloud option, the video, audio, and chat text are recorded in the Zoom cloud.
     *
     * Scopes: recording:read:admin, recording:read
     * Rate Limit Label: Medium
     *
     * @param string $user_id ID or email of the user who possesses the recording. For user-level apps, instead pass the `me` value.
     * @param int|null $page_size The number of records returned from the call, minimum 30, maximum 300.
     * @param string|null $next_page_token The next page token is used to paginate through large set results. A next page token will be returned when the available results exceed the current page size. Expires in 15 minutes.
     * @param string|null $mc Query metadata of recording if an on-premise meeting connector was used for the meeting.
     * @param bool $trash List recordings from the trash. Default is false. If true, be sure to set the trash_type variable.
     * @param Carbon|null $from List meetings from this Carbon date. Default is the current date. Trash files cannot be sorted by date. Maximum range is one month.
     * @param Carbon|null $to List meetings up to this Carbon date.
     * @param string $trash_type The type of cloud recording to retrieve from trash. Can be either `meeting_recordings` (default) or `recording_file`.
     * @param string|null $meeting_id Meeting ID to scan for.
     * @return array|Exception
     * @throws Exception
     */
    public static function listAllRecordings(
        string $user_id,
        ?int $page_size = 30,
        ?string $next_page_token = null,
        ?string $mc = null,
        bool $trash = false,
        ?Carbon $from = null,
        ?Carbon $to = null,
        string $trash_type = 'meeting_recordings',
        ?string $meeting_id = null,
    ): array|Exception
    {
        if($trash_type !== 'meeting_recordings' && $trash_type !== 'recording_file') throw new Exception('Invalid trash type.');

        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/users/'.$user_id.'/recordings',
            [
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'mc' => $mc,
                'trash' => $trash,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
                'trash_type' => $trash_type,
                'meeting_id' => $meeting_id
            ]
        );
    }

    /**
     * GET /meetings/{meetingId}/recordings
     *
     * Get all the recordings from a meeting or Webinar instance.
     * The recording files can be downloaded via the download_url property listed in the response.
     *
     * To access a password-protected cloud recording, add an access_token parameter to the download URL and provide OAuth access token or JWT as the access_token value.
     *
     * Scopes: recording:read:admin, recording:read
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to get recordings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @param string|null $include_fields Get the `download_access_token` field for downloading recordings.
     * @param int|null $ttl TTL of the `download_access_token`. Only valid if `include_fields` contains `download_access_token`. Max 604800.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingRecordings(
        string $meeting_id,
        ?string $include_fields = null,
        ?int $ttl = null,
    ): array|Exception
    {
        if($include_fields !== null && $include_fields !== 'download_access_token') throw new Exception ('Unknown Include Fields option.');
        if($ttl) {
            if($ttl > 604800) throw new Exception ('TTL larger than acceptable maximum of 604800.');
        }

        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/recordings',
            [
                'include_fields' => $include_fields,
                'ttl' => $ttl
            ]
        );
    }

    /**
     * DELETE /meetings/{meetingId}/recordings
     *
     * Delete all recording files of a meeting.
     *
     * Scopes: recording:write:admin, recording:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to delete recordings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @param string $action Either `trash` (move to trash) or `delete` (permanent deletion).
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteMeetingRecordings(
        string $meeting_id,
        string $action
    ): array|Exception
    {
        if($action !== 'trash' && $action !== 'delete') throw new Exception ('Unsupported delete action.');

        return ZoomKit::returnResponse(
            'DELETE',
            '/meetings/'.$meeting_id.'/recordings',
            [
                'action' => $action
            ]
        );
    }

    /**
     * DELETE /meetings/{meetingId}/recordings/{recordingId}
     *
     * Delete a specific recording file from a meeting.
     *
     * Note: To use this API, you must enable the `The host can delete cloud recordings` setting.
     * You can find this setting in the Recording tab of the Settings interface in the Zoom web portal.
     *
     * Scopes: recording:write:admin, recording:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to delete recordings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @param string $recording_id Recording ID to specifically delete from the meeting.
     * @param string $action Either `trash` (move to trash) or `delete` (permanent deletion).
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteMeetingRecordingFile(
        string $meeting_id,
        string $recording_id,
        string $action
    ): array|Exception
    {
        if($action !== 'trash' && $action !== 'delete') throw new Exception ('Unsupported delete action.');

        return ZoomKit::returnResponse(
            'DELETE',
            '/meetings/'.$meeting_id.'/recordings/'.$recording_id,
            [
                'action' => $action
            ]
        );
    }

    /**
     * PUT /meetings/{meetingId}/recordings/status
     *
     * Zoom allows users to recover recordings from trash for up to 30 days from the deletion date.
     * Use this API to recover all deleted Cloud Recordings of a specific meeting.
     *
     * This Zoom API is a questionable implementation on Zoom's part because it requires an action
     * field, but there's only one available action. Which means, what's the !@#$%^& point Zoom?
     *
     * Scopes: recording:write:admin, recording:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to recover recordings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @return array|Exception
     * @throws Exception
     */
    public static function recoverMeetingRecordings(
        string $meeting_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'PUT',
            '/meetings/'.$meeting_id.'/recordings/status',
            [],
            [],
            [
                'action' => 'recover'
            ]
        );
    }

    

}
