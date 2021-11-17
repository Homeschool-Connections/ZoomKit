# ZoomKit

Consume Zoom Meeting APIs easily from a Laravel application.
Licensed under MIT.

### Currently implemented APIs
See the [Zoom API documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api) for more information.

The goal is to eventually support most, if not all, of Zoom's APIs for easy consumption.

- [ ] Accounts
- [ ] Archiving
- [ ] Billing
- [ ] Chat Channels
- [ ] Chat Channels (Account-level)
- [ ] Chat Messages
- [ ] Chatbot Messages
- [ ] Contacts
- [ ] Cloud Recording
- [x] Dashboards (**fully implemented**)
- [ ] Devices
- [ ] Groups
- [ ] IM Chat
- [ ] IM Groups
- [ ] Meetings
- [ ] PAC
- [ ] Reports
- [ ] Roles
- [ ] SIP Connected Audio
- [ ] SIP Phone
- [ ] Tracking Field
- [ ] TSP
- [ ] Users
- [ ] Webinars
- [ ] Zoom Rooms
- [ ] Zoom Rooms Account
- [ ] Zoom Rooms Location
- [ ] Zoom Rooms Devices

### Calling an API

You must have `ZOOM_KEY` and `ZOOM_SECRET` entries in your `.env` file.
You can get these from the Zoom developer center as the `JWT` app type.

Once these environment variables have been added, the APIs are modeled off of Zoom's API documentation. 
For example, if you wanted to use the API at [Zoom's Dashboards > List Meetings](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardmeetings), you would do the following:

```
use HSC\ZoomKit\ZoomKitDashboards;

class MyClass {
    function list_meetings() {
        $result = ZoomKitDashboards::listMeetings();
        dump($result);
    }
}
```

In the above example, `ZoomKitDashboards` is the class containing all the APIs within the `Dashboards` section of the documentation.
All functions are named similarly to their documentation titles, with the "List meetings" article being `listMeetings()`.

Some functions support additional arguments, which is recorded in PHPdoc blocks. Dates use `Carbon` types, similar to other Laravel dates.

A very customized call of the same `listMeetings()` function might look like:

```
use HSC\ZoomKit\ZoomKitDashboards;

class MyClass {
    function list_meetings() {
        $from = Carbon::now()->subMonth();
        $to = Carbon::now();
        
        $result = ZoomKitDashboards::listMeetings(
            type: 'pastOne',
            from: $from,
            to: $to,
            page_size: 300,
        );
        
        dump($result);
    }
}
```

### Currently implemented functions
#### `ZoomKitDashboards` class
- `listMeetings()`
- `getMeetingDetails()`
- `listMeetingParticipants()`
- `getMeetingParticipantQoS()`
- `listMeetingParticipantsQoS()`
- `getMeetingSharingRecordingDetails()`
- `listWebinars()`
- `getWebinarDetails()`
- `getWebinarParticipants()`
- `getWebinarParticipantQoS()`
- `listWebinarParticipantsQoS()`
- `getWebinarSharingRecordingDetails()`
- `listZoomRooms()`
- `getZoomRoomsDetails()`
- `getCRCPortUsage()`
- `getChatMetrics()`
- `listMeetingsClientFeedback()`
- `getTop25IssuesOfZoomRooms()`
- `getTop25ZoomRoomsWithIssues()`
- `getIssuesOfZoomRooms()`
- `getMeetingQualityScores()`
- `getMeetingsClientFeedback()`
- `listClientMeetingSatisfaction()`
- `getPostMeetingFeedback()`
- `getPostWebinarFeedback()`

More to come.
