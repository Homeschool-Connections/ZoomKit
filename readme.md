# ZoomKit

Consume Zoom Meeting APIs easily from a Laravel application.
Licensed under MIT.

### Zoom API Sections
See the [Zoom API documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api) for more information.

The goal is to eventually support most, if not all, of Zoom's APIs for easy consumption.
Currently, only the **Dashboards** section of the Zoom API is implemented.

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
- `listMeetings()` - [List meetings](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardmeetings)
- `getMeetingDetails()` - [Get meeting details](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardmeetingdetail)
- `listMeetingParticipants()` - [List meeting participants](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardmeetingparticipants)
- `getMeetingParticipantQoS()` - [Get meeting participant QoS](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardmeetingparticipantqos)
- `listMeetingParticipantsQoS()` - [List meeting participants QoS](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardmeetingparticipantsqos)
- `getMeetingSharingRecordingDetails()` - [Get sharing/recording details](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardmeetingparticipantshare)
- `listWebinars()` - [List webinars](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardwebinars)
- `getWebinarDetails()` - [Get webinar details](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardwebinardetail)
- `getWebinarParticipants()` - [Get webinar participants](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardwebinarparticipants)
- `getWebinarParticipantQoS()` - [Get webinar participant QoS](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardwebinarparticipantqos)
- `listWebinarParticipantsQoS()` - [List webinar participant QoS](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardwebinarparticipantsqos)
- `getWebinarSharingRecordingDetails()` - [Get sharing/recording details](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardwebinarparticipantshare)
- `listZoomRooms()` - [List Zoom Rooms](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardzoomrooms)
- `getZoomRoomsDetails()` - [Get Zoom Rooms details](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardzoomroom)
- `getCRCPortUsage()` - [Get CRC port usage](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardcrc)
- `getChatMetrics()` - [Get chat metrics](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardchat)
- `listMeetingsClientFeedback()` - [List Zoom meetings client feedback](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardclientfeedback)
- `getTop25IssuesOfZoomRooms()` - [Get top 25 issues of Zoom Rooms](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardzoomroomissue)
- `getTop25ZoomRoomsWithIssues()` - [Get top 25 Zoom Rooms with issues](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardissuezoomroom)
- `getIssuesOfZoomRooms()` - [Get issues of Zoom Rooms](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardissuedetailzoomroom)
- `getMeetingQualityScores()` - [Get meeting quality scores](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardquality)
- `getMeetingsClientFeedback()` - [Get zoom meetings client feedback](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/dashboardclientfeedbackdetail)
- `listClientMeetingSatisfaction()` - [List client meeting satisfaction](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/listmeetingsatisfaction)
- `getPostMeetingFeedback()` - [Get post meeting feedback](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/participantfeedback)
- `getPostWebinarFeedback()` - [Get post webinar feedback](https://marketplace.zoom.us/docs/api-reference/zoom-api/dashboards/participantwebinarfeedback)

More to come.
