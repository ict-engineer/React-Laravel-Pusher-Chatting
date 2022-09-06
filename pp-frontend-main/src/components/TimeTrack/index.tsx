import './../../styles/styles.css'
import Avatar from '@material-ui/core/Avatar';
import moment from 'moment/moment';
import { useUser, useTransaction } from "../../store/hooks";

const TimeTrack = (props: any) => {
  const { selectedTransactionId, transactions } = useTransaction();
  const { user } = useUser();

  return (
    <>
      {user.user.user_role === "client" ?
        <div className="flex flex-col items-start relative w-full mb-4" style={{ paddingLeft: "45px" }}>
          <Avatar
            style={{ position: "absolute", top: '0', left: '0' }}
            src={process.env.REACT_APP_BASE_URL + transactions[selectedTransactionId].avatar}
            alt={transactions[selectedTransactionId].full_name}
          >{transactions[selectedTransactionId].first_name.charAt(0).toUpperCase() + transactions[selectedTransactionId].last_name.charAt(0).toUpperCase()}
          </Avatar>
          <div className="w-1/2 border-solid border-gray-300 border-2 px-4 py-2">
            <p className="font-medium text-2xl text-center border-b-2 border-solid border-gray-200 mb-2">Time Track(manual)</p>
            <p className="text-gray-400 mt-1 font-medium">Duaration of this work</p>
            <p className="text-gray-400 mt-1 font-medium">({props.item.trk_date}: {props.item.trk_from}~{props.item.trk_to})   {props.item.trk_total_hrs}Hours</p>
            <p className="text-gray-400 mt-1 font-medium whitespace-pre-line">{props.item.trk_description}</p>
            <p className="text-gray-400 mt-1 text-xs">{moment(new Date(props.item.created_at)).format('h:mm A')}</p>
          </div>
        </div> :
        <div className="flex flex-col items-end relative w-full mb-4" style={{ paddingRight: "45px" }}>
          <Avatar
            style={{ position: "absolute", top: '0', right: '0' }}
            src={process.env.REACT_APP_BASE_URL + user.user.avatar}
          >
            {user.user.first_name === "" ? null : (user.user.first_name.charAt(0).toUpperCase() + user.user.last_name.charAt(0).toUpperCase())}
          </Avatar>
          <div className="w-1/2 border-solid border-gray-300 border-2 px-4 py-2">
            <p className="font-medium text-2xl text-center border-b-2 border-solid border-gray-200 mb-2">Time Track(manual)</p>
            <p className="text-gray-400 mt-1 font-medium">Duaration of this work</p>
            <p className="text-gray-400 mt-1 font-medium">({props.item.trk_date}: {props.item.trk_from}~{props.item.trk_to})   {props.item.trk_total_hrs}Hours</p>
            <p className="text-gray-400 mt-1 font-medium whitespace-pre-line">{props.item.trk_description}</p>
            <p className="text-gray-400 mt-1 text-xs">{moment(new Date(props.item.created_at)).format('h:mm A')}</p>
          </div>
        </div>
      }
    </>
  );
};

export default TimeTrack;