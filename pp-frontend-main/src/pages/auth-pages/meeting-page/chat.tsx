import { useHistory } from "react-router-dom";
import Avatar from '@material-ui/core/Avatar';
import Icon from '@material-ui/core/Icon';
import { makeStyles } from '@material-ui/core/styles';
import clsx from 'clsx';
import moment from 'moment/moment';
import StatusIcon from './StatusIcon';
import { useState, useRef, useEffect } from 'react';
import { useMeeting, useUser } from "../../../store/hooks";
import SweetAlert from 'react-bootstrap-sweetalert';
import ContractModal from '../../../components/Contract-Modal'

const useStyles = makeStyles(theme => ({
  messageRow: {
    '&.contact': {
      '& .bubble': {
        backgroundColor: "rgb(226,232,240)",
        color: "black",
        borderTopRightRadius: 10,
        borderBottomRightRadius: 10,
        marginBottom: '1px',
        wordBreak: "break-all",
        width: "fit-content",
      },
      '&.first-of-group': {
        '& .bubble': {
          borderTopLeftRadius: 10
        }
      },
      '&.last-of-group': {
        marginBottom: "1.5rem",
        '& .bubble': {
          borderBottomLeftRadius: 10,
        }
      }
    },
    '&.me': {
      '& .avatar': {
        order: 2,
        margin: '0 0 0 16px'
      },
      '& .bubble': {
        marginLeft: 'auto',
        backgroundColor: "rgb(49,130,206)",
        color: theme.palette.primary.contrastText,
        borderTopLeftRadius: 10,
        borderBottomLeftRadius: 10,
        marginBottom: '1px',
        wordBreak: "break-all",
        width: "fit-content",
      },
      '&.first-of-group': {
        '& .bubble': {
          borderTopRightRadius: 10
        }
      },

      '&.last-of-group': {
        marginBottom: "1.5rem",
        '& .bubble': {
          borderBottomRightRadius: 10,
        }
      }
    },
    '&.first-of-group': {
      '& .bubble': {
        borderTopLeftRadius: 10,
      }
    },
    '&.last-of-group': {
      '& .bubble': {
        borderBottomLeftRadius: 10,
      }
    }
  }
}));

const Chat = (props: any) => {
  const history = useHistory();
  const messageEl = useRef<HTMLDivElement>(null);
  const { selectedMeetingInfo, selectedChannelIndex, channelUnread, sendMessage } = useMeeting();
  const { user } = useUser();
  const classes = useStyles(props);
  const [messageText, setMessageText] = useState('');
  const [paymentAlert, setPaymentAlert] = useState(false);
  const [showModal, setShowModal] = useState(false);

  const shouldShowContactAvatar = (item: any, i: any) => {
    if (item.user_id !== user.user.user_id)
      return true;
    return false;
  }

  const isFirstMessageOfGroup = (item: any, i: any) => {
    if (i === 0)
      return true;

    if (selectedMeetingInfo.channels[selectedChannelIndex].messages[i - 1]) {
      if (selectedMeetingInfo.channels[selectedChannelIndex].messages[i - 1].user_id !== item.user_id)
        return true;
      else {
        const date1 = new Date(selectedMeetingInfo.channels[selectedChannelIndex].messages[i].created_at).getTime();
        const date2 = new Date(selectedMeetingInfo.channels[selectedChannelIndex].messages[i - 1].created_at).getTime();

        if (date1 - date2 > 180000)
          return true;
      }
    }
    return false;
  }

  const isLastMessageOfGroup = (item: any, i: any) => {
    if (i === selectedMeetingInfo.channels[selectedChannelIndex].messages.length - 1)
      return true;

    if (selectedMeetingInfo.channels[selectedChannelIndex].messages[i + 1]) {
      if (selectedMeetingInfo.channels[selectedChannelIndex].messages[i + 1].user_id !== item.user_id)
        return true;
      else {
        const date1 = new Date(selectedMeetingInfo.channels[selectedChannelIndex].messages[i + 1].created_at).getTime();
        const date2 = new Date(selectedMeetingInfo.channels[selectedChannelIndex].messages[i].created_at).getTime();

        if (date1 - date2 > 180000)
          return true;
      }
    }
  }

  const onInputChange = (ev: any) => {
    setMessageText(ev.target.value);
  }

  const onViewProfile = () => {
    history.push('/profile/' + selectedMeetingInfo.channels[selectedChannelIndex].fre_id);
  }

  const differceFromToday = (str: any) => {
    let date1 = new Date().setHours(0, 0, 0, 0);
    let date2 = new Date(str).setHours(0, 0, 0, 0);

    return (date1 - date2) / (24 * 60 * 60 * 1000);
  }

  const onMessageSubmit = (ev: any) => {
    ev.preventDefault();
    if (messageText === '') {
      return;
    }

    sendMessage({
      user_id: user.user.user_id,
      msg_body: messageText,
      channel_id: selectedMeetingInfo.channels[selectedChannelIndex].channel_id,
      created_at: new Date()
    });

    setMessageText('');
  }

  const onKeyDown = (e: any) => {
    if (e.keyCode === 13 && e.ctrlKey)
      onMessageSubmit(e)
  }

  const onSendOffer = (e: any) => {
    e.preventDefault();
    if (user.user.payment_email === '' || user.user.payment_email === null) {
      setPaymentAlert(true);
      return;
    }

    setShowModal(true);
  }

  const onKeyUp = (e: any) => {
    if (e.keyCode === 13 || e.keyCode === 8) {
      e.target.style.height = 'inherit';
      e.target.style.height = `${e.target.scrollHeight}px`;
      var target: any = messageEl.current;
      if (target)
        target.scroll({ top: target.scrollHeight, behavior: 'smooth' });
    }
  }

  useEffect(() => {
    if (messageEl.current !== null && selectedChannelIndex !== -1) {
      var target: any = messageEl.current;
      if (target)
        target.scroll({ top: target.scrollHeight });
      messageEl.current.addEventListener('DOMNodeInserted', (event: any) => {
        const { currentTarget: target } = event;
        target.scroll({ top: target.scrollHeight, behavior: 'smooth' });
      });
    }
  }, [selectedChannelIndex]);// eslint-disable-line react-hooks/exhaustive-deps

  return (
    <div className={clsx('flex flex-col relative', props.className)}>
      {selectedChannelIndex !== -1 ? (<>
        {user.user.user_role === 'client' ?
          (selectedChannelIndex === 0 ? (<div className="flex pt-6 pl-10 pr-10 justify-between items-center">
            <div className="flex">
              <Avatar
                style={{ width: "3.3rem", height: "3.3rem" }}
                src="/icons/avatar.svg"
                alt="Group Chat"
              >GC</Avatar>
              <div className="flex flex-col justify-between ml-4">
                <p className="font-medium text-blue-600 pl-1">Group Chat</p>
                <div className="flex">
                  <Icon className="text-128">
                    notifications
                  </Icon>
                  Freelancers now asking the question
              </div>
              </div>
            </div>

            <div className="flex ml-6">
              <Icon className="text-128">
                alarm
							</Icon>
          2:00
        </div>
            <button className="ml-3 px-4 py-3 tracking-wide text-white transition-colors duration-200 transform bg-blue-500 rounded hover:bg-blue-200 focus:outline-none">
              End the QA and make the freechat
            </button>
          </div>) : (
            <div className="flex pt-6 pl-10 pr-10 justify-between items-center">
              <div className="flex">
                <Avatar
                  style={{ width: "3.3rem", height: "3.3rem" }}
                  src={process.env.REACT_APP_BASE_URL + selectedMeetingInfo.channels[selectedChannelIndex].avatar}
                  alt={selectedMeetingInfo.channels[selectedChannelIndex].full_name}>
                  {selectedMeetingInfo.channels[selectedChannelIndex].first_name.charAt(0).toUpperCase() + selectedMeetingInfo.channels[selectedChannelIndex].last_name.charAt(0).toUpperCase()}
                </Avatar>
                <div className="flex flex-col justify-between ml-4">
                  <div className="flex items-center">
                    <p className="font-medium text-blue-600 mr-1">{selectedMeetingInfo.channels[selectedChannelIndex].full_name}</p>
                    {selectedChannelIndex === 1 ? (<StatusIcon status="online" />) : (selectedChannelIndex === 2 ? <StatusIcon status="away" /> : (selectedChannelIndex === 3 ? <StatusIcon status="do-not-disturb" /> : <StatusIcon status="offline" />))}
                  </div>
                  <p>skype.....</p>
                </div>

              </div>
              <div>
                <button onClick={onViewProfile} className="ml-3 px-4 py-3 tracking-wide transition-colors duration-200 transform text-blue-500 bg-white rounded hover:bg-blue-200 focus:outline-none border-solid border-2 border-blue-500">
                  View Profile
              </button>

                {selectedMeetingInfo.channels[selectedChannelIndex].has_contract === false ?
                  <button
                    className="ml-3 px-4 py-3 tracking-wide text-white transition-colors duration-200 transform bg-blue-500 rounded hover:bg-blue-200 focus:outline-none"
                    onClick={(e: any) => onSendOffer(e)}
                  >
                    Send Offer
              </button> : null}
              </div>
            </div>
          )) : (<div className="flex pt-6 pl-6 pr-10 justify-between items-center">
            <div className="flex items-center">
              <Avatar
                style={{ width: "3.3rem", height: "3.3rem" }}
                src={process.env.REACT_APP_BASE_URL + selectedMeetingInfo.channels[selectedChannelIndex].clt_avatar}
              >
              </Avatar>
              <p className="ml-2 font-medium text-blue-600"></p>
            </div>
            <div className="flex">
              <Icon className="text-128">
                notifications
            </Icon>
        Client Asked the question
      </div>
            <div className="flex ml-6">
              <Icon className="text-128">
                alarm
            </Icon>
        2:00
      </div>
          </div>)
        }
        {
          selectedMeetingInfo.channels[selectedChannelIndex] && selectedMeetingInfo.channels[selectedChannelIndex].messages.length > 0 ? (
            <div className="flex flex-col pt-6 px-6 my-4 h-3/4 overflow-auto bg-white box-content" style={{ borderTop: "1px solid lightgray" }} ref={messageEl}>
              <div className="max-w-screen-lg mx-auto w-full">
                {selectedChannelIndex === 0 ? (
                  <div className="text-center mx-16 mb-4">
                    <p className="text-black text-3xl">{selectedMeetingInfo.title}</p>
                    <p className="break-words whitespace-pre-line">{selectedMeetingInfo.description}</p>
                  </div>
                ) : null}
                {selectedMeetingInfo.channels[selectedChannelIndex].messages.map((item: any, i: any) => {
                  return (<div key={i}>
                    {(i === 0 || (i > 0 && (differceFromToday(item.created_at) !== differceFromToday(selectedMeetingInfo.channels[selectedChannelIndex].messages[i - 1].created_at)))) ? (
                      <div className="w-full flex items-center my-4" style={{ paddingLeft: "50px" }}>
                        <div className="w-full bg-gray-300" style={{ height: "1px" }}></div>
                        <div className="px-3 text-sm text-gray-500 whitespace-nowrap">
                          {differceFromToday(item.created_at) === 0 ? "Today" :
                            ((differceFromToday(item.created_at) === 1) ? "Yesterday" :
                              ((differceFromToday(item.created_at) < 4) ? moment(new Date(item.created_at)).format('dddd')
                                : moment(new Date(item.created_at)).format('dddd, MMMM D, YYYY')))
                          }
                        </div>
                        <div className="w-full bg-gray-300" style={{ height: "1px" }}></div>
                      </div>
                    ) : null}
                    {channelUnread !== 0 && (selectedMeetingInfo.channels[selectedChannelIndex].messages.length - i) === channelUnread ? (
                      <div className="w-full flex items-center my-4" style={{ paddingLeft: "50px" }}>
                        <div className="w-full bg-blue-500" style={{ height: "2px" }}></div>
                        <div className="px-3 text-sm text-blue-500 whitespace-nowrap">Unread Messages</div>
                        <div className="w-full bg-blue-500" style={{ height: "2px" }}></div>
                      </div>
                    ) : null}

                    <div
                      className={clsx(
                        classes.messageRow,
                        'flex flex-grow-0 flex-shrink-0 items-start relative',
                        { me: item.user_id === user.user.user_id },
                        { contact: item.user_id !== user.user.user_id },
                        { 'first-of-group': isFirstMessageOfGroup(item, i) },
                        { 'last-of-group': isLastMessageOfGroup(item, i) },
                        i + 1 === selectedMeetingInfo.channels[selectedChannelIndex].messages.length && 'pb-2'
                      )}
                    >
                      {shouldShowContactAvatar(item, i) ? (
                        isFirstMessageOfGroup(item, i) ? (
                          <>
                            {selectedChannelIndex === 0 ? (
                              selectedMeetingInfo.channels[selectedChannelIndex].clt_id === item.user_id ?
                                <Avatar
                                  className="avatar absolute ltr:left-0 rtl:right-0 m-0"
                                  src={process.env.REACT_APP_BASE_URL + selectedMeetingInfo.channels[selectedChannelIndex].clt_avatar}
                                >CL</Avatar> :
                                <Avatar
                                  className="avatar absolute ltr:left-0 rtl:right-0 m-0"
                                  src="/icons/avatar.svg"
                                  alt={selectedMeetingInfo.contacts[item.user_id]}
                                />
                            ) :
                              (
                                user.user.user_role === "freelancer" ?
                                  <Avatar
                                    className="avatar absolute ltr:left-0 rtl:right-0 m-0"
                                    src={process.env.REACT_APP_BASE_URL + selectedMeetingInfo.channels[selectedChannelIndex].clt_avatar}
                                  >CL</Avatar> : <Avatar
                                    className="avatar absolute ltr:left-0 rtl:right-0 m-0"
                                    src={process.env.REACT_APP_BASE_URL + selectedMeetingInfo.channels[selectedChannelIndex].avatar}
                                  >
                                    {selectedMeetingInfo.channels[selectedChannelIndex].first_name.charAt(0).toUpperCase() + selectedMeetingInfo.channels[selectedChannelIndex].last_name.charAt(0).toUpperCase()}
                                  </Avatar>
                              )
                            }
                            <div style={{ marginLeft: "10px" }}>
                              {isFirstMessageOfGroup(item, i) ?
                                (<p
                                  className="time w-full text-xs ltr:left-0 rtl:right-0 bottom-0 whitespace-no-wrap mb-1"
                                >
                                  {moment(new Date(item.created_at)).format('h:mm A')}
                                </p>) : null}
                              <div className="bubble flex relative items-center justify-center px-4 py-3 max-w-full mr-auto shadow-1">
                                <div className="leading-tight whitespace-pre-wrap">{item.msg_body}</div>
                              </div>
                            </div>
                          </>) : (
                          <div style={{ marginLeft: "50px" }}>
                            {isFirstMessageOfGroup(item, i) ?
                              (<p
                                className="time w-full text-xs ltr:left-0 rtl:right-0 bottom-0 whitespace-no-wrap mb-1"
                              >
                                {moment(new Date(item.created_at)).format('h:mm A')}
                              </p>) : null}
                            <div className="bubble flex relative items-center justify-center px-4 py-3 w-auto shadow-1 mr-auto" style={{ width: "fit-content" }}>
                              <div className="leading-tight whitespace-pre-wrap">{item.msg_body}</div>
                            </div>
                          </div>)
                      ) : (
                        <div className="ml-auto">
                          {isFirstMessageOfGroup(item, i) ?
                            (<p
                              className="time w-full text-xs ltr:left-0 rtl:right-0 bottom-0 whitespace-no-wrap mb-1 flex justify-end"
                            >
                              {moment(new Date(item.created_at)).format('h:mm A')}
                            </p>) : null}
                          <div className="ml-auto bubble flex relative items-center justify-center px-4 py-3 shadow-1" style={{ width: "fit-content" }}>
                            <div className="leading-tight whitespace-pre-wrap">{item.msg_body}</div>
                          </div>
                        </div>
                      )}
                    </div>
                  </div>
                  );
                })}
              </div>
            </div>
          ) : (
            <div className="flex flex-col flex-1">
              <div className="flex flex-col flex-1 items-center justify-center">
                <Icon className="text-128" color="disabled">
                  chat
							</Icon>
              </div>
            </div>
          )
        }

        <div className="flex pb-1 px-4 max-w-screen-lg mx-auto w-full items-end">
          <textarea
            className="resize-none border rounded-3xl w-full px-8 py-2 focus:outline-none bg-gray-100 overflow-hidden max-h-40"
            placeholder={user.user.user_role === 'client' ? 'Enter the question' : 'Answer for the question'}
            onKeyDown={(e: any) => onKeyDown(e)}
            onKeyUp={(e: any) => onKeyUp(e)}
            onChange={onInputChange}
            value={messageText}
            rows={1}
          ></textarea>

          {messageText === '' ? (user.user.user_role === 'client' ?
            (<button className="w-44 ml-3 px-4 py-3 tracking-wide text-black transition-colors duration-200 transform bg-white rounded hover:bg-gray-300 focus:outline-none border-solid border-2 border-gray-300">
              Waiting Time
            </button>) : (
              <p className="w-44 ml-3 px-4 py-3 text-blue-500 font-semibold">You are {selectedMeetingInfo.contacts[user.user.user_id]}</p>
            )) :
            (<button onClick={onMessageSubmit} className="w-44 ml-3 px-4 py-3 tracking-wide text-white transition-colors duration-200 transform bg-blue-500 rounded hover:bg-blue-200 focus:outline-none">
              {selectedChannelIndex ? 'Send' : 'Ask'}
            </button>
            )
          }
        </div>
        <div className="pb-6 flex justify-end max-w-screen-lg mx-auto w-full">
          <p className="text-sm">Press <i>Ctrl+Enter</i> to send a message.</p>
        </div></>) :
        (
          <div className="flex flex-col flex-1">
            <div className="flex flex-col flex-1 items-center justify-center">
              <div className="text-3xl text-gray-500">Select to start chat</div>
              <Icon className="text-5xl" color="disabled">
                chat
							</Icon>
            </div>
          </div>
        )}
      <SweetAlert
        title=""
        show={paymentAlert}
        type="warning"
        onConfirm={response => setPaymentAlert(false)}
      >
        Please verify paypal address.
      </SweetAlert>
      {showModal ? <ContractModal setShowModal={setShowModal}></ContractModal> : null}
    </div >
  );
}

export default Chat;
