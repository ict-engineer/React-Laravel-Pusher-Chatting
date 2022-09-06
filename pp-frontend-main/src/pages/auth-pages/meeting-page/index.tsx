import SwipeableDrawer from '@material-ui/core/SwipeableDrawer';
import clsx from 'clsx';
import { useEffect } from 'react';
import Chat from './chat';
import ChatsSidebar from './ChatSidebar';
import Header from '../../../layouts/header'
// import ContactSidebar from './ContactSidebar';
import { useMeeting } from "../../../store/hooks";

const MeetingPage = (props: any) => {
  const { contactSidebarOpen, setContactsSidebar, getMeetingInfoById } = useMeeting();

  useEffect(() => {
    if (props.match.params.id !== null && props.match.params.id !== undefined && props.match.params.id !== '') {
      getMeetingInfoById(props.match.params.id);
    }
  }, []);// eslint-disable-line react-hooks/exhaustive-deps

  return (
    <>
      <Header></Header>
      <div className="bg-gray-100 h-screen pt-20 pb-4 px-4">
        <div className="flex flex-wrap content-evenly bg-white shadow h-full">
          <div className='w-full h-full flex flex-col'>
            <div className="flex h-full">
              <div className="hidden sm:block w-full max-w-xs" style={{ backgroundColor: "rgb(237,242,247)" }}>
                <ChatsSidebar />
              </div>

              <main className='z-10 flex flex-col w-full h-full'>
                {/* {!selectedMeetingInfo.chat ? (
                  <div className="flex flex-col flex-1 items-center justify-center p-24">
                    <Typography variant="h6" className="mt-24 mb-12 text-32 font-700">
                      Chat App
								</Typography>
                    <Typography
                      className="hidden md:flex px-16 pb-24 text-16 text-center"
                      color="textSecondary"
                    >
                      Select a contact to start a conversation!..
								</Typography>
                    <Button
                      variant="outlined"
                      color="primary"
                      className="flex md:hidden normal-case"
                    >
                      Select a contact to start a conversation!..
								</Button>
                  </div>
                ) : (
                  <> */}
                <div className="relative h-full">
                  <Chat className="flex flex-1 z-10 h-full" />
                </div>
                {/* </>
                )} */}
              </main>

              <SwipeableDrawer
                className="h-full absolute z-30"
                variant="temporary"
                anchor="right"
                open={contactSidebarOpen}
                onOpen={ev => { }}
                onClose={() => setContactsSidebar(false)}
                classes={{
                  paper: clsx('absolute ltr:right-0 rtl:left-0')
                }}
                style={{ position: 'absolute' }}
                ModalProps={{
                  keepMounted: true,
                  disablePortal: true,
                  BackdropProps: {
                    classes: {
                      root: 'absolute'
                    }
                  }
                }}
              >
                {/* <ContactSidebar /> */}
              </SwipeableDrawer>
            </div>

          </div>
        </div>
      </div>
    </>
  );
}

export default MeetingPage;