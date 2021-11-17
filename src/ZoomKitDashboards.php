<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;

final class ZoomKitDashboards extends ZoomKit {

    /**
     * ZoomKit for the Dashboards Section of Zoom API
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
     * GET /metrics/meetings
     *
     * List total live or past meetings that occurred during a specified period of time.
     * This overview will show if features such as audio, video, screen sharing, and recording were being used in the meeting.
     * You can also see the license types of each user on your account.
     *
     * You can specify a monthly date range for the dashboard data using the from and to query parameters.
     * The month should fall within the last six months.
     * Scopes: dashboard_meetings:read:admin
     * Rate Limit Label: Resource-intensive
     *
     * @param string $type Must be live (default), pastOne, or past
     * @param Carbon|null $from List meetings from this Carbon date
     * @param Carbon|null $to List meetings up to this Carbon date
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @param bool $tracking_fields Include tracking fields of each meeting
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetings(
        string $type = 'live',
        ?Carbon $from = null,
        ?Carbon $to = null,
        int $page_size = 30,
        string $next_page_token = null,
        bool $tracking_fields = false
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'pastOne' && $type !== 'past') throw new Exception('Invalid meeting type.');
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        $include_fields = ($tracking_fields ? 'tracking_fields' : '');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/meetings',
            [
                'type' => $type,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'include_fields' => $include_fields,
            ]
        );
    }

    /**
     * GET /metrics/meetings/{meetingId}
     *
     * Get details on live or past meetings.
     * This overview will show if features such as audio, video, screen sharing, and recording were being used in the meeting.
     * You can also see the license types of each user on your account.
     *
     * You can specify a monthly date range for the dashboard data using the from and to query parameters.
     * The month should fall within the last six months.
     *
     * Scopes: dashboard_meetings:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $meeting_id The meeting's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), past, or pastOne.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingDetails(
        string $meeting_id,
        string $type = 'live',
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'pastOne' && $type !== 'past') throw new Exception('Invalid meeting type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/meetings/'.$meeting_id,
            [
                'type' => $type,
            ],
        );
    }

    /**
     * GET /metrics/meetings/{meetingId}/participants
     *
     * Use this API to return a list of participants from live or past meetings.
     *
     * If you do not provide the type query parameter, the default value will be set to the live value.
     * This API will only return metrics for participants in a live meeting, if any exist.
     *
     * Scopes: dashboard_meetings:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $meeting_id The meeting's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), past, or pastOne.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @param bool $registrant_id Enable if you would like to see the unique identifier of a meeting registrant
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingParticipants(
        string $meeting_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
        bool $registrant_id = false,
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'pastOne' && $type !== 'past') throw new Exception('Invalid meeting type.');
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        $include_fields = ($registrant_id ? 'registrant_id' : '');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/meetings/'.$meeting_id.'/participants',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'include_fields' => $include_fields,
            ],
        );
    }

    /**
     * GET /metrics/meetings/{meetingId}/participants/{participantId}/qos
     *
     * Use this API to return the quality of service (QoS) report for participants from live or past meetings.
     * The data returned indicates the connection quality for sending/receiving video, audio, and shared content.
     * The API returns this data for either the API request or when the API request was last received.
     *
     * When the sender sends data, a timestamp is attached to the sender’s data packet.
     * The receiver then returns this timestamp to the sender.
     * This helps determine the upstream and downstream latency, which includes the application processing time.
     * The latency data returned is the five-second average and five-second maximum.
     * This API will not return data if there is no data being sent or received at the time of request.
     *
     * Note:
     * This API may return empty values for participants’ user_name, ip_address, location, and email responses when the account calling this API:
     * * Does not have a signed HIPAA business associate agreement (BAA).
     * * Is a legacy HIPAA BAA account.
     *
     * Scopes: dashboard_meetings:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $meeting_id The meeting's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $participant_id The ID of the participant to get QoS data on.
     * @param string $type Must be live (default), or past.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingParticipantQoS(
        string $meeting_id,
        string $participant_id,
        string $type = 'live',
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid meeting type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/meetings/'.$meeting_id.'/participants/'.$participant_id.'/qos',
            [
                'type' => $type,
            ],
        );
    }

    /**
     * GET /metrics/meetings/{meetingId}/participants/qos
     *
     * Use this API to return a list of meeting participants from live or past meetings and their quality of service received during the meeting.
     * The data returned indicates the connection quality for sending/receiving video, audio, and shared content.
     *
     * Note:
     * This API may return empty values for participants’ user_name, ip_address, location, and email responses when the account calling this API:
     * * Does not have a signed HIPAA business associate agreement (BAA).
     * * Is a legacy HIPAA BAA account.
     *
     * Scopes: dashboard_meetings:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $meeting_id The meeting's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), or past.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetingParticipantsQoS(
        string $meeting_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid meeting type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/meetings/'.$meeting_id.'/participants/qos',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * GET /metrics/meetings/{meetingId}/participants/sharing
     * Retrieve the sharing and recording details of participants from live or past meetings.
     *
     * Scopes: dashboard_meetings:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $meeting_id The meeting's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), or past.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingSharingRecordingDetails(
        string $meeting_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid meeting type.');
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/meetings/'.$meeting_id.'/participants/sharing',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * GET /metrics/webinars
     *
     * List all the live or past webinars from a specified period of time.
     *
     * Scopes: dashboard_webinars:read:admin
     * Rate Limit Label: Resource-intensive
     *
     * @param string $type Must be live (default), or past
     * @param Carbon|null $from List webinars from this Carbon date
     * @param Carbon|null $to List webinars up to this Carbon date
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listWebinars(
        string $type = 'live',
        ?Carbon $from = null,
        ?Carbon $to = null,
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid webinar type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/webinars',
            [
                'type' => $type,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ]
        );
    }

    /**
     * GET /metrics/webinars/{webinarId}
     *
     * Retrieve details from live or past webinars.
     *
     * Scopes: dashboard_webinars:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $webinar_id The webinar's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), or past.
     * @return array|Exception
     * @throws Exception
     */
    public static function getWebinarDetails(
        string $webinar_id,
        string $type = 'live',
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid webinar type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/webinars/'.$webinar_id,
            [
                'type' => $type,
            ],
        );
    }

    /**
     * GET /metrics/webinars/{webinarId}/participants
     *
     * Use this API to return information about participants from live or past webinars.
     *
     * Note:
     * This API may return empty values for participants’ user_name, ip_address, location, and email responses when the account calling this API:
     *
     * * Does not have a signed HIPAA business associate agreement (BAA).
     * * Is a legacy HIPAA BAA account.
     *
     * Scopes: dashboard_webinars:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $webinar_id The webinar's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), or past.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @param bool $registrant_id Enable if you would like to see the unique identifier of a meeting registrant
     * @return array|Exception
     * @throws Exception
     */
    public static function getWebinarParticipants(
        string $webinar_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
        bool $registrant_id = false,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        $include_fields = ($registrant_id ? 'registrant_id' : '');
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid webinar type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/webinars/'.$webinar_id.'/participants',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'include_fields' => $include_fields,
            ],
        );
    }

    /**
     * GET /metrics/webinars/{webinarId}/participants/{participantId}/qos
     *
     * Use this API to return the quality of service (QoS) for participants during live or past webinars.
     * This data returned indicates the connection quality for sending/receiving video, audio, and shared content.
     * The API returns this data for either the API request or when the API request was last received.
     *
     * When the sender sends its data, a timestamp is attached to the sender’s data packet.
     * The receiver then returns this timestamp to the sender.
     * This helps determine the upstream and downstream latency, which includes the application processing time.
     * The latency data returned is the five second average and five second maximum.
     *
     * This API will not return data if there is no data being sent or received at the time of request.
     *
     * Note:
     * This API may return empty values for participants’ user_name, ip_address, location, and email responses when the account calling this API:
     * * Does not have a signed HIPAA business associate agreement (BAA).
     * * Is a legacy HIPAA BAA account.
     *
     * Scopes: dashboard_webinars:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $webinar_id The webinar's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $participant_id The ID of the participant to get QoS data on.
     * @param string $type Must be live (default), or past.
     * @return array|Exception
     * @throws Exception
     */
    public static function getWebinarParticipantQoS(
        string $webinar_id,
        string $participant_id,
        string $type = 'live',
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid webinar type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/webinars/'.$webinar_id.'/participants/'.$participant_id.'/qos',
            [
                'type' => $type,
            ],
        );
    }

    /**
     * GET /metrics/webinars/{webinarID}/participants/qos
     *
     * Use this API to return a list of webinar participants from live or past webinars and the quality of service they received during the webinar.
     * The data returned indicates the connection quality for sending/receiving video, audio, and shared content.
     *
     * Note:
     * This API may return empty values for participants’ user_name, ip_address, location, and email responses when the account calling this API:
     * * Does not have a signed HIPAA business associate agreement (BAA).
     * * Is a legacy HIPAA BAA account.
     *
     * Scopes: dashboard_webinars:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $webinar_id The webinar's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), or past.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listWebinarParticipantsQoS(
        string $webinar_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid webinar type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/webinars/'.$webinar_id.'/participants/qos',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * GET /metrics/webinars/{webinarId}/participants/sharing
     * Retrieve the sharing and recording details of participants from live or past meetings.
     *
     * Scopes: dashboard_webinars:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $webinar_id The webinar's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), or past.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getWebinarSharingRecordingDetails(
        string $webinar_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid webinar type.');
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/webinars/'.$webinar_id.'/participants/sharing',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * GET /metrics/zoomrooms
     * List information on all Zoom Rooms in an account
     *
     * Scopes: dashboard_zr:read:admin
     * Rate Limit Label: Resource-Intensive
     *
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param int|null $page_number The page number of the current page in the returned results. (Believed to be a Zoom Documentation error.)
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listZoomRooms(
        int $page_size = 30,
        int $page_number = null,
        string $next_page_token = null,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/zoomrooms',
            [
                'page_size' => $page_size,
                'page_number' => $page_number,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * GET /metrics/zoomrooms/{zoomroomId}
     * The Zoom Rooms dashboard metrics lets you know the type of configuration a Zoom room has and details on the meetings held in that room.
     * Use this API to retrieve information on a specific room.
     *
     * Scopes: dashboard_zr:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $zoom_room_id The Zoom Room's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param Carbon|null $from List Zoom Room activity from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Zoom Room activity up to this Carbon date. Should be no greater than 1 month after From.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getZoomRoomsDetails(
        string $zoom_room_id,
        ?Carbon $from = null,
        ?Carbon $to = null,
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/zoomrooms/'.$zoom_room_id,
            [
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/crc
     *
     * A Cloud Room Connector allows H.323/SIP endpoints to connect to a Zoom meeting.
     *
     * Use this API to get the hour by hour CRC Port usage for a specified period of time.
     * We will provide the report for a maximum of one month.
     * For example, if “from” is set to “2017-08-05” and “to” is set to “2017-10-10”, we will adjust “from” to “2017-09-10”.
     *
     * Scopes: dashboard_crc:read:admin
     * Rate Limit Label: Heavy
     *
     * @param Carbon|null $from List CRC usage from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List CRC usage up to this Carbon date. Should be no greater than 1 month after From.
     * @return array|Exception
     * @throws Exception
     */
    public static function getCRCPortUsage(
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/crc',
            [
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/im
     *
     * Get metrics on how users are utilizing the Zoom Chat client.
     * You can specify a monthly date range for the dashboard data using the from and to query parameters.
     * The month should fall within the last six months.
     *
     * Deprecated: Zoom will completely deprecate this endpoint in a future release.
     * You can continue using this endpoint to query data for messages sent before July 1, 2021.
     * To get metrics on chat messages sent on and after July 1, 2021, use the Get Chat Metrics API.
     *
     * Because this API is deprecated, ZoomKit does not implement and has never implemented this API.
     * You can still use it, but you'll need to program it yourself.
     *
     * Scopes: dashboard_im:read:admin
     * Rate Limit Label: Resource-intensive
     *
     * @throws Exception
     */
    public static function getIMMetrics() {
        throw new Exception('Not implemented. IM Metrics is a deprecated Zoom API. You should use Chat Metrics API instead.');
    }

    /**
     * GET /metrics/chat
     * Get metrics for how users are utilizing Zoom Chat to send messages.
     *
     * Use the from and to query parameters to specify a monthly date range for the dashboard data. The monthly date range must be within the last six months.
     *
     * Note: To query chat metrics from July 1, 2021 and later, use this endpoint instead of the Get IM metrics API.
     *
     * Scope: dashboard_im:read:admin
     * Rate Limit Label: Resource-intensive
     *
     *
     * @param Carbon|null $from List Chat activity from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Chat activity up to this Carbon date. Should be no greater than 1 month after From.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getChatMetrics(
        ?Carbon $from = null,
        ?Carbon $to = null,
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/chat',
            [
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/client/feedback
     *
     * Use this API to return Zoom meetings client feedback survey results.
     * You can specify a monthly date range for the Dashboard data using the from and to query parameters.
     * The month should fall within the last six months.
     *
     * Scopes: dashboard_home:read:admin
     * Rate Limit Label: Heavy
     *
     * The "Feedback to Zoom" option must be enabled.
     *
     * @param Carbon|null $from List Client Feedback from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Client Feedback up to this Carbon date. Should be no greater than 1 month after From.
     * @return array|Exception
     * @throws Exception
     */
    public static function listMeetingsClientFeedback(
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/client/feedback',
            [
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/zoomrooms/issues
     *
     * Get top 25 issues of Zoom Rooms.
     *
     * Scopes: dashboard_zr:read:admin
     * Rate Limit Label: Heavy
     *
     * @param Carbon|null $from List Zoom Room Issues from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Zoom Room Issues up to this Carbon date. Should be no greater than 1 month after From.
     * @return array|Exception
     * @throws Exception
     */
    public static function getTop25IssuesOfZoomRooms(
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/zoomrooms/issues',
            [
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/issues/zoomrooms
     *
     * Get information on top 25 Zoom Rooms with issues in a month.
     * The month specified with the “from” and “to” range should fall within the last six months.
     *
     * Scopes: dashboard_home:read:admin
     * Rate Limit Label: Heavy
     *
     * @param Carbon|null $from List Zoom Room Issues from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Zoom Room Issues up to this Carbon date. Should be no greater than 1 month after From.
     * @return array|Exception
     * @throws Exception
     */
    public static function getTop25ZoomRoomsWithIssues(
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/issues/zoomrooms',
            [
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/issues/zoomrooms/{zoomroomId}
     * Use this API to return information about the Zoom Rooms in an account with issues, such as disconnected hardware or bandwidth issues.
     * You can specify a monthly date range for the Dashboard data using the from and to query parameters.
     * The month should fall within the last six months.
     *
     * Scopes: dashboard_home:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $zoom_room_id The Zoom Room's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param Carbon|null $from List Zoom Room issues from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Zoom Room issues up to this Carbon date. Should be no greater than 1 month after From.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getIssuesOfZoomRooms(
        string $zoom_room_id,
        ?Carbon $from = null,
        ?Carbon $to = null,
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/issues/zoomrooms/'.$zoom_room_id,
            [
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/quality
     *
     * Use this API to return meeting quality score information.
     * Meeting quality scores are based on the mean opinion score (MOS).
     * The MOS measures a meeting’s quality on a scale of “Good” (5-4), “Fair” (4-3), “Poor” (3-2), or “Bad” (2-1).
     *
     * Scopes: dashboard_home:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $type The type of meeting score to query, either meeting (default) or participants.
     * @param Carbon|null $from List Meeting Quality scores from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Meeting Quality scores up to this Carbon date. Should be no greater than 1 month after From.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingQualityScores(
        string $type = 'meeting',
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();
        if($type !== 'meeting' && $type !== 'participants') throw new Exception('Invalid meeting score type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/quality',
            [
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
                'type' => $type,
            ],
        );
    }

    /**
     * GET /metrics/client/feedback/{feedbackId}
     * Retrieve detailed information on a Zoom meetings client feedback.
     * You can specify a monthly date range for the dashboard data using the from and to query parameters.
     * The month should fall within the last six months.
     *
     * Scopes: dashboard_home:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $feedback_id The Feedback's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param Carbon|null $from List Feedback from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Feedback up to this Carbon date. Should be no greater than 1 month after From.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingsClientFeedback(
        string $feedback_id,
        ?Carbon $from = null,
        ?Carbon $to = null,
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/client/feedback/'.$feedback_id,
            [
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/client/satisfaction
     *
     * If the End of Meeting Feedback Survey option is enabled, attendees will be prompted with a survey window where they can tap either the Thumbs Up or Thumbs Down button that indicates their Zoom meeting experience.
     * With this API, you can get information on the attendees’ meeting satisfaction.
     * Specify a monthly date range for the query using the from and to query parameters.
     * The month should fall within the last six months.
     *
     * To get information on the survey results with negative experiences (indicated by Thumbs Down), use the Get Zoom Meetings Client Feedback API.
     *
     * Scopes: dashboard:read:admin
     * Rate Limit Label: Heavy
     *
     * @param Carbon|null $from List Client Satisfaction from this Carbon date. Should be no less than 1 month before To.
     * @param Carbon|null $to List Client Satisfaction up to this Carbon date. Should be no greater than 1 month after From.
     * @return array|Exception
     * @throws Exception
     */
    public static function listClientMeetingSatisfaction(
        ?Carbon $from = null,
        ?Carbon $to = null,
    ): array|Exception
    {
        if(!$from) $from = Carbon::now()->subDay();
        if(!$to) $to = Carbon::now();

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/client/satisfaction',
            [
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
            ],
        );
    }

    /**
     * GET /metrics/meetings/{meetingId}/participants/satisfaction
     *
     * When a meeting ends, each attendee will be prompted to share their meeting experience by clicking either thumbs up or thumbs down.
     * Use this API to retrieve the feedback submitted for a specific meeting.
     * Note that this API only works for meetings scheduled after December 20, 2020.
     *
     * Scopes: dashboard_meetings:read:admin (corrected from Zoom docs, dashboard_meetings:read:admiin)
     * Rate Limit Label: Heavy
     *
     * @param string $meeting_id The meeting's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), pastOne, or past.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getPostMeetingFeedback(
        string $meeting_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');
        if($type !== 'live' && $type !== 'past' && $type !== 'pastOne') throw new Exception('Invalid webinar type.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/meetings/'.$meeting_id.'/participants/satisfaction',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * GET /metrics/webinars/{webinarId}/participants/satisfaction
     *
     * When a Webinar ends, each attendee will be prompted to share their Webinar experience by clicking either thumbs up or thumbs down.
     * Use this API to retrieve the feedback submitted for a specific webinar.
     * Note that this API only works for webinars scheduled after December 20, 2020.
     *
     * NOTE: Zoom docs say `pastOne` is an acceptable $type, but the other webinar APIs don't support it.
     * This is almost certainly a Zoom documentation error, so `pastOne` is not permitted here.
     *
     * Scopes: dashboard_webinars:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $webinar_id The webinar's ID or UUID. If UUID beginning with / or //, must be double-encoded.
     * @param string $type Must be live (default), or past.
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function getPostWebinarFeedback(
        string $webinar_id,
        string $type = 'live',
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if($type !== 'live' && $type !== 'past') throw new Exception('Invalid webinar type.');
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/metrics/webinars/'.$webinar_id.'/participants/satisfaction',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ],
        );
    }
}
