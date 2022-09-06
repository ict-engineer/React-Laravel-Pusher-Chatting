import { useMemo, useState } from 'react';
import { useMeeting, useUser } from "../../../store/hooks";
import { useHistory } from "react-router-dom";
import ContactListItem from './ContactListItem';
import Avatar from '@material-ui/core/Avatar';
import StatusIcon from './StatusIcon';
import LocationOn from '@material-ui/icons/LocationOn';
import ArrowBack from '@material-ui/icons/ArrowBack';
import Menu from '@material-ui/core/Menu';

const statusArr = [
  {
    title: 'Online',
    value: 'online'
  },
  {
    title: 'Away',
    value: 'away'
  },
  {
    title: 'Do not disturb',
    value: 'do-not-disturb'
  },
  {
    title: 'Offline',
    value: 'offline'
  }
];

const ChatsSidebar = (props: any) => {
  const history = useHistory();
  const { selectedMeetingInfo } = useMeeting();
  const { user } = useUser();
  const [statusMenuEl, setStatusMenuEl] = useState(null);
  const [searchText, setSearchText] = useState('');

  // const { setSelectedChannelIndex } = useMeeting();
  // const onClickGroupChat = () => {
  //   setSelectedChannelIndex(0);
  // };
  const handleStatusMenuClick = (event: any) => {
    event.preventDefault();
    setStatusMenuEl(event.currentTarget);
  };

  const handleStatusClose = (event: any) => {
    event.preventDefault();
    setStatusMenuEl(null);
  };

  const handleStatusSelect = (event: any, status: any) => {
    event.preventDefault();
    setStatusMenuEl(null);
  }

  return (
    <div className="flex flex-col flex-auto h-full">
      <div className="w-full">
        {useMemo(() => {
          const onGoMeetings = (e: any) => {
            e.preventDefault();
            history.push('/meetings');
          }
          return (
            <>
              <div className="flex px-4 py-6 items-center">
                <div onClick={(e: any) => onGoMeetings(e)} className="cursor-pointer">
                  <ArrowBack></ArrowBack>
                </div>
                <div className="relative ml-4">

                  <Avatar
                    style={{ width: "3.3rem", height: "3.3rem" }}
                    src={process.env.REACT_APP_BASE_URL + user.user.avatar}
                  >
                  </Avatar>
                  <div
                    className="absolute right-0 bottom-0 z-10 cursor-pointer"
                    aria-haspopup="true"
                    onClick={handleStatusMenuClick}
                    role="button"
                    tabIndex={0}
                  >
                    <StatusIcon status="online" />
                  </div>

                  <Menu
                    id="status-switch"
                    anchorEl={statusMenuEl}
                    open={Boolean(statusMenuEl)}
                    onClose={handleStatusClose}
                  >
                    {statusArr.map(status => (
                      <div className="flex items-center px-4 py-2" onClick={(ev: any) => handleStatusSelect(ev, status.value)} key={status.value}>
                        <div>
                          <StatusIcon status={status.value} />
                        </div>
                        <p className="ml-2">{status.title}</p>
                      </div>
                    ))}
                  </Menu>
                </div>
                <div className="flex flex-col justify-between ml-4">
                  <p className="font-medium text-blue-600 text-lg">{user.user.full_name}</p>
                  <div className="flex">
                    <LocationOn style={{ fill: "gray" }} />
                    <p>UK</p>
                  </div>
                </div>
              </div>
              <div className="relative my-2 flex mx-4">
                <span className="absolute inset-y-0 pl-4 flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="h-4 w-4 text-gray-400"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input
                  value={searchText}
                  onChange={(e: any) => setSearchText(e.target.value)}
                  type="text"
                  placeholder="Search"
                  className="py-3 pl-10 rounded-full text-xs w-full placeholder-gray-400 outline-none focus:outline-none">
                </input>
              </div>

              {selectedMeetingInfo.channels.map((channel: any, i: any) => (
                channel.full_name.toLowerCase().includes(searchText.toLowerCase()) ?
                  <ContactListItem
                    key={i}
                    index={i}
                    channel={channel}
                  /> : null
              ))}

            </>
          );
        }, [searchText, user.user.full_name, user.user.avatar, selectedMeetingInfo.channels, statusMenuEl, history])}
      </div>
    </div>
  );
}

export default ChatsSidebar;
