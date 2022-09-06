import { useEffect, useState } from "react"
import { useHistory } from "react-router-dom";
import { useTickets, useUser } from '../../../store/hooks'
import Header from '../../../layouts/header'
import ArrowBack from '@material-ui/icons/ArrowBack';
import Avatar from '@material-ui/core/Avatar';
import { format } from 'date-fns';

const TicketViewPage = (props: any) => {
  const history = useHistory();
  const [isReply, setIsReply] = useState(false);
  const [status, setStatus] = useState('');
  const [description, setDescription] = useState('');
  const [descriptionError, setDescriptionError] = useState('');
  const { getTicketInfoById, selectedTicketInfo, updateSelectedTicketInfo } = useTickets();
  const { user } = useUser();

  useEffect(() => {
    const init = async () => {
      await getTicketInfoById(props.match.params.id);
    }
    init();
  }, []);// eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => {
    if (selectedTicketInfo.main_info)
      setStatus(selectedTicketInfo.main_info.ticket_status);
  }, [selectedTicketInfo.main_info]);

  const onShowTickets = () => {
    history.push('/tickets');
  }

  const onSendReply = (e: any) => {
    e.preventDefault();
    if (description === '') {
      setDescriptionError('Please input description');
      return;
    }

    updateSelectedTicketInfo({
      ticket_id: selectedTicketInfo.main_info.ticket_id,
      ticket_status: status,
      message: description
    });
    setIsReply(false);
    setDescription('');
  }

  return (
    <>
      <Header></Header>
      <section className="bg-gray-100 h-full body-font pt-20 overflow-auto">
        <div className="container pb-10">
          <div className="flex items-center cursor-pointer mb-4" onClick={onShowTickets}>
            <ArrowBack />
            <p className="ml-2 text-lg font-semibold">Tickets</p>
          </div>
          {selectedTicketInfo.main_info &&
            <div className="md:flex w-full">

              <div className="md:w-3/5">
                <div className="bg-white shadow-lg w-full px-4 py-8 rounded">
                  <h1 className="text-center text-green-500 text-2xl mb-4">Title: {selectedTicketInfo.main_info.ticket_title}</h1>
                  <p className="text-gray-500">Description: {selectedTicketInfo.main_info.ticket_description}</p>
                </div>

                <h1 className="mt-8 text-2xl font-semibold text-gray-700 mb-4">Messages</h1>
                <div className="bg-white shadow-lg w-full px-4 rounded border-l-4 border-solid border-gray-500">
                  {
                    selectedTicketInfo.history.map((item: any, i: any) => (
                      item.sender !== "Admin" ? (
                        <div className="p-4 border-b-2 border-gray-100 border-solid flex" key={i}>
                          <Avatar src={process.env.REACT_APP_BASE_URL + item.avatar} alt=""></Avatar>
                          <div className="pl-4">
                            <p>{item.sender}</p>
                            <p>{format(new Date(item.created_at), "MMMM do, yyyy hh:mm:ss")}</p>
                            <p className="text-gray-700 mt-8">{item.ticket_dtl_msg}</p>
                          </div>
                        </div>)
                        : (<div className="p-4 border-b-2 border-gray-100 border-solid flex justify-end" key={i}>
                          <div className="pr-4 flex flex-col items-end">
                            <p>{item.sender}</p>
                            <p>{format(new Date(item.created_at), "MMMM do, yyyy hh:mm:ss")}</p>
                            <p className="text-gray-700 mt-8">{item.ticket_dtl_msg}</p>
                          </div>
                          <Avatar src="/icons/avatar.svg" alt="Admin"></Avatar>
                        </div>)
                    ))
                  }
                </div>

                {isReply ? (<div className="mt-4 bg-white p-4 rounded shadow-lg">

                  <textarea
                    value={description}
                    placeholder="Description"
                    onChange={e => setDescription(e.target.value)}
                    onKeyDown={e => setDescriptionError('')}
                    className={"block w-full h-60 px-4 py-2 text-sm text-gray-600 bg-white border rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring mt-2 " + ((descriptionError !== '') ? 'border-red-500' : 'border-gray-300')}
                  >
                  </textarea>
                  {descriptionError && <p className="text-left text-xs text-red-500 mt-1">{descriptionError}</p>}
                  <div className="flex justify-end mt-4">
                    <select className="mr-2 px-4 rounded focus:outline-none cursor-pointer bg-gray-100" value={status} onChange={(e: any) => setStatus(e.target.value)}>
                      <option value="Open">Set to Open</option>
                      <option value="Closed">Set to Closed</option>
                    </select>
                    <button className="secondary-btn" onClick={(e: any) => onSendReply(e)}>Send to admin</button>
                  </div>
                </div>) :
                  <button className="secondary-btn mt-4" onClick={e => setIsReply(true)}>Replay to Admin</button>
                }
              </div>
              <div className="md:w-2/5 pl-10">
                <div className="flex">
                  {
                    selectedTicketInfo.main_info.ticket_status === "Open" ?
                      (<div className="rounded-full py-1 px-4 text-white bg-red-400">
                        {selectedTicketInfo.main_info.ticket_status}
                      </div>) :
                      (<div className="rounded-full py-1 px-4 text-white bg-blue-400">
                        {selectedTicketInfo.main_info.ticket_status}
                      </div>)
                  }
                </div>

                <div className="bg-white shadow-lg w-full p-4 rounded mt-4 ">
                  <p className="text-center text-gray-500 text-lg mb-4 border-solid border-b-2 border-gray-200">Title: {selectedTicketInfo.main_info.ticket_title}</p>
                  <div className="px-4 flex pb-6">
                    <div className="w-1/2">
                      <p className="text-gray-800">Ticket Opened by</p>
                      <p className="text-gray-800 mt-2">Date</p>
                    </div>
                    <div className="w-1/2">
                      <p className="text-gray-800">{user.user.full_name}</p>
                      <p className="text-gray-800 mt-2">{format(new Date(selectedTicketInfo.main_info.created_at), "MMMM do, yyyy hh:mm:ss")}</p>
                    </div>

                  </div>
                </div>
              </div>
            </div>
          }
        </div>
      </section>
    </>
  );
}

export default TicketViewPage;
