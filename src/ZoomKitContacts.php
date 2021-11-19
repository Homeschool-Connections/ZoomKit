<?php

namespace HSC\ZoomKit;
use Carbon\Carbon;
use Exception;

final class ZoomKitContacts extends ZoomKit {
    /**
     * ZoomKit APIs for the Contacts Section of Zoom API
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
     * GET /contacts
     *
     * A user under an organization’s Zoom account has internal users listed under Company Contacts in the Zoom Client.
     * Use this API to search users that are in the company contacts of a Zoom account.
     * Using the search_key query parameter, provide either first name, last name or the email address of the user that you would like to search for.
     * Optionally, set query_presence_status to true in order to include the presence status of a contact.
     *
     * Scopes: contact:read:admin, contact:read
     * Rate Limit Label: Medium
     *
     * @param string $search_key Provide the keyword - first name, last name, or email.
     * @param bool $query_presence_status Set to `true` to include the presence status of a contact in the response. Default false.
     * @param int|null $page_size Number of records to be returned by the API call. Default and minimum 1, Maximum 25.
     * @param int|null $contact_types 1 - Zoom User (default), 2 - Auto Receptionist, 3 - Common Area Phone, 4 - Call Queue, 5 - Shared Line Group, 6 - Shared Global Directory, 7 - Shared Office Contact
     * @param string|null $next_page_token The next page token is for too-large paginated results. Expires after 15 minutes.
     * @return array|Exception
     * @throws Exception
     */
    public static function searchCompanyContacts(
        string $search_key,
        bool $query_presence_status = false,
        ?int $page_size = 1,
        ?int $contact_types = 1,
        ?string $next_page_token = null
    ): array|Exception
    {
        if($page_size) {
            if($page_size > 7) {
                throw new Exception ('Unsupported page size.');
            }
        }
        return ZoomKit::returnResponse(
            'GET',
            '/contacts',
            [
                'search_key' => $search_key,
                'query_presence_status' => $query_presence_status,
                'page_size' => $page_size,
                'contact_types' => $contact_types,
                'next_page_token' => $next_page_token
            ]
        );
    }

    /**
     * GET /chat/users/me/contacts
     *
     * A user under an organization’s Zoom account has internal users listed under Company Contacts in the Zoom Client.
     * A Zoom user can also add another Zoom user as a contact.
     * Call this API to list all the contacts of a Zoom user.
     * Zoom contacts are categorized into “company contacts” and “external contacts”.
     * You must specify the contact type in the type query parameter.
     * If you do not specify, by default, the type will be set as company contact.
     *
     * Note: This API only supports user-managed OAuth app.
     *
     * Scope: chat_contact:read
     * Rate Limit Label: Medium
     *
     * @param string|null $type The type of contact, either `company` or `external`.
     * @param int|null $page_size The number of records returned with a single API call. Minimum and default 10, Maximum 50.
     * @param string|null $next_page_token The next page token used for paginating through large requests. Expires after 15 minutes.
     * @return array|Exception
     * @throws Exception
     */
    public static function listUserContacts(
        ?string $type = 'company',
        ?int $page_size = 10,
        ?string $next_page_token = null
    ): array|Exception
    {
        if($type !== null && $type !== 'company' && $type !== 'external') throw new Exception ('Unsupported contact type.');
        if($page_size) {
            if($page_size > 50 || $page_size < 10) {
                throw new Exception ('Unsupported page size.');
            }
        }
        return ZoomKit::returnResponse(
            'GET',
            '/chat/users/me/contacts',
            [
                'type' => $type,
                'page_size' => $page_size,
                'next_page_token' => $next_page_token
            ]
        );
    }

    /**
     * GET /chat/users/me/contacts/{contactId}
     *
     * A user under an organization’s Zoom account has internal users listed under Company Contacts in the Zoom Client.
     * A Zoom user can also add another Zoom user as a contact.
     * Call this API to get information on a specific contact of the Zoom user.
     *
     * Note: This API only supports user-managed OAuth app.
     *
     * Scope: chat_contact:read
     * Rate Limit Label: Medium
     *
     * @param string $contact_id ID of the Contact to get information for.
     * @param bool $query_presence_status Set to `true` to include the presence status of a contact in the response. Default false.
     * @return array|Exception
     * @throws Exception
     */
    public static function getUserContactDetails(
        string $contact_id,
        bool $query_presence_status = false
    ): array|Exception
    {
        return ZoomKit::returnResponse(
            'GET',
            '/chat/users/me/contacts/'.$contact_id,
            [
                'query_presence_status' => $query_presence_status
            ]
        );
    }
}
