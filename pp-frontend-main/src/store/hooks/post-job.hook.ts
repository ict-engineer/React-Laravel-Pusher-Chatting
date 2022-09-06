import { useDispatch, useSelector } from "react-redux";
import { PostJobService } from "../../services";
import { useNotification } from './notification.hook';
import { POSTJOB, NOTI_TYPE } from "../types";
import { useProgress } from "../hooks";

export const usePostJob = () => {
  const dispatch = useDispatch();
  const { jobInfo, topFreelancers, confirmCode } = useSelector(({ postJob }: any) => postJob);
  const { setNewNotification } = useNotification();
  const { startProgress, stopProgress } = useProgress();

  const setJobInfo = (payload: any) => {
    dispatch({
      type: POSTJOB.SET_JOBINFO,
      payload
    });

    return true;
  };

  const addJobInfo = async () => {
    try {
      startProgress();
      await PostJobService.addJobInfo(jobInfo, topFreelancers);
      setNewNotification(NOTI_TYPE.SUCCESS, "Job successfully posted.");
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      setNewNotification(NOTI_TYPE.WARNING, "Job post error.");
      return false;
    }
  }

  const getTopFreelancers = async () => {
    try {
      const data = await PostJobService.getTopFreelancers();
      const payload = data.data.data;
      dispatch({ type: POSTJOB.GET_TOPFREELANCERS_SUCCESS, payload });
      return true;
    } catch ({ response, message }) {

      dispatch({
        type: POSTJOB.GET_TOPFREELANCERS_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const getConfirmCode = async (email: any) => {
    try {
      startProgress();
      const result = await PostJobService.getConfirmCode(email);
      setNewNotification(NOTI_TYPE.SUCCESS, "Sent confirm code to email!");
      stopProgress();
      return result.data.data;
    } catch ({ response, message }) {
      stopProgress();
      setNewNotification(NOTI_TYPE.WARNING, "Failed to sent confirm code!");
      return '';
    }
  }

  return {
    jobInfo,
    topFreelancers,
    confirmCode,
    getConfirmCode,
    getTopFreelancers,
    setJobInfo,
    addJobInfo,
  };
};