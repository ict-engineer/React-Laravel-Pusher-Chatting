import Avatar from '@material-ui/core/Avatar';
import moment from 'moment';
import StatusIcon from './StatusIcon';
import { useMeeting, useUser } from "../../../store/hooks";

const ContactListItem = (props: any) => {
  const { setSelectedChannelIndex, selectedChannelIndex } = useMeeting();
  const { user } = useUser();

  const differceFromToday = (str: any) => {
    let date1 = new Date().setHours(0, 0, 0, 0);
    let date2 = new Date(str).setHours(0, 0, 0, 0);

    return (date1 - date2) / (24 * 60 * 60 * 1000);
  }

  return (
    <div
      className={'flex px-4 py-4 min-h-92 cursor-pointer ' + (selectedChannelIndex === props.index ? 'bg-blue-200' : '')}
      onClick={() => setSelectedChannelIndex(props.index)}
    >
      <div className="relative mb-auto">
        {props.channel.fre_id && user.user.user_role === "client" ? (
          <div className="absolute right-0 bottom-0 z-10">
            {props.index === 1 ? (<StatusIcon status="online" />) : (props.index === 2 ? <StatusIcon status="away" /> : (props.index === 3 ? <StatusIcon status="do-not-disturb" /> : <StatusIcon status="offline" />))}
          </div>) : null}

        <Avatar src={process.env.REACT_APP_BASE_URL + props.channel.avatar} alt={props.channel.full_name}>
          {props.channel.first_name.charAt(0).toUpperCase() + props.channel.last_name.charAt(0).toUpperCase()}
        </Avatar>
      </div>
      <div className="w-full">
        <div className="pl-5 flex w-full justify-between items-center">
          {props.channel.fre_id !== '' && user.user.user_role === "client" ?
            (<p className="text-blue-600 font-semibold">{props.channel.full_name}  ({(props.index + 9).toString(36).toUpperCase()})</p>)
            : (<p className="text-blue-600 font-semibold">{props.channel.full_name}</p>)}
          {props.channel.lastMessageTime && (
            <p className="whitespace-no-wrap text-xs text-gray-500">
              {differceFromToday(props.channel.lastMessageTime) === 0 ? moment(new Date(props.channel.lastMessageTime)).format('h:mm A') :
                ((differceFromToday(props.channel.lastMessageTime) < 4) ? moment(new Date(props.channel.lastMessageTime)).format('ddd')
                  : moment(new Date(props.channel.lastMessageTime)).format('M/D/YYYY'))
              }
            </p>
          )}
        </div>

        <div className="flex pl-5 w-full justify-between items-center">

          <p className="text-sm text-gray-600">{props.channel.last_message}</p>
          {props.channel.unread !== 0 && (
            <div
              className='flex items-center justify-center w-5 h-5 rounded-full text-xs text-center bg-blue-500 text-white'
            >
              {props.channel.unread}
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

export default ContactListItem;
