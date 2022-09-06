import { useHistory } from "react-router-dom";
import { useEffect, useState } from "react";
import { usePostJob, useUser } from "../../../store/hooks";
import { makeStyles } from '@material-ui/core/styles';
import moment from "moment";
import Header from '../../../layouts/header'

const useStyles = makeStyles(theme => ({
  freelancerCard: {
    fontWeight: 400,
    position: "relative",
    display: "flex",
    flexBasis: "100%",
    padding: "2rem",
    margin: "0px 4rem 2rem",
    overflow: "hidden",
    transition: "all .2s",
    boxShadow: "0 2px 3px 0 rgba(0,0,0,.1)",
    background: "#fff",
    border: "1px solid rgba(235,236,237,.5)",
    "&:hover": {
      border: "1px solid #edf0f8",
      boxShadow: "0 7px 15px 0 rgba(0, 0, 0, .1)",
    },
  },
  titleColor: {
    color: "#183a9e",
  }
}));


const MatchFreelancersPage = (props: any) => {
  const history = useHistory();
  const { topFreelancers, getTopFreelancers, addJobInfo } = usePostJob();
  const { user } = useUser();
  const classes = useStyles(props);
  const [date, setDate] = useState(moment().format("YYYY-MM-DD"));
  const [time, setTime] = useState(moment().format("hh:mm"));

  const handleSubmit = async () => {
    if (user.token !== '' && user.token !== null && user.token !== undefined) {
      await addJobInfo();
    }
    history.push('/client/select-meeting');
  }

  useEffect(() => {
    getTopFreelancers();
  }, []);// eslint-disable-line react-hooks/exhaustive-deps

  const showProfilePage = (e: any, id: any) => {
    e.preventDefault();
    history.push('/profile/' + id);
  }

  return (
    <>
      <Header></Header>
      <div className="h-full pt-16 overflow-auto pb-4">
        <div className="hero-area h-80 pt-16">
          <div className="text-center ">
            <p className={classes.titleColor + " text-5xl"}>We matched the top 5 freelancers here.</p>
            <p className="text-lg my-6 text-gray-700">You can setup the meeting time with them now.</p>
            <button onClick={handleSubmit} className="bg-green-400 text-xl py-3 px-8 rounded text-white font-medium mt-4 hover:bg-green-600 focus:outline-none">
              Schedule the meeting now
            </button>
          </div>
        </div>
        <div className="container py-10 mx-auto flex flex-col">
          {topFreelancers.map((freelancer: any, i: any) =>
            <div className={classes.freelancerCard} key={i}>
              <div className="w-1/4 px-4">
                <div className="group relative">
                  <div className="image_box">
                    <a className="w-full mx-auto h-full absolute left-0 top-0" onClick={e => showProfilePage(e, freelancer.user_id)} href="void(0)">
                      {freelancer.avatar === null || freelancer.avatar === undefined || freelancer.avatar === "" ?
                        <img className="w-full h-full" src="../assets/imgs/avatar.png" alt=""></img> :
                        <img className="w-full h-full" src={process.env.REACT_APP_BASE_URL + freelancer.avatar} alt=""></img>}
                    </a>
                  </div>
                  <div onClick={e => showProfilePage(e, freelancer.user_id)} className='group-hover:opacity-80 opacity-0 absolute top-0 left-0 transform w-full h-full bg-blue-500 flex justify-center items-center text-white font-medium cursor-pointer'>
                    View Full Profile
                  </div>
                </div>

                <button onClick={e => showProfilePage(e, freelancer.user_id)} className="primary-btn w-full mt-4 py-4">View {freelancer.first_name}</button>
              </div>
              <div className="w-3/4 px-4">
                <p className="text-blue-700 text-lg font-bold">{freelancer.full_name}</p>
                <p className="text-blue-700 mb-8">English: {freelancer.english_level}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Timezone: EST</p>
                <p>{freelancer.description}</p>
                <a onClick={e => showProfilePage(e, freelancer.user_id)} href="void(0)" className="text-blue-700 cursor-pointer">Show More...</a>
                <div className="flex justify-between px-8 pt-12">
                  <div className="items-center text-center w-20">
                    <p className="text-4xl mb-4">&#x1f917;</p>
                    <p className="font-bold">Quality</p>
                    <p>250</p>
                  </div>
                  <div className="items-center text-center w-20">
                    <p className="text-4xl mb-4">&#x1f604;</p>
                    <p className="font-bold">Speed</p>
                    <p>250</p>
                  </div>
                  <div className="items-center text-center w-20">
                    <p className="text-4xl mb-4">&#x1f60D;</p>
                    <p className="font-bold">Deadline</p>
                    <p>250</p>
                  </div>
                  <div className="items-center text-center w-20">
                    <p className="text-4xl mb-4">&#x1f603;</p>
                    <p className="font-bold">Communi</p>
                    <p>250</p>
                  </div>
                  <div className="items-center text-center w-20">
                    <p className="text-4xl mb-4">&#x1f970;</p>
                    <p className="font-bold">Design</p>
                    <p>250</p>
                  </div>
                  <div className="items-center text-center w-20">
                    <p className="text-4xl mb-4">&#x1f621;</p>
                    <p className="font-bold">Bad</p>
                    <p>20</p>
                  </div>
                </div>
              </div>

            </div>
          )}
          <div className="pl-16 mt-2">
            <p className="text-black">Tip:It is better to setup meeting schedule around 24 hours.</p>
            <p className="text-black">You can group chat with them at the same time to select prefer developer.</p>
            <p className="text-black">The average meeting time for 5 developers are <b><u>EST 2PM ~ EST 5PM</u></b></p>
          </div>
          <div className="px-16 mt-5 flex flex-col sm:flex-row">
            <div className="sm:w-1/5">
              <input
                value={date}
                onChange={(e: any) => setDate(e.target.value)}
                type="date"
                placeholder="Meeting Date"
                className="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2" />
            </div>
            <div className="sm:w-1/5">
              <input
                value={time}
                onChange={(e: any) => setTime(e.target.value)}
                type="time"
                placeholder="Meeting Time"
                className="block ml-3 w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2" />
            </div>
            <div className="sm:w-1/5">
            </div>
            <div className="sm:w-2/5 flex justify-end">
              <button onClick={handleSubmit} className="ml-3 px-4 py-2 tracking-wide text-white transition-colors duration-200 transform bg-green-400 rounded hover:bg-green-600 focus:outline-none">
                Schedule the meeting now
            </button>
            </div>
          </div>
        </div>
      </div>
    </>
  );
}

export default MatchFreelancersPage;
