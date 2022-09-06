export interface Message {
  msg_id: string,
  msg_body: string,
  channel_id: string,
  user_id: string,
  created_at: Date,
}

export interface Channel {
  channel_id: string,
  channel_status: boolean,
  fre_id: string,
  clt_id: string,
  job_id: string,
  created_at: Date,
  full_name: string,
  first_name: string,
  last_name: string,
  avatar: string,
  has_contract: boolean,
  last_message: string,
  lastMessageTime: Date,
  unread: number,
  messages: Message[],
}

export interface IMeeting {
  id: string,
  title: string,
  last_message: string,
  created: string,
  status: string,
}

export interface Meeting {
  selectedMeetingInfo: {
    title: string,
    description: string,
    channels: Channel[],
    contacts: Object,
  },
  meetings: IMeeting[]
  selectedChannelIndex: number,
  error: string,
  channelUnread: number,
  contactSidebarOpen: boolean,
}
