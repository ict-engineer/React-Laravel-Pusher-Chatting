import { useHistory } from "react-router-dom";
import { useEffect, useState } from "react";
import Header from '../../../layouts/header'
import { useMeeting, useUser } from '../../../store/hooks'
import RemoveRedEye from '@material-ui/icons/RemoveRedEye'

function MeetingsPage() {
  const { setSelectedChannelIndex, getMeetings, meetings } = useMeeting();
  const { setUnreadMeeting } = useUser();
  const [searchText, setSearchText] = useState('');
  const history = useHistory();

  const handleGoMeeting = (e: any, id: any) => {
    e.preventDefault();
    history.push('/meeting/' + id);
  }
  useEffect(() => {
    const initial = async () => {
      await getMeetings();
      setUnreadMeeting(0);
    };

    initial();
    setSelectedChannelIndex(-1);
  }, []);// eslint-disable-line react-hooks/exhaustive-deps

  return (
    <>
      <Header></Header>
      <div className="hero-area h-80">
        <div className="text-center">
          <p className="text-blue-500 pt-40 text-5xl">Meetings</p>
          <div className="flex justify-center mt-8">
            <div className="relative my-2 flex w-96">
              <span className="absolute inset-y-0 pl-4 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="h-4 w-4 text-gray-400"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
              </span>
              <input
                value={searchText}
                onChange={(e: any) => setSearchText(e.target.value)}
                type="text"
                placeholder="Search Meeting"
                className="shadow-xl py-3 pl-10 rounded-full text-xs w-full placeholder-gray-500 outline-none focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring"></input>
            </div>
          </div>
        </div>
      </div>
      <div className="overflow-auto bg-gray-200" style={{ height: "calc(100% - 20rem" }}>
        <div className="bg-white container p-4 shadow-xl mt-4">
          <table className="min-w-full divide-y divide-gray-200">
            <thead className="bg-gray-50">
              <tr>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Meeting Title
              </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Last Message
              </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Created
              </th>
                <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
              </th>
                <th scope="col" className="relative px-6 py-3">
                  <span className="sr-only"></span>
                </th>
              </tr>
            </thead>
            <tbody className="bg-white divide-y divide-gray-200">
              {meetings.map((meeting: any, i: any) => (
                meeting.title.toLowerCase().includes(searchText.toLowerCase()) ?
                  (<tr key={i}>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="flex items-center">
                        <div className="text font-medium text-gray-900">
                          {meeting.title}
                        </div>
                      </div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <div className="text-sm text-gray-500">{meeting.last_message}</div>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {meeting.created}
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap">
                      <span className="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                        Active
                        </span>
                    </td>
                    <td className="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                      <a className="cursor-pointer" href="void(0)" onClick={(e: any) => handleGoMeeting(e, meeting.id)}>
                        <RemoveRedEye></RemoveRedEye>
                      </a>
                    </td>
                  </tr>)
                  : (null)
              ))}
            </tbody>
          </table>

        </div>
      </div>
    </>
  );
}

export default MeetingsPage;
