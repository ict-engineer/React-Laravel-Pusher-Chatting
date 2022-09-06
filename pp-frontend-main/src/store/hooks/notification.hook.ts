import { useDispatch, useSelector } from "react-redux";
import { ANOTI } from "../types";


export const useNotification = () => {
  const dispatch = useDispatch();
  const { new_noti } = useSelector(({ notification }: any) => notification);

  const setNewNotification = (type: any, description: any) => {
    const payload = {
      type: type,
      description: description
    }

    dispatch({
      type: ANOTI.NEW,
      payload
    });
    return true;
  };

  const initNotification = () => {
    dispatch({
      type: ANOTI.INIT
    });

    return true
  };

  return {
    new_noti,
    setNewNotification,
    initNotification,
  };
};