<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class RoomsDevices extends Response {
    /**
     * ZoomKit APIs for the Zoom Rooms Devices Section of Zoom API
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
     * PUT /rooms/{roomId}/devices/{deviceId}/app_version
     *
     * Use this API to upgrade or downgrade the version of your installed Zoom Rooms app on your Mac or Windows device.
     *
     * Scopes: room:write:admin
     * Rate Limit Label: Unspecified
     *
     * The Zoom Rooms software must be installed on a Mac or a Windows device. This API does not support other devices.
     *
     * This is a weird Zoom API. It's the only function on the Zoom Rooms Devices category.
     * Come on Zoom - maybe this needs to be reorganized.
     *
     * @param string $room_id ID of the Room the device is located in.
     * @param string $device_id ID of the device, running Windows or macOS.
     * @param string $action Can be `upgrade`, `downgrade`, or `cancel`.
     * @return array|Exception
     * @throws Exception
     */
    public static function changeZoomRoomsAppVersion(
        string $room_id,
        string $device_id,
        string $action
    ): array|Exception
    {
        if($action !== 'upgrade' && $action !== 'downgrade' && $action !== 'cancel') throw new Exception ('Unsupported Zoom Rooms device action.');

        return Response::returnResponse(
            'PUT',
            '/rooms/'.$room_id.'/devices/'.$device_id.'/app_version',
            [],
            [],
            [
                'action' => $action
            ]
        );
    }
}
