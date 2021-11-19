<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class ZoomKitArchiving extends ZoomKit {
    /**
     * ZoomKit APIs for the Archiving Section of Zoom API
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
     * GET /archive_files
     *
     * Use this API to retrieve archived meeting or webinar files of an account.
     *
     * Zoom’s archiving solution allows account administrators to set up an automated mechanism to record, collect, and archive meeting data to a 3rd-party platform of their choice to satisfy FINRA and/or other compliance requirements.
     *
     * Scopes: recording:read:admin
     * Rate Limit Label: Medium
     *
     * You must follow a prior enablement process to use this feature.
     *
     * @param Carbon|null $from List meetings from this Carbon date. Maximum range is one week.
     * @param Carbon|null $to List meetings up to this Carbon date. Default is today.
     * @param int|null $page_size The number of records returned from the call, minimum 30, maximum 300.
     * @param string|null $next_page_token The next page token is used to paginate through large set results. A next page token will be returned when the available results exceed the current page size. Expires in 15 minutes.
     * @return array|Exception
     * @throws Exception
     */
    public static function listArchivedFiles(
        ?Carbon $from = null,
        ?Carbon $to = null,
        ?int $page_size = 30,
        ?string $next_page_token = null
    ): array|Exception
    {
        if(!$to) $to = Carbon::today();
        if(!$from) $from = Carbon::today()->subWeek();

        return ZoomKit::returnResponse(
            'GET',
            '/archive_files',
            [
                'from' => ($from ? $from->format('Y-m-d').'\'T\''.$from->format('H:i:s').'\'Z\'' : ''),
                'to' => ($to ? $to->format('Y-m-d').'\'T\''.$to->format('H:i:s').'\'Z\'' : ''),
                'page_size' => $page_size,
                'next_page_token' => $next_page_token
            ]
        );
    }

    /**
     * GET /past_meetings/{meetingUUID}/archive_files
     *
     * List the archived recording files of the specific meeting instance.
     *
     * Scopes: recording:read
     * Rate Limit Label: Light
     *
     * You must follow a prior enablement process to use this feature.
     *
     * @param string $meeting_UUID The meeting's universally unique identifier. Each meeting instance generates a new UUID. For example, after a meeting ends, a new UUID is generated for the next instance. If the UUID begins with / or contains a //, you must double-encode the meeting UUID when using the meeting UUID for other calls.
     * @return array|Exception
     * @throws Exception
     */
    public static function getMeetingArchivedFiles(
        string $meeting_UUID,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/past_meetings/'.$meeting_UUID.'/archive_files',
        );
    }
}
