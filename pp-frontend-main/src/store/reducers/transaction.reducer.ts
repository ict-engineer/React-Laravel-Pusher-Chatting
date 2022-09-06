import { TRANSACTION } from '../types';
import { Transaction } from '../../models';
import { TransactionService } from "../../services";

const initialState: Transaction = {
  transactions: [],
  selectedTransactionId: -1,
  error: "",
  transactionUnread: 0,
};

export default function TransactionReducer(state = initialState, { type, payload }: any) {
  switch (type) {
    case TRANSACTION.GET_TRANSACTIONS_SUCCESS:
      return {
        ...state,
        transactions: payload,
      };
    case TRANSACTION.GET_TRANSACTIONS_FAILED:
      return {
        ...state,
        error: payload,
      };
    case TRANSACTION.GET_TRANSACTIONINFO_SUCCESS:
      return {
        ...state,
        selectedTransactionInfo: payload,
      };
    case TRANSACTION.GET_TRANSACTIONINFO_FAILED:
      return {
        ...state,
        error: payload,
      };
    case TRANSACTION.SEND_MESSAGE_SUCCESS:
      let transactions = state.transactions;
      let index = -1;
      if (transactions[state.selectedTransactionId] !== undefined && state.selectedTransactionId !== -1) {
        let history: any[] = transactions[state.selectedTransactionId].history;
        history.push(payload);
        transactions[state.selectedTransactionId].history = history;

        if (payload.chat_type === "review")
          transactions[state.selectedTransactionId].has_review = true;

        if (state.selectedTransactionId) {
          let tmp = transactions[state.selectedTransactionId];
          transactions[state.selectedTransactionId] = transactions[0];
          transactions[0] = tmp;
        }
        index = 0;
      }
      return {
        ...state,
        selectedTransactionId: index,
        transactionUnread: 0,
        transactions: transactions,
      };
    case TRANSACTION.SEND_MESSAGE_FAILED:
      return {
        ...state,
        error: payload,
      };
    case TRANSACTION.SET_SELECTEDTRANSACTIONID:
      {
        let transactions = state.transactions;
        let unread = 0;
        if (payload !== -1) {
          unread = transactions[payload].unread;
          transactions[payload].unread = 0;
        }

        return {
          ...state,
          transactions: transactions,
          transactionUnread: unread,
          selectedTransactionId: payload,
        };
      }
    case TRANSACTION.LOAD_NEW_MESSAGE:
      {
        let transactions = state.transactions;
        let unread = state.transactionUnread;
        let index = -1;
        transactions.forEach((transaction, idx) => {
          if (transaction.channel_id === payload.channel_id) {
            transaction.history.push(payload);
            if (state.selectedTransactionId !== idx) {
              transaction.unread += 1;
            }
            else {
              unread = transaction.unread + 1;
            }
            index = idx;

            if (state.selectedTransactionId === idx) {
              TransactionService.setUnreadasReadTr(transaction.channel_id);
            }
          }
        });

        if (index !== -1 && index) {
          let tmp = transactions[index];
          transactions[index] = transactions[0];
          transactions[0] = tmp;
        }

        let tranId = state.selectedTransactionId;
        if (tranId === 0)
          tranId = index;
        else if (tranId === index && tranId !== -1)
          tranId = 0;

        return {
          ...state,
          selectedTransactionId: tranId,
          transactionUnread: unread,
          transactions: transactions,
        };
      }
    case TRANSACTION.CHANGE_CONTRACT:
      {
        let transactions = state.transactions;
        transactions[state.selectedTransactionId].contract_status = payload.status;
        transactions[state.selectedTransactionId].history.push(payload.data);

        let index = state.selectedTransactionId;
        if (state.selectedTransactionId !== -1 && state.selectedTransactionId !== 0) {
          let tmp = transactions[state.selectedTransactionId];
          transactions[state.selectedTransactionId] = transactions[0];
          transactions[0] = tmp;
          index = 0;
        }

        return {
          ...state,
          selectedTransactionId: index,
          transactionUnread: 0,
          transactions: transactions,
        };
      }
    case TRANSACTION.CONTRACT_STATUS_CHANGE:
      {
        let transactions = state.transactions;
        let unread = state.transactionUnread;
        let index = -1;

        transactions.forEach((transaction, idx) => {
          if (transaction.channel_id === payload.data.channel_id) {
            transaction.contract_status = payload.status;
            transaction.history.push(payload.data);
            if (state.selectedTransactionId !== idx) {
              transaction.unread += 1;
            }
            else {
              unread = transaction.unread + 1;
            }
            index = idx;

            if (state.selectedTransactionId === idx) {
              TransactionService.setUnreadasReadTr(transaction.channel_id);
            }
          }
        });

        if (index !== -1 && index) {
          let tmp = transactions[index];
          transactions[index] = transactions[0];
          transactions[0] = tmp;
          index = 0;
        }

        let tranId = state.selectedTransactionId;
        if (tranId === 0)
          tranId = index;
        else if (tranId === index && tranId !== -1)
          tranId = 0;

        return {
          ...state,
          transactionUnread: unread,
          selectedTransactionId: tranId,
          transactions: transactions,
        };
      }
    default:
      return state;
  }
}