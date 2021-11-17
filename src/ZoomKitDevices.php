<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class ZoomKitDevices extends ZoomKit {
    /**
     * ZoomKit for the Devices Section of Zoom API
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
     * GET /h323/devices
     * A H.323 or SIP device can make a video call to a Room Connector to join a Zoom cloud meeting.
     * A Room Connector can also call out to a H.323 or SIP device to join a Zoom cloud meeting.
     * Use this API to list all H.323/SIP Devices on a Zoom account.
     *
     * The Zoom API mentions that you can also use a `page_number` field, but that this field is
     * deprecated and will stop working eventually. Because it was deprecated when ZoomKit was written,
     * this field is not implemented.
     *
     * Scopes: h323:read:admin
     * Rate Limit Label: Medium
     *
     * @param int $page_size Number of results per page, min 30, max 300.
     * @param string|null $next_page_token Provide this token for viewing a different page in the paginated result. Expires after 15 min.
     * @return array|Exception
     * @throws Exception
     */
    public static function listH323SIPDevices(
        int $page_size = 30,
        string $next_page_token = null,
    ): array|Exception
    {
        if($page_size < 30 || $page_size > 300) throw new Exception('Page size is minimum 30, maximum 300 results.');

        return ZoomKit::returnResponse(
            'GET',
            '/h323/devices',
            [
                'page_size' => $page_size,
                'next_page_token' => $next_page_token,
            ],
        );
    }

    /**
     * POST /h323/devices
     *
     * A H.323 or SIP device can make a video call to a Room Connector to join a Zoom cloud meeting.
     * A Room Connector can also call out to a H.323 or SIP device to join a Zoom cloud meeting.
     * Use this API to add a H.323/SIP device to your Zoom account.
     *
     * Scopes: h323:write:admin
     * Rate Limit Label: Light
     *
     * @param string $name Device name
     * @param string $protocol Can be `H.323` or `SIP`.
     * @param string $ip Device IP Address
     * @param string $encryption Can be `auto`, `yes`, or `no`. Default `auto`.
     * @return array|Exception
     * @throws Exception
     */
    public static function createH323SIPDevice(
        string $name,
        string $protocol,
        string $ip,
        string $encryption = 'auto'
    ): array|Exception
    {
        if($encryption !== 'auto' && $encryption !== 'yes' && $encryption !== 'no') throw new Exception('Unsupported encryption status.');
        if($protocol !== 'H.323' && $protocol !== 'SIP') throw new Exception('Unsupported device protocol.');

        return ZoomKit::returnResponse(
            'POST',
            '/h323/devices',
            [],
            [],
            [
                'name' => $name,
                'protocol' => $protocol,
                'ip' => $ip,
                'encryption' => $encryption
            ]
        );
    }

    /**
     * PATCH /h323/devices/{deviceId}
     *
     * A H.323 or SIP device can make a video call to a Room Connector to join a Zoom cloud meeting.
     * A Room Connector can also call out to a H.323 or SIP device to join a Zoom cloud meeting.
     * Use this API to edit information of a H.323/SIP device from your Zoom account.
     *
     * Scopes: h323:write:admin
     * Rate Limit Label: Light
     *
     * @param string $device_id Device ID in Zoom to update
     * @param string|null $name Device name
     * @param string|null $protocol Can be `H.323` or `SIP`.
     * @param string|null $ip Device IP Address
     * @param string|null $encryption Can be `auto`, `yes`, or `no`.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateH323SIPDevice(
        string $device_id,
        ?string $name = null,
        ?string $protocol = null,
        ?string $ip = null,
        ?string $encryption = null
    ): array|Exception
    {
        $data = array();

        if($encryption) {
            if($encryption !== 'auto' && $encryption !== 'yes' && $encryption !== 'no') throw new Exception('Unsupported encryption status.');
        }
        if($protocol) {
            if($protocol !== 'H.323' && $protocol !== 'SIP') throw new Exception('Unsupported device protocol.');
        }

        if($name) $data['name'] = $name;
        if($protocol) $data['protocol'] = $protocol;
        if($ip) $data['ip'] = $ip;
        if($encryption) $data['encryption'] = $encryption;

        return ZoomKit::returnResponse(
            'PATCH',
            '/h323/devices/'.$device_id,
            [],
            [],
            $data
        );
    }

    /**
     * DELETE /h323/devices/{deviceId}
     *
     * A H.323 or SIP device can make a video call to a Room Connector to join a Zoom cloud meeting.
     * A Room Connector can also call out to a H.323 or SIP device to join a Zoom cloud meeting.
     * Use this API to delete a H.323/SIP device from your Zoom account.
     *
     * Scopes: h323:write:admin
     * Rate Limit Label: Light
     *
     * @param string $device_id Device ID in Zoom to delete
     * @return array|Exception
     * @throws Exception
     */
    public static function deleteH323SIPDevice(
        string $device_id,
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'DELETE',
            '/h323/devices/'.$device_id
        );
    }
}
