import { Message } from './meeting.models'

// export interface Contract {
//   contract_id: string,
//   contract_title: string,
//   contract_desc: string,
//   contract_max_hrs: number,
//   contract_hourly_rate: number,
//   contract_allow_manual_track: boolean,
//   channel_id: string,
//   contract_status: string,
// }

// export interface ChatLog {
//   chat_log_id: string,
//   channel_id: string,
//   user_id: string,
//   chat_type: string,
//   chat_log_event_id: string,
//   is_read: string,
// }

// export interface TimeTrack {

// }

// export interface Invoice {

// }

export interface ITransaction {
  channel_id: string,
  job_title: string,
  job_desc: string,
  channel_status: boolean,
  contract_status: string,
  contract_id: string,
  has_review: boolean,
  fre_id: string,
  clt_id: string,
  job_id: string,
  unread: number,
  full_name: string,
  first_name: string,
  last_name: string,
  avatar: string,
  history: any[],
}

export interface Transaction {
  transactions: ITransaction[],
  selectedTransactionId: number,
  error: string,
  transactionUnread: number,
}
