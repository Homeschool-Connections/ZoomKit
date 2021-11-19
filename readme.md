# ZoomKit

Consume Zoom Meeting APIs easily from a Laravel application.
Licensed under MIT.

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

#### `ZoomKitArchiving` class
- `listArchivedFiles()` - [List archived files](https://marketplace.zoom.us/docs/api-reference/zoom-api/archiving/listarchivedfiles)
- `getMeetingArchivedFiles()` - [Get meeting archived files](https://marketplace.zoom.us/docs/api-reference/zoom-api/archiving/testgetrecordarchivedfiles)
- `deleteMeetingArchivedFiles()` - [Delete meeting archived files](https://marketplace.zoom.us/docs/api-reference/zoom-api/archiving/deleterecordarchivedfiles)

#### `ZoomKitCloudRecordings` class
- `listAllRecordings()` - [List all recordings](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingslist)
- `getMeetingRecordings()` - [Get meeting recordings](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingget)
- `deleteMeetingRecordings()` - [Delete meeting recordings](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingdelete)
- `deleteMeetingRecordingFile()` - [Delete meeting recording file](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingdeleteone)
- `recoverMeetingRecordings()` - [Recover meeting recordings](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingstatusupdate)
- `recoverSingleRecording()` - [Recover single recording](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingstatusupdateone)
- `getMeetingRecordingSettings()` - [Get meeting recording settings](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingsettingupdate)
- `updateMeetingRecordingSettings()` - [Update meeting recording settings](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingsettingsupdate)
- `listRecordingRegistrants()` - [List recording registrants](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/meetingrecordingregistrants)
- `createRecordingRegistrant()` - [Create recording registrant](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/meetingrecordingregistrantcreate)
- `updateRegistrantsStatus()` - [Update registrants status](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/meetingrecordingregistrantstatus)
- `getRegistrationQuestions()` - [Get registration questions](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingregistrantsquestionsget)
- `updateRegistrationQuestions()` - [Update registration questions](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/recordingregistrantquestionupdate)
- `listAccountRecordings()` - [List account recordings](https://marketplace.zoom.us/docs/api-reference/zoom-api/cloud-recording/getaccountcloudrecording)

#### `ZoomKitContacts` class
- `searchCompanyContacts()` - [Search company contacts](https://marketplace.zoom.us/docs/api-reference/zoom-api/contacts/searchcompanycontacts)
- `listUserContacts()` - [List user contacts](https://marketplace.zoom.us/docs/api-reference/zoom-api/contacts/getusercontacts)
- `getUserContactDetails()` - [Get user contact details](https://marketplace.zoom.us/docs/api-reference/zoom-api/contacts/getusercontact)

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

#### `ZoomKitDevices` class
- `listH323SIPDevices()` - [List H.323/SIP devices](https://marketplace.zoom.us/docs/api-reference/zoom-api/devices/devicelist)
- `createH323SIPDevice()` - [Create a H.323/SIP device](https://marketplace.zoom.us/docs/api-reference/zoom-api/devices/devicecreate)
- `updateH323SIPDevice()` - [Update a H.323/SIP device](https://marketplace.zoom.us/docs/api-reference/zoom-api/devices/deviceupdate)
- `deleteH323SIPDevice()` - [Delete a H.323/SIP device](https://marketplace.zoom.us/docs/api-reference/zoom-api/devices/devicedelete)

#### `ZoomKitMeetings` class
- `listMeetings()` - [List meetings](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetings)
- `getMeeting()` - [Get a meeting](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meeting)
- `createMeeting()` - [Create a meeting](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingcreate)
- `updateMeeting()` - [Update a meeting](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingupdate)
- `deleteMeeting()` - [Delete a meeting](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingdelete)
- `updateMeetingStatus()` - [Update meeting status](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingstatus)
- `listMeetingRegistrants()` - [List meeting registrants](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrants)
- `addMeetingRegistrant()` - [Add meeting registrant](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantcreate)
- `deleteMeetingRegistrant()` - [Delete meeting registrant](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantdelete)
- `getMeetingRegistrant()` - [Get meeting registrant](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantget)
- `updateRegistrantStatus()` - [Update registrant's status](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantstatus)
- `getPastMeetingDetails()` - [Get past meeting details](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/pastmeetingdetails)
- `getPastMeetingParticipants()` - [Get past meeting participants](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/pastmeetingparticipants)
- `listEndedMeetingInstance()` - [List ended meeting instance](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/pastmeetings)
- `listMeetingPolls()` - [List meeting polls](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingpolls)
- `createMeetingPoll()` - [Create a meeting poll](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingpollcreate)
- `getMeetingPoll()` - [Get a meeting poll](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingpollget)
- `updateMeetingPoll()` - [Update a meeting poll](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingpollupdate)
- `deleteMeetingPoll()` - [Delete a meeting poll](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingpolldelete)
- `listRegistrationQuestions()` - [List registration questions](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantsquestionsget)
- `updateRegistrationQuestions()` - [Update registration questions](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetingregistrantquestionupdate)
- `getMeetingInvitation()` - [Get meeting invitation](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetinginvitation)
- `updateLiveStream()` - [Update a live stream](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetinglivestreamupdate)
- `getLiveStreamDetails()` - [Get live stream details](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/getmeetinglivestreamdetails)
- `updateLiveStreamStatus()` - [Update Live Stream Status](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetinglivestreamstatusupdate)
- `listPastMeetingPollResults()` - [List past meeting's poll results](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/listpastmeetingpolls)
- `performBatchRegistration()` - [Perform batch registration](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/addbatchregistrants)
- `useInMeetingRecordingControl()` - [Use in-Meeting recording control](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/inmeetingrecordingcontrol)
- `performBatchPollCreation()` - [Perform batch poll creation](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/createbatchpolls)
- `listMeetingTemplates()` - [List meeting templates](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/listmeetingtemplates)
- `createMeetingInviteLinks()` - [Create meeting's invite links](https://marketplace.zoom.us/docs/api-reference/zoom-api/meetings/meetinginvitelinkscreate)

##### `ZoomKitMeetingsExtras` class
Expands the `ZoomKitMeetings` class with ZoomKit-exclusive API abstractions that simplify the use of some of Zoom's more complex APIs.
- `createInstantMeeting()` - Simplified call of `ZoomKitMeetings::createMeeting()` specific to Instant Meetings
- `createScheduledMeeting()` - Simplified call of `ZoomKitMeetings::createMeeting()` specific to Scheduled Meetings
- `endMeeting()` - Simplified call of `ZoomKitMeeting::updateMeetingStatus()` to simply end a meeting.

#### `ZoomKitPAC` class
Zoom's smallest API section (tied with the Zoom Rooms Devices section) with only one function.
- `listUserPACAccounts()` - [List a user's PAC accounts](https://marketplace.zoom.us/docs/api-reference/zoom-api/pac/userpacs)

#### `ZoomKitZoomRoomsAccount` class
- `getZoomRoomAccountProfile()` - [Get Zoom Room account profile](https://marketplace.zoom.us/docs/api-reference/zoom-api/zoom-rooms-account/getzraccountprofile)
- `updateZoomRoomAccountProfile()` - [Update Zoom Room account profile](https://marketplace.zoom.us/docs/api-reference/zoom-api/zoom-rooms-account/updatezraccprofile)

#### `ZoomKitZoomRoomsDevices` class
- `changeZoomRoomsAppVersion()` - [Change Zoom Rooms app version](https://marketplace.zoom.us/docs/api-reference/zoom-api/zoom-rooms-devices/changezoomroomsappversion)

### Zoom API Sections
See the [Zoom API documentation](https://marketplace.zoom.us/docs/api-reference/zoom-api) for more information.

The goal is to eventually support most, if not all, of Zoom's APIs for easy consumption.
**Currently, only the API sections marked as partially or fully implemented / complete are present.**

- [ ] Accounts
- [x] Archiving (complete)
- [ ] Billing
- [ ] Chat Channels
- [ ] Chat Channels (Account-level)
- [ ] Chat Messages
- [ ] Chatbot Messages
- [x] Contacts (complete)
- [x] Cloud Recording (complete)
- [x] Dashboards (complete)
- [x] Devices (complete)
- [ ] Groups
- [ ] IM Groups
- [x] Meetings (complete)
- [x] PAC (complete)
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
- [x] Zoom Rooms Devices (complete)

I hope to eventually complete all the Zoom API sections.
I will not be implementing deprecated functions (such as the IM Chat section of APIs).
If APIs are deprecated after I've already implemented them, they will remain implemented until they are shut down.
