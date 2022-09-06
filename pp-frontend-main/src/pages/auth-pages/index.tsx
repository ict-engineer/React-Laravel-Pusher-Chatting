import React, { FunctionComponent, useEffect } from 'react';
import { Route, Redirect, Switch } from 'react-router-dom';

import HomePage from '../unauth-pages/home-page'
import ProfilePage from './profile-page';
import SettingPage from './setting-page';
import MeetingPage from './meeting-page'
import TransactionPage from './transaction-page'
import MeetingsPage from './meetings-page'
import TicketsPage from './tickets-page'
import SelectMeetingPage from '../unauth-pages/select-meeting-page'
import PostJobPage from '../unauth-pages/post-job-page'
import MatchFreelancersPage from '../unauth-pages/match-freelancers-page'
import TicketViewPage from './tickets-page/ticketView'
import { useUser, useMeeting, useTransaction } from '../../store/hooks'
import Pusher from 'pusher-js'

const UnAuthPages: FunctionComponent = () => {
  const { user, setPusherChannel, setUnreadMeeting, setUnreadTransaction } = useUser();
  const { loadNewMessage, selectedMeetingInfo } = useMeeting();
  const { loadNewMessageTr, handleContractStatusChange, transactions } = useTransaction();

  useEffect(() => {
    if (user.user.user_id !== '') {
      let options = {
        cluster: process.env.REACT_APP_PUSHER_APP_CLUSTER
      };
      let key: any = process.env.REACT_APP_PUSHER_APP_KEY;
      var pusher = new Pusher(key, options);
      var channel = pusher.subscribe('chat-new-channel-' + user.user.user_id);
      setPusherChannel(channel);
      channel.bind('MessageSent', async function (message: any) {
        if (message.type === 'contract') {
          let unread = user.user.unreadTransaction + 1;
          setUnreadTransaction(unread);

          handleContractStatusChange(message);
        }
        else if (message.type === 'message' || message.type === 'timetrack' || message.type === "review") {
          console.log("asdf");
          let unread = user.user.unreadTransaction + 1;
          console.log(unread);
          setUnreadTransaction(unread);

          loadNewMessageTr({ ...message.data, chat_type: message.type });
        }
        else if (message.type === 'm_chat') {
          let unread = user.user.unreadMeeting + 1;
          setUnreadMeeting(unread);

          loadNewMessage(message.data);
        }
      });
    }
  }, [user.user]);// eslint-disable-line react-hooks/exhaustive-deps

  return (
    <React.Fragment>
      <Switch>
        <Route exact path="/client/post-job" component={PostJobPage} />
        <Route exact path="/client/match-freelancers" component={MatchFreelancersPage} />
        <Route exact path="/profile/:id" component={ProfilePage} />
        <Route exact path="/setting" component={SettingPage} />
        <Route exact path="/tickets" component={TicketsPage} />
        <Route exact path="/ticket/:id" component={TicketViewPage} />
        <Route exact path="/transaction" component={TransactionPage} />
        <Route exact path="/client/select-meeting" component={SelectMeetingPage} />
        <Route exact path="/meetings" component={MeetingsPage} />
        <Route exact path="/meeting/:id" component={MeetingPage} />
        {user.token !== "" ?
          (<Redirect to="/profile/me"></Redirect>)
          : (
            <>
              <Route exact path="/home" component={HomePage} />
              <Redirect to="/home"></Redirect>
            </>)
        }
      </Switch>
    </React.Fragment>
  );
};

export default UnAuthPages;
