import { useEffect, useState } from "react"
import { useHistory } from "react-router-dom";
import { useTickets } from '../../../store/hooks'
import Header from '../../../layouts/header'
import MaterialTable, { MTableToolbar } from 'material-table'
import TicketModal from '../../../components/Ticket-Modal'
import { format } from 'date-fns';

function TicketsPage() {
  const history = useHistory();
  const { tickets, getTickets } = useTickets();
  const [showModal, setShowModal] = useState(false);

  const onShowTicket = (e: any, id: any) => {
    e.preventDefault();
    history.push('/ticket/' + id)
  }

  const columns = [
    {
      title: 'Title',
      field: 'ticket_title',
      render: (rowData: any) => <a className="text-green-300" href="void(0)" onClick={(e: any) => onShowTicket(e, rowData.ticket_id)}>{rowData.ticket_title}</a>
    },
    {
      title: 'Status',
      field: 'ticket_status',
      render: (rowData: any) =>
        <div className="flex">
          {
            rowData.ticket_status === "Open" ?
              (<div className="text-sm rounded-full py-1 px-4 text-white bg-red-400">
                {rowData.ticket_status}
              </div>) :
              (<div className="text-sm rounded-full py-1 px-4 text-white bg-blue-400">
                {rowData.ticket_status}
              </div>)
          }
        </div>
    },
    {
      title: 'Created',
      field: 'created_at',
      render: (rowData: any) => format(new Date(rowData.created_at), "MMMM do, yyyy hh:mm:ss")
    },
    { title: 'Ticket#', field: 'ticket_id' }
  ];

  useEffect(() => {
    const init = async () => {
      await getTickets();
    }
    init();
  }, []);// eslint-disable-line react-hooks/exhaustive-deps
  return (
    <>
      <Header></Header>
      <section className="bg-gray-100 h-full body-font pt-20 overflow-auto">
        <div className="container">
          <MaterialTable
            style={{ padding: "1rem", boxShadow: 'none' }}
            columns={columns}
            data={tickets}
            title={<p className="text-blue-500 text-4xl font-semibold">Tickets</p>}
            options={{
              sorting: true,
              headerStyle: {
                backgroundColor: 'rgb(249, 250, 251)',
                fontSize: "1.2rem",
              }
            }}
            components={{
              Toolbar: props => (
                <div className="flex justify-between items-center mb-8">
                  <div className="w-full">
                    <MTableToolbar {...props} />
                  </div>
                  <div className="w-60 flex justify-end">
                    <button onClick={(e: any) => setShowModal(true)} className="secondary-btn">Add Ticket</button>
                  </div>
                </div>
              ),
            }}
          />
        </div>
        {showModal ? <TicketModal setShowModal={setShowModal}></TicketModal> : null}
      </section>
    </>
  );
}

export default TicketsPage;
