import { useHistory } from "react-router-dom";
import Avatar from '@material-ui/core/Avatar';
import { makeStyles } from '@material-ui/core/styles';
import clsx from 'clsx';
import moment from 'moment/moment';
import Contract from '../../../components/Contract'
import Review from '../../../components/Review'
import TimeTrack from '../../../components/TimeTrack'
import { useState, useRef, useEffect } from 'react';
import { useTransaction, useUser } from "../../../store/hooks";
import TicketModal from '../../../components/Ticket-Modal'
import TrackModal from '../../../components/Track-Modal'
import ReviewModal from '../../../components/Review-Modal'
import SweetAlert from 'react-bootstrap-sweetalert';

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

const Transaction = (props: any) => {
  const history = useHistory();
  const messageEl = useRef<HTMLDivElement>(null);
  const { sendMessageTr } = useTransaction();
  const { transactions, transactionUnread, selectedTransactionId, changeContract } = useTransaction();
  const { user } = useUser();
  const classes = useStyles(props);
  const [messageText, setMessageText] = useState('');
  const [showSupportModal, setShowSupportModal] = useState(false);
  const [showTrackModal, setShowTrackModal] = useState(false);
  const [showReviewModal, setShowReviewModal] = useState(false);
  const [paymentAlert, setPaymentAlert] = useState(false);
  const [alertContent, setAlertContent] = useState('');

  const onRequestSupport = () => {
    setShowSupportModal(true);
  }

  console.log(transactions);
  const shouldShowContactAvatar = (item: any, i: any) => {
    if (item.user_id !== user.user.user_id)
      return true;
    return false;
  }

  const onInputChange = (ev: any) => {
    setMessageText(ev.target.value);
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

    sendMessageTr({
      user_id: user.user.user_id,
      msg_body: messageText,
      channel_id: transactions[selectedTransactionId].channel_id,
      created_at: new Date(),
      chat_type: "message",
    });

    setMessageText('');
  }

  const onKeyDown = (e: any) => {
    if (e.keyCode === 13 && e.ctrlKey)
      onMessageSubmit(e)
  }

  const onViewProfile = () => {
    if (user.user.user_role === "client")
      history.push('/profile/' + transactions[selectedTransactionId].fre_id);
    else if (user.user.user_role === "freelancer")
      history.push('/profile/' + transactions[selectedTransactionId].clt_id);
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

  const onEndContract = async () => {
    const result = await changeContract({ status: "ended", id: transactions[selectedTransactionId].contract_id });
    if (result)
      setShowReviewModal(true);
  }

  useEffect(() => {
    if (messageEl.current !== null && selectedTransactionId !== -1) {
      var target: any = messageEl.current;
      if (target)
        target.scroll({ top: target.scrollHeight });
      messageEl.current.addEventListener('DOMNodeInserted', (event: any) => {
        const { currentTarget: target } = event;
        target.scroll({ top: target.scrollHeight, behavior: 'smooth' });
      });
    }

    if (selectedTransactionId !== -1) {
      if (transactions[selectedTransactionId].paid_first_invoice === false) {
        if (transactions[selectedTransactionId].has_pending_invoice === true) {
          setAlertContent("The client's funds are not on escrow yet. The client didn't pay your works now. Please wait until the client pay it.");
          setPaymentAlert(true);
        }
        else {
          setAlertContent("The client's funds are not on escrow yet.");
          setPaymentAlert(true);
        }
      }
      else if (transactions[selectedTransactionId].has_pending_invoice === true) {
        setAlertContent("The client didn't pay your works now. Please wait until the client pay it.");
        setPaymentAlert(true);
      }
    }
  }, [selectedTransactionId]);// eslint-disable-line react-hooks/exhaustive-deps

  return (
    <div className={clsx('flex flex-col relative', props.className)}>
      {transactions.length !== 0 && selectedTransactionId !== -1 &&
        (<>
          <div className="flex pt-6 pl-10 pr-10 items-center justify-between">
            <div className="flex">
              <Avatar
                onClick={onViewProfile}
                className="cursor-pointer"
                style={{ width: "3.3rem", height: "3.3rem" }}
                src={process.env.REACT_APP_BASE_URL + transactions[selectedTransactionId].avatar}
                alt={transactions[selectedTransactionId].full_name}
              >
                {transactions[selectedTransactionId].first_name === "" ? null : (transactions[selectedTransactionId].first_name.charAt(0).toUpperCase() + transactions[selectedTransactionId].last_name.charAt(0).toUpperCase())}
              </Avatar>
              <div>
                <p className="ml-2 font-medium text-xl text-blue-600 cursor-pointer" onClick={onViewProfile}>{transactions[selectedTransactionId].full_name}</p>
                <p className="ml-2 text-gray-400">skype....</p>
              </div>
            </div>
            <div>
              {user.user.user_role === 'client' && transactions[selectedTransactionId].contract_status === 'accepted' ?
                <button className="secondary-btn mx-1" onClick={(e: any) => onEndContract()}>End Contract</button>
                : (user.user.user_role === 'freelancer' ? <button className="secondary-btn mx-1" onClick={(e: any) => setShowTrackModal(true)}>Track Time</button> : null)}
              {
                transactions[selectedTransactionId].contract_status === 'ended' && transactions[selectedTransactionId].has_review === false ?
                  <button className="secondary-btn mx-1" onClick={(e: any) => setShowReviewModal(true)}>Send Review</button> :
                  null
              }
            </div>
          </div>
          <div className="flex flex-col pt-6 px-6 my-4 h-3/4 overflow-auto bg-white box-content" style={{ borderTop: "1px solid lightgray" }} ref={messageEl}>
            <div className="max-w-screen-lg mx-auto w-full">
              <div className="mx-8 mt-4 mb-8 border-gray-400 border-solid border-2 px-8 py-4 text-center">
                <p className="text-green-500 text-2xl font-medium">Title: {transactions[selectedTransactionId].job_title}</p>
                <p className="text-gray-400 text-sm whitespace-pre-line">{transactions[selectedTransactionId].job_desc}</p>
              </div>
              {transactions[selectedTransactionId].history.map((item: any, i: any) => {
                return (<div key={i}>
                  {(i === 0 || (i > 0 && (differceFromToday(item.created_at) !== differceFromToday(transactions[selectedTransactionId].history[i - 1].created_at)))) ? (
                    <div className="w-full flex items-center my-4" style={{ paddingLeft: "45px", paddingRight: "45px" }}>
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

                  {transactionUnread !== 0 && (transactions[selectedTransactionId].history.length - i) === transactionUnread ? (
                    <div className="w-full flex items-center my-4" style={{ paddingLeft: "50px" }}>
                      <div className="w-full bg-blue-500" style={{ height: "2px" }}></div>
                      <div className="px-3 text-sm text-blue-500 whitespace-nowrap">Unread Messages</div>
                      <div className="w-full bg-blue-500" style={{ height: "2px" }}></div>
                    </div>
                  ) : null}

                  {(item.chat_type === "message" || item.chat_type === "m_chat" ? (
                    <div
                      className={clsx(
                        classes.messageRow,
                        'flex flex-grow-0 flex-shrink-0 items-start relative',
                        { me: item.user_id === user.user.user_id },
                        { contact: item.user_id !== user.user.user_id },
                        'pb-2'
                      )}
                    >
                      {shouldShowContactAvatar(item, i) ? (
                        <div className="mb-2 flex">
                          <Avatar
                            className="avatar absolute ltr:left-0 rtl:right-0 m-0"
                            src={process.env.REACT_APP_BASE_URL + transactions[selectedTransactionId].avatar}
                          >
                            {transactions[selectedTransactionId].first_name === "" ? null : (transactions[selectedTransactionId].first_name.charAt(0).toUpperCase() + transactions[selectedTransactionId].last_name.charAt(0).toUpperCase())}
                          </Avatar>
                          <div style={{ marginLeft: "5px" }}>
                            <div className="bubble flex relative items-center justify-center px-4 py-3 max-w-full mr-auto shadow-1">
                              <div className="leading-tight whitespace-pre-wrap">{item.msg_body}</div>
                            </div>
                            <p
                              className="time w-full text-xs ltr:left-0 rtl:right-0 bottom-0 whitespace-no-wrap mb-1"
                            >
                              {moment(new Date(item.created_at)).format('h:mm A')}
                            </p>
                          </div>
                        </div>
                      ) : (
                        <div className="flex flex-col items-end relative w-full mb-2" style={{ paddingRight: "45px" }}>
                          <Avatar
                            style={{ position: "absolute", top: '0', right: '0' }}
                            className="avatar absolute ltr:left-0 rtl:right-0 m-0"
                            src={process.env.REACT_APP_BASE_URL + user.user.avatar}
                          >
                            {user.user.first_name === "" ? null : (user.user.first_name.charAt(0).toUpperCase() + user.user.last_name.charAt(0).toUpperCase())}
                          </Avatar>
                          <div className="ml-auto bubble flex relative items-center justify-center px-4 py-3 shadow-1" style={{ width: "fit-content" }}>
                            <div className="leading-tight whitespace-pre-wrap">{item.msg_body}</div>
                          </div>
                          <p
                            className="time w-full text-xs ltr:left-0 rtl:right-0 bottom-0 whitespace-no-wrap mb-1 flex justify-end"
                          >
                            {moment(new Date(item.created_at)).format('h:mm A')}
                          </p>
                        </div>
                      )}
                    </div>
                  ) : (
                    item.chat_type.includes("contract_") ? (
                      <Contract item={item} index={i} />
                    ) : (item.chat_type === "timetrack" ?
                      <TimeTrack item={item} index={i} /> :
                      (item.chat_type === "review" ?
                        <Review item={item} index={i} /> : null
                      )
                    )
                  ))}
                </div>

                )
              })}

            </div>
          </div>

          <div className="flex pb-1 px-4 max-w-screen-lg mx-auto w-full items-end">
            <textarea
              className="resize-none border rounded-3xl w-full px-8 py-2 focus:outline-none bg-gray-100 overflow-hidden max-h-40"
              placeholder='Type your Message'
              onKeyDown={(e: any) => onKeyDown(e)}
              onKeyUp={(e: any) => onKeyUp(e)}
              onChange={onInputChange}
              value={messageText}
              rows={1}
            ></textarea>
            {messageText === "" ?
              <button onClick={onRequestSupport} className="w-56 ml-3 px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-blue-500 rounded hover:bg-blue-200 focus:outline-none">
                Request Support
              </button> :
              <button onClick={onMessageSubmit} className="w-44 ml-3 px-4 py-3 tracking-wide text-white transition-colors duration-200 transform bg-blue-500 rounded hover:bg-blue-200 focus:outline-none">
                Send
              </button>
            }
          </div>
          <div className="pb-6 flex justify-end max-w-screen-lg mx-auto w-full">
            <p className="text-sm">Press <i>Ctrl+Enter</i> to send a message.</p>
          </div>
        </>)
      }
      {showSupportModal ? <TicketModal setShowModal={setShowSupportModal}></TicketModal> : null}
      {showTrackModal ? <TrackModal setShowModal={setShowTrackModal}></TrackModal> : null}
      {showReviewModal ? <ReviewModal setShowModal={setShowReviewModal}></ReviewModal> : null}
      <SweetAlert
        title=""
        show={paymentAlert}
        type="warning"
        onConfirm={response => setPaymentAlert(false)}
      >
        {alertContent}
      </SweetAlert>
    </div >
  );
}

export default Transaction;
