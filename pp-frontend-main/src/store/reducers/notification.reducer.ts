import { ANOTI, NOTI_TYPE } from '../types';
import { Notification } from '../../models';

const initialState: Notification = {
  new_noti: {
    type: NOTI_TYPE.INFO,
    description: "",
  }
};

export default function notificationReducer(state = initialState, { type, payload }: any) {
  switch (type) {
    case ANOTI.INIT:
      return {
        ...state,
        new_noti: {
          type: NOTI_TYPE.INFO,
          description: "",
        }
      };
    case ANOTI.NEW:
      return {
        ...state,
        new_noti: {
          type: payload.type,
          description: payload.description,
        }
      };
    default:
      return state;
  }
}