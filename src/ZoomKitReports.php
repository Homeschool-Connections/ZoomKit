<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class ZoomKitReports extends ZoomKit
{
    /**
     * ZoomKit APIs for the Reports Section of Zoom API
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
     * GET /report/daily
     *
     * Retrieve daily report accessing the account-wide usage of Zoom services for each day in a given month.
     * It lists the number of new users, meetings, participants, and meeting minutes.
     *
     * Scopes: report:read:admin
     * Rate Limit Label: Heavy
     *
     * @param int $month Month for the report.
     * @param int $year Year for the report.
     * @throws Exception
     */
    public static function getDailyUsageReport(
        int $year,
        int $month
    ): array|Exception
    {
        if($month < 1 || $month > 12) throw new Exception ('Invalid month.');

        return ZoomKit::returnResponse(
            'GET',
            '/report/daily',
            [
                'year' => $year,
                'month' => $month
            ]
        );
    }

    /**
     * GET /report/users
     *
     * A user is considered to be an active host during the month specified in the “from” and “to” range, if the user has hosted at least one meeting during this period.
     * If the user did not host any meetings during this period, the user is considered to be inactive.
     *
     * The Active Hosts report displays a list of meetings, participants, and meeting minutes for a specific time range, up to one month.
     * The month should fall within the last six months.
     *
     * The Inactive Hosts report pulls a list of users who were not active during a specific period of time.
     * Use this API to retrieve an active or inactive host report for a specified period of time.
     * The time range for the report is limited to a month and the month should fall under the past six months.
     *
     * You can specify the type of report and date range using the query parameters.
     *
     * Scopes: report:read:admin
     * Rate Limit Label: Heavy
     *
     * @param string $type Active or inactive hosts. Can be `active` or `inactive`.
     * @param Carbon|null $from Start date, no further than 1 month from `to`.
     * @param Carbon|null $to End date, no further than 1 month from `from`.
     * @param int|null $page_size The number of records returned from the call. Min 30, Max 300, Default 30.
     * @param string|null $next_page_token Used for paginating through results. Expires in 15 minutes.
     * @return array|Exception
     */
    public static function getActiveInactiveHostReports(
        string $type,
        ?Carbon $from,
        ?Carbon $to,
        ?int $page_size = 30,
        ?string $next_page_token = null,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/report/users',
            [
                'type' => $type,
                'from' => ($from ? $from->format('Y-m-d') : ''),
                'to' => ($to ? $to->format('Y-m-d') : ''),
                'page_size' => $page_size,
                'next_page_token' => $next_page_token
            ]
        );
    }
}
