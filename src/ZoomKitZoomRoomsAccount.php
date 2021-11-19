<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class ZoomKitZoomRoomsAccount extends ZoomKit {
    /**
     * ZoomKit APIs for the Zoom Rooms Accounts Section of Zoom API
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
     * GET /rooms/account_profile
     *
     * Get details on the account profile of a Zoom Room.
     * This information can only be accessed by either by the Zoom Room Account Owner or a user with Zoom Rooms admin permission.
     * To get information on an individual Room Profile, use Get Zoom Room Profile API instead.
     *
     * Scopes: room:read:admin
     * Rate Limit Label: Medium
     *
     * This API, surprisingly, has no documented parameters.
     *
     * @return array|Exception
     * @throws Exception
     */
    public static function getZoomRoomAccountProfile(
        // This API, surprisingly, has no documented parameters.
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/rooms/account_profile'
        );
    }

    /**
     * PATCH /rooms/account_profile
     *
     * Update information on the account profile of a Zoom Room.
     * This information can only be accessed by either by the Zoom Room Account Owner or a user with Zoom Rooms admin permission.
     * To update information on an individual Room Profile, use Update Zoom Room Profile API instead.
     *
     * Scopes: room:write:admin
     * Rate Limit Label: Medium
     *
     * @param string|null $support_email The email address used for reporting Zoom Rooms issues.
     * @param string|null $support_phone The phone number used for reporting Zoom Rooms issues.
     * @param string|null $room_passcode 1-16 digit number or characters used to secure your Zoom Rooms application. Code must be entered to change settings or log out.
     * @param bool|null $required_code_to_ext Require code to exit out of the Zoom Rooms application or to switch between other apps.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateZoomRoomAccountProfile(
        ?string $support_email = null,
        ?string $support_phone = null,
        ?string $room_passcode = null,
        ?bool $required_code_to_ext = null,
    ): array|Exception
    {
        $data = array();

        if($room_passcode) {
            if ($room_passcode > 16) {
                throw new Exception ('Room passcode is too long.');
            } elseif ($room_passcode < 1) {
                throw new Exception ('Room passcode is too short.');
            }
        }

        if($support_email) $data['support_email'] = $support_email;
        if($support_phone) $data['support_phone'] = $support_phone;
        if($room_passcode) $data['room_passcode'] = $room_passcode;
        if($required_code_to_ext) $data['required_code_to_ext'] = $required_code_to_ext;

        return ZoomKit::returnResponse(
            'PATCH',
            '/rooms/account_profile',
            [],
            [],
            [
                'basic' => $data,
            ]
        );
    }

    /**
     * GET /rooms/account_settings
     *
     * Get details on Account Settings of a Zoom Room.
     * With this API, you can view either the Account Meeting Settings or the Alert Settings (Client Alert Settings and Notifications Settings) of the Zoom Rooms account.
     * By default, only Account Meeting Settings are returned.
     * To view only Alert Settings, specify alert as the value of the setting_type query parameter.
     *
     * Scopes: room:read:admin
     * Rate Limit Label: Medium
     *
     * @param string $setting_type Type of setting to retrieve for the Zoom Rooms account. `alert` for Alert Settings. `meeting` for Meeting Settings (default). `signage` for Digital Signage settings.
     * @return array|Exception
     * @throws Exception
     */
    public static function getZoomRoomAccountSettings(
        string $setting_type = 'meeting'
    ): array|Exception
    {
        if($setting_type !== 'meeting' && $setting_type !== 'alert' && $setting_type !== 'signage') throw new Exception ('Unsupported type of setting to retrieve.');

        return ZoomKit::returnResponse(
            'GET',
            '/rooms/account_settings',
            [
                'setting_type' => $setting_type,
            ]
        );
    }

    /**
     * PATCH /rooms/account_settings
     *
     * NOTE: This API really should be two separate ones in Zoom - it's messy!
     *
     * Update account settings applied for Zoom Rooms in a Zoom account.
     * With this API, you can update either the Account Meeting Settings or the Alert Settings (Client Alert Settings and Notifications Settings) of the Zoom Rooms account by specifying the required setting type in the setting_type parameter.
     * To update only Alert Settings, specify alert as the value of the setting_type query parameter and to update only Account Meeting Settings, specify meeting as the value of the setting_type query parameter.
     *
     * Scopes: room:write:admin
     * Rate Limit Label: Medium
     *
     * @param string $setting_type Type of setting to update for the Zoom Rooms account. `alert` for Alert Settings. `meeting` for Meeting Settings (default). `signage` for Digital Signage settings.
     * @param array $data Settings data to update. Read the Zoom docs for more information. This API does too much and is very confusing - and so I haven't tried to build automatic processing, it's bad. TODO: Revisit this later for fully native implementation.
     * @return array|Exception
     * @throws Exception
     */
    public static function updateZoomRoomAccountSettings(
        string $setting_type,
        array $data
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'PATCH',
            '/rooms/account_settings',
            [
                'setting_type' => $setting_type
            ],
            [],
            $data
        );
    }
}
