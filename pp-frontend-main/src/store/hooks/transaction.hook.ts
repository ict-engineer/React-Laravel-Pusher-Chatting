import { useDispatch, useSelector } from "react-redux";
import { TransactionService } from "../../services";
import { useProgress } from "./progress.hook";
import { TRANSACTION, NOTI_TYPE } from "../types";
import { useNotification } from './notification.hook';

export const useTransaction = () => {
  const dispatch = useDispatch();
  const { transactions, transactionUnread, selectedTransactionInfo, selectedTransactionId } = useSelector(({ transaction }: any) => transaction);
  const { startProgress, stopProgress } = useProgress();
  const { setNewNotification } = useNotification();

  const addContract = async (payload: any) => {
    try {
      startProgress();
      await TransactionService.addContract(payload);
      setNewNotification(NOTI_TYPE.SUCCESS, "Successfully made a contract!");
      stopProgress();
    } catch ({ response, message }) {
      stopProgress();
      setNewNotification(NOTI_TYPE.WARNING, "Failed to make a contract");
      return false;
    }
  }

  const handleContractStatusChange = (payload: any) => {
    dispatch({ type: TRANSACTION.CONTRACT_STATUS_CHANGE, payload });
    return true;
  }

  const changeContract = async (payload: any) => {
    try {
      startProgress();
      const data = await TransactionService.changeContract(payload);
      dispatch({ type: TRANSACTION.CHANGE_CONTRACT, payload: { status: payload.status, data: data.data.data } });
      setNewNotification(NOTI_TYPE.SUCCESS, "Successfully" + payload.status + " !");
      stopProgress();
    } catch ({ response, message }) {
      stopProgress();
      setNewNotification(NOTI_TYPE.WARNING, response.data.message);
      return false;
    }
  }

  const loadNewMessageTr = (payload: any) => {
    dispatch({
      type: TRANSACTION.LOAD_NEW_MESSAGE,
      payload,
    });
    return true;
  }

  const getTransactions = async () => {
    try {
      startProgress();
      const result = await TransactionService.getTransactions();
      dispatch({ type: TRANSACTION.GET_TRANSACTIONS_SUCCESS, payload: result.data.data });
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      dispatch({
        type: TRANSACTION.GET_TRANSACTIONS_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const sendMessageTr = async (payload: any) => {
    try {
      if (payload.chat_type === 'message')
        dispatch({ type: TRANSACTION.SEND_MESSAGE_SUCCESS, payload });

      const data = await TransactionService.sendMessage(payload);

      if (payload.chat_type !== 'message') {
        dispatch({ type: TRANSACTION.SEND_MESSAGE_SUCCESS, payload: { ...data.data.data, chat_type: payload.chat_type } });
        setNewNotification(NOTI_TYPE.SUCCESS, "Successfully add a timetrack");
      }

      return true;
    } catch ({ response, message }) {
      if (payload.chat_type !== 'message')
        setNewNotification(NOTI_TYPE.WARNING, response.data.message);
      dispatch({
        type: TRANSACTION.SEND_MESSAGE_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const setSelectedTransactionId = async (payload: any) => {
    try {
      dispatch({
        type: TRANSACTION.SET_SELECTEDTRANSACTIONID,
        payload
      });

      if (payload !== -1) {
        await TransactionService.setUnreadasReadTr(transactions[payload].channel_id);
      }
      return true;
    } catch ({ response, message }) {
      return false;
    }
  }

  return {
    transactions,
    selectedTransactionId,
    selectedTransactionInfo,
    transactionUnread,
    addContract,
    changeContract,
    loadNewMessageTr,
    setSelectedTransactionId,
    getTransactions,
    sendMessageTr,
    handleContractStatusChange,
  };
};