import { POSTJOB } from '../types';
import { PostJob } from '../../models';

const initialState: PostJob = {
  jobInfo: {
    job_id: "",
    job_title: "",
    job_desc: "",
    job_status: "Active",
    job_tags: [],
    user_id: "",
  },
  topFreelancers: [],
  error: "",
  confirmCode: ""
};

export default function postJobReducer(state = initialState, { type, payload }: any) {
  switch (type) {
    case POSTJOB.SET_JOBINFO:
      return {
        ...state,
        jobInfo: payload,
      };
    case POSTJOB.GET_TOPFREELANCERS_SUCCESS:
      return {
        ...state,
        topFreelancers: payload,
      };
    case POSTJOB.GET_TOPFREELANCERS_FAILED:
      return {
        ...state,
        topFreelancers: [],
        error: payload,
      };
    case POSTJOB.GET_CONFIRMCODE_SUCCESS:
      return {
        ...state,
        confirmCode: payload,
      };
    case POSTJOB.GET_CONFIRMCODE_FAILED:
      return {
        ...state,
        confirmCode: '',
        error: payload,
      };
    default:
      return state;
  }
}