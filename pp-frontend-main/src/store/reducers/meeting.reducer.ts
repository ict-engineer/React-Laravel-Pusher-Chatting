import { MEETING } from '../types';
import { Meeting } from '../../models';
import { MeetingService } from "../../services";

const initialState: Meeting = {
  selectedMeetingInfo: {
    title: '',
    description: '',
    channels: [],
    contacts: {},
  },
  meetings: [],
  selectedChannelIndex: -1,
  error: "",
  contactSidebarOpen: false,
  channelUnread: 0,
};

export default function meetingReducer(state = initialState, { type, payload }: any) {
  switch (type) {
    case MEETING.SET_MOBILECHATSIDEBAROPEN:
      return {
        ...state,
        mobileChatsSidebarOpen: payload,
      };
    case MEETING.SET_SELECTEDCHANNELINDEX:
      {
        let channels = state.selectedMeetingInfo.channels;
        let unread = 0;
        if (payload !== -1) {
          unread = channels[payload].unread;
          channels[payload].unread = 0;
        }

        return {
          ...state,
          selectedChannelIndex: payload,
          channelUnread: unread,
          selectedMeetingInfo: { ...state.selectedMeetingInfo, channels: channels },
        };
      }
    case MEETING.SET_CONTACTSIDEBAROPEN:
      return {
        ...state,
        contactSidebarOpen: payload,
      };
    case MEETING.GET_MEETINGS_SUCCESS:
      return {
        ...state,
        meetings: payload,
      };
    case MEETING.GET_MEETINGS_FAILED:
      return {
        ...state,
        error: payload,
      };
    case MEETING.GET_MEETING_INFO_SUCCESS:
      return {
        ...state,
        selectedMeetingInfo: payload,
      };
    case MEETING.GET_MEETING_INFO_FAILED:
      return {
        ...state,
        error: payload,
      };
    case MEETING.SEND_MESSAGE_SUCCESS_M:
      let channels = state.selectedMeetingInfo.channels;
      if (state.selectedChannelIndex !== -1) {

        channels[state.selectedChannelIndex].messages.push(payload);
        channels[state.selectedChannelIndex].last_message = payload.msg_body;
        channels[state.selectedChannelIndex].lastMessageTime = payload.created_at;
      }

      return {
        ...state,
        channelUnread: 0,
        selectedMeetingInfo: { ...state.selectedMeetingInfo, channels: channels },
      };
    case MEETING.SEND_MESSAGE_FAILED_M:
      return {
        ...state,
        error: payload,
      };
    case MEETING.LOAD_NEW_MESSAGE_MEETING:
      {
        let channels = state.selectedMeetingInfo.channels;
        let unread = state.channelUnread;

        channels.forEach((channel, idx) => {
          if (channel.channel_id === payload.channel_id) {
            channel.messages.push(payload);
            channel.last_message = payload.msg_body;
            if (state.selectedChannelIndex !== idx) {
              channel.unread += 1;
            }
            else {
              unread = channel.unread + 1;
            }
            channel.lastMessageTime = payload.created_at;

            if (state.selectedChannelIndex === idx) {
              MeetingService.setUnreadasRead(channel.channel_id);
            }
          }
        });

        return {
          ...state,
          channelUnread: unread,
          selectedMeetingInfo: { ...state.selectedMeetingInfo, channels: channels },
        };
      }
    default:
      return state;
  }
}