import { TICKETS } from '../types';
import { Tickets } from '../../models';

const initialState: Tickets = {
  tickets: [],
  selectedTicketInfo: {
    main_info: {
      ticket_title: '',
      ticket_description: '',
      ticket_status: '',
      created_at: new Date(),
      ticket_id: '',
      name: '',
      user_role: '',
    },
    history: [],
  },
  error: "",
};

export default function TicketsReducer(state = initialState, { type, payload }: any) {
  switch (type) {
    case TICKETS.GET_TICKETS_SUCCESS:
      return {
        ...state,
        tickets: payload,
      };
    case TICKETS.GET_TICKETS_FAILED:
      return {
        ...state,
        error: payload,
      };
    case TICKETS.ADD_TICKET_SUCCESS:
      return {
        ...state,
        tickets: [...state.tickets, payload],
      };
    case TICKETS.ADD_TICKET_FAILED:
      return {
        ...state,
        error: payload,
      };
    case TICKETS.GET_TICKETINFOBYID_SUCCESS:
      return {
        ...state,
        selectedTicketInfo: payload,
      };
    case TICKETS.GET_TICKETINFOBYID_FAILED:
      return {
        ...state,
        error: payload,
      };
    case TICKETS.UPDATE_SELECTEDTICKETINFO_SUCCESS:
      return {
        ...state,
        selectedTicketInfo: {
          ...state.selectedTicketInfo,
          history: [...state.selectedTicketInfo.history, payload]
        },
      };
    case TICKETS.UPDATE_SELECTEDTICKETINFO_FAILED:
      return {
        ...state,
        error: payload,
      };
    default:
      return state;
  }
}