import { useTransaction } from "../../../store/hooks";
import Avatar from '@material-ui/core/Avatar';

const TransactionListItem = (props: any) => {
  const { setSelectedTransactionId, selectedTransactionId } = useTransaction();

  return (
    <div
      className={'flex px-4 py-4 min-h-92 cursor-pointer border-l-4 border-solid border-green-400 mb-2 ' + (selectedTransactionId === props.index ? 'bg-blue-200' : '')}
      onClick={() => setSelectedTransactionId(props.index)}
    >
      <div className=" w-full">
        <div className="pr-8 flex w-full items-center text-green-500 font-medium text-xl mb-2">
          {props.contact.job_title}
        </div>
        <div className="flex w-full items-center">
          <Avatar src={process.env.REACT_APP_BASE_URL + props.contact.avatar} alt={props.contact.full_name}>
            {props.contact.first_name === "" ? null : (props.contact.first_name.charAt(0).toUpperCase() + props.contact.last_name.charAt(0).toUpperCase())}
          </Avatar>
          <p className="ml-4 text-blue-600 font-semibold">{props.contact.full_name}</p>
          {props.contact.unread !== 0 && (
            <div
              className='ml-4 flex items-center justify-center w-5 h-5 rounded-full text-xs text-center bg-blue-500 text-white'
            >
              {props.contact.unread}
            </div>
          )}
        </div>
      </div>
    </div >
  );
}
export default TransactionListItem;
