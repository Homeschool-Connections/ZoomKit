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

    

}
