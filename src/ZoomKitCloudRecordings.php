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

    /**
     * PUT /meetings/{meetingId}/recordings/{recordingId}/status
     *
     * Zoom allows users to recover recordings from trash for up to 30 days from the deletion date.
     * Use this API to recover a single recording file from the meeting.
     *
     * This repeats the use of Zoom's pointless action field in the request body.
     *
     * Scopes: recording:write:admin, recording:write
     * Rate Limit Label: Light

     * @param string $meeting_id Meeting ID to recover recordings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @param string $recording_id  Recording ID to specifically recover from the meeting.
     * @return array|Exception
     * @throws Exception
     */
    public static function recoverSingleRecording(
        string $meeting_id,
        string $recording_id
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'PUT',
            '/meetings/'.$meeting_id.'/recordings/'.$recording_id.'/status',
            [],
            [],
            [
                'action' => 'recover'
            ]
        );
    }

    /**
     * GET /meetings/{meetingId}/recordings/settings
     *
     * Retrieve settings applied to a meeting’s Cloud Recording.
     *
     * Scopes: recording:read:admin, recording:read
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to get settings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingRecordingSettings(
        string $meeting_id
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/recordings/settings',
        );
    }

    /**
     * PATCH /meetings/{meetingId}/recordings/settings
     *
     * Update settings applied to a meeting’s Cloud Recording
     *
     * Scopes: recording:write:admin, recording:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to update settings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @param string|null $share_recording Determines how the recording is shared. Can be `publicly`, `internally`, or `none`.
     * @param bool|null $recording_authentication Whether only authenticated users can view.
     * @param string|null $authentication_option Authentication Options. (Zoom docs are really ambiguous on this one.)
     * @param string|null $authentication_domains Authentication Domains. (Zoom docs also don't say almost anything on this one.)
     * @param bool|null $viewer_download Whether a viewer can download the recording file or not.
     * @param string|null $password Enable password protection for meeting. Min 8, Max 10 characters. If password strength requirements are enabled, they must be met.
     * @param bool|null $on_demand Whether registration is required to view the recording.
     * @param int|null $approval_type Approval type for the registration. 0 = Auto Approve on Registration, 1 = Manual Approve on Registration, 2 = No Registration.
     * @param bool|null $send_email_to_host Send email to host when someone registers to view the recording. Only applies to on-demand recordings.
     * @param bool|null $show_social_share_buttons Show social share buttons on registration page. Only applies to on-demand recordings.
     * @param string|null $topic
     * @return array|Exception
     * @throws Exception
     */
    public static function updateMeetingRecordingSettings(
        string $meeting_id,
        ?string $share_recording = null,
        ?bool $recording_authentication = null,
        ?string $authentication_option = null,
        ?string $authentication_domains = null,
        ?bool $viewer_download = null,
        ?string $password = null,
        ?bool $on_demand = null,
        ?int $approval_type = null,
        ?bool $send_email_to_host = null,
        ?bool $show_social_share_buttons = null,
        ?string $topic = null
    ): array|Exception
    {
        if($share_recording !== 'publicly' && $share_recording !== 'internally' && $share_recording !== 'none' && $share_recording !== null) throw new Exception ('Unsupported Share Recording option.');
        if($approval_type !== null && $approval_type !== 0 && $approval_type !== 1 && $approval_type !== 2) throw new Exception ('Unsupported approval type.');

        $data = array();
        if($share_recording) $data['share_recording'] = $share_recording;
        if($recording_authentication) $data['recording_authentication'] = $recording_authentication;
        if($authentication_option) $data['authentication_option'] = $authentication_option;
        if($authentication_domains) $data['authentication_domains'] = $authentication_domains;
        if($viewer_download) $data['viewer_download'] = $viewer_download;
        if($password) $data['password'] = $password;
        if($on_demand) $data['on_demand'] = $on_demand;
        if($approval_type) $data['approval_type'] = $approval_type;
        if($send_email_to_host) $data['send_email_to_host'] = $send_email_to_host;
        if($show_social_share_buttons) $data['show_social_share_buttons'] = $show_social_share_buttons;
        if($topic) $data['topic'] = $topic;

        return ZoomKit::returnResponse(
            'PATCH',
            '/meetings/'.$meeting_id.'/recordings/settings',
            [],
            [],
            $data
        );
    }

    /**
     * GET /meetings/{meetingId}/recordings/registrants
     *
     * Cloud Recordings of past Zoom Meetings can be made on-demand.
     * Users should be registered to view these recordings.
     *
     * Use this API to list registrants of On-demand Cloud Recordings of a past meeting.
     *
     * Scopes: recording:read:admin, recording:read
     * Rate Limit Label: Medium
     *
     * @param string $meeting_id The meeting ID in long format, no UUIDs it seems from the documentation, but that could be an error?
     * @param string $status Registrant status to fetch. Can be `pending`, `approved` (default), or `denied`.
     * @param int|null $page_size The number of records returned from the call, minimum 30, maximum 300. Default 30.
     * @param string|null $next_page_token The next page token is used to paginate through large set results. A next page token will be returned when the available results exceed the current page size. Expires in 15 minutes.
     * @return array|Exception
     * @throws Exception
     */
    public static function listRecordingRegistrants(
        string $meeting_id,
        string $status = 'approved',
        ?int $page_size = 30,
        ?string $next_page_token = null
    ): array|Exception
    {
        if($status !== 'pending' && $status !== 'approved' && $status !== 'denied') throw new Exception ('Unsupported status type.');

        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/recordings/registrants',
            [
                'status' => $status,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token
            ]
        );
    }

    /**
     * POST /meetings/{meetingId}/recordings/registrants
     *
     * Cloud Recordings of past Zoom Meetings can be made on-demand.
     * Users should be registered to view these recordings.
     *
     * Use this API to register a user to gain access to On-demand Cloud Recordings of a past meeting.
     *
     * WARNING: There are validations on this data required that this API implementation does not attempt to handle.
     * Please read the Zoom Docs for information about approved values.
     *
     * Scopes: recording:write:admin, recording:write.
     * Rate Limit Label: Light
     *
     * @param string $meeting_id The meeting ID in long format, no UUIDs it seems from the documentation, but that could be an error?
     * @param string $email A valid email address for the registrant.
     * @param string $first_name First name of the registrant.
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
     * @return array|Exception
     * @throws Exception
     */
    public static function createRecordingRegistrant(
        string $meeting_id,
        string $email,
        string $first_name,
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

        return ZoomKit::returnResponse(
            'POST',
            '/meetings/'.$meeting_id.'/recordings/registrants',
            [],
            [],
            $data
        );
    }

    /**
     * PUT /meetings/{meetingId}/recordings/registrants/status
     *
     * A registrant can either be approved or denied from viewing the on-demand recording.
     * Use this API to update a registrant’s status.
     *
     * NOTE: Zoom Docs use punctuation and description as though this can be used to update
     * one registration at a time, but in practice the API accepts an array of registrant
     * IDs to update them all at once - thus the slightly awkward plural function name.
     *
     * Scopes: recording:write:admin, recording:write
     * Rate Limit Label: Medium
     *
     * @param string $meeting_id The meeting ID in long format, no UUIDs it seems from the documentation, but that could be an error?
     * @param string $action Either `approve` or `deny`.
     * @param array $registrants Array of registrants with IDs, see Zoom Docs for format.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateRegistrantsStatus(
        string $meeting_id,
        string $action,
        array $registrants
    ): array|Exception
    {
        if($action !== 'approve' && $action !== 'deny') throw new Exception ('Unsupported action.');

        return ZoomKit::returnResponse(
            'PUT',
            '/meetings/'.$meeting_id.'/recordings/registrants/status',
            [],
            [],
            [
                'action' => $action,
                'registrants' => $registrants
            ]
        );
    }

    /**
     * GET /meetings/{meetingId}/recordings/registrants/questions
     *
     * For on-demand meeting recordings, you can include fields with questions that will be shown to registrants when they register to view the recording.
     * Use this API to retrieve a list of questions that are displayed for users to complete when registering to view the recording of a specific meeting.
     *
     * Scopes: recording:read:admin, recording:read
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to get settings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
     * @return array|Exception
     * @throws Exception
     */
    public static function getRegistrationQuestions(
        string $meeting_id
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/meetings/'.$meeting_id.'/recordings/registrants/questions',
        );
    }

    /**
     * PATCH /meetings/{meetingId}/recordings/registrants/questions
     *
     * For on-demand meeting recordings, you can include fields with questions that will be shown to registrants when they register to view the recording.
     * Use this API to update registration questions that are to be answered by users while registering to view a recording.
     *
     * Scopes: recording:write:admin, recording:write
     * Rate Limit Label: Light
     *
     * @param string $meeting_id Meeting ID to get settings for. Can be ID or UUID. If ID is provided and not UUID, response will be for the latest instance. If UUID starts with / or contains a //, you must double-encode the UUID before request.
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
            '/meetings/'.$meeting_id.'/recordings/registrants/questions',
            [],
            [],
            [
                'questions' => $questions,
                'custom_questions' => $custom_questions
            ]
        );
    }
}
