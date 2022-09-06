import { useDispatch, useSelector } from "react-redux";
import { MeetingService } from "../../services";
import { MEETING } from "../types";
import { useProgress } from "./progress.hook";

export const useMeeting = () => {
  const dispatch = useDispatch();
  const { selectedMeetingInfo, selectedChannelIndex, contactSidebarOpen, meetings, channelUnread } = useSelector(({ meeting }: any) => meeting);
  const { startProgress, stopProgress } = useProgress();

  const getMeetings = async () => {
    try {
      startProgress();
      const data = await MeetingService.getMeetings();
      const payload = data.data.data;
      dispatch({ type: MEETING.GET_MEETINGS_SUCCESS, payload });
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      dispatch({
        type: MEETING.GET_MEETINGS_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const setSelectedChannelIndex = async (payload: any) => {
    try {
      dispatch({
        type: MEETING.SET_SELECTEDCHANNELINDEX,
        payload
      });

      if (payload !== -1)
        await MeetingService.setUnreadasRead(selectedMeetingInfo.channels[payload].channel_id);

      return true;
    } catch ({ response, message }) {
      return false;
    }
  };

  const setContactsSidebar = (payload: any) => {
    dispatch({
      type: MEETING.SET_CONTACTSIDEBAROPEN,
      payload,
    });
  }

  const getMeetingInfoById = async (id: any) => {
    try {
      startProgress();
      const data = await MeetingService.getMeetingInfoById(id);
      const payload = data.data.data;
      dispatch({ type: MEETING.GET_MEETING_INFO_SUCCESS, payload });
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      dispatch({
        type: MEETING.GET_MEETING_INFO_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const sendMessage = async (payload: any) => {
    try {
      dispatch({ type: MEETING.SEND_MESSAGE_SUCCESS_M, payload });
      await MeetingService.sendMessage(payload);
      return true;
    } catch ({ response, message }) {
      dispatch({
        type: MEETING.SEND_MESSAGE_FAILED_M,
        payload: "message"
      });
      return false;
    }
  }

  const loadNewMessage = (payload: any) => {
    dispatch({
      type: MEETING.LOAD_NEW_MESSAGE_MEETING,
      payload,
    });
    return true;
  }

  return {
    meetings,
    channelUnread,
    selectedMeetingInfo,
    selectedChannelIndex,
    contactSidebarOpen,
    getMeetingInfoById,
    getMeetings,
    loadNewMessage,
    setSelectedChannelIndex,
    setContactsSidebar,
    sendMessage,
  };
};