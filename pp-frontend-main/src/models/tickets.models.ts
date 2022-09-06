export interface ITicket {
  ticket_id: string,
  ticket_title: string,
  ticket_description: string,
  ticket_status: string,
  created_at: Date,
  name: string,
  user_role: string,
}

export interface ITicketChat {
  sender: string,
  time: string,
  content: string,
}

export interface Tickets {
  tickets: ITicket[],
  selectedTicketInfo: {
    main_info: ITicket,
    history: ITicketChat[],
  }
  error: string,
}
