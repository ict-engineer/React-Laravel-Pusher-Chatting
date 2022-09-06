import { useDispatch, useSelector } from "react-redux";
import { TicketsService } from "../../services";
import { TICKETS } from "../types";
import { useProgress } from "./progress.hook";

export const useTickets = () => {
  const dispatch = useDispatch();
  const { tickets, selectedTicketInfo } = useSelector(({ tickets }: any) => tickets);
  const { startProgress, stopProgress } = useProgress();

  const getTickets = async () => {
    try {
      startProgress();
      const result = await TicketsService.getTickets();
      dispatch({
        type: TICKETS.GET_TICKETS_SUCCESS,
        payload: result.data.data
      });
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      dispatch({
        type: TICKETS.GET_TICKETS_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const getTicketInfoById = async (id: any) => {
    try {
      startProgress();
      const result: any = await TicketsService.getTicketInfoById(id);
      dispatch({
        type: TICKETS.GET_TICKETINFOBYID_SUCCESS,
        payload: result.data.data
      });
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      dispatch({
        type: TICKETS.GET_TICKETINFOBYID_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const updateSelectedTicketInfo = async (data: any) => {
    try {
      startProgress();
      const result: any = await TicketsService.updateSelectedTicketInfo(data);
      dispatch({
        type: TICKETS.UPDATE_SELECTEDTICKETINFO_SUCCESS,
        payload: result.data.data
      });
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      dispatch({
        type: TICKETS.UPDATE_SELECTEDTICKETINFO_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  const addTicket = async (data: any) => {
    try {
      startProgress();
      const result = await TicketsService.addTicket(data);
      dispatch({
        type: TICKETS.ADD_TICKET_SUCCESS,
        payload: result.data.data
      });
      stopProgress();
      return true;
    } catch ({ response, message }) {
      stopProgress();
      dispatch({
        type: TICKETS.ADD_TICKET_FAILED,
        payload: "message"
      });
      return false;
    }
  }

  return {
    tickets,
    selectedTicketInfo,
    getTickets,
    addTicket,
    updateSelectedTicketInfo,
    getTicketInfoById,
  };
};