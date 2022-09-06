import { useState } from "react";
import isEmail from '../../../utils/validation/isEmail';
import { usePostJob, useUser } from "../../../store/hooks";
import { useHistory } from "react-router-dom";


function SelectMeetingPage() {
  const history = useHistory();
  const [disabled, setDisabled] = useState(true);
  const [email, setEmail] = useState('');
  const [emailError, setEmailError] = useState('');
  const [code, setCode] = useState('');
  const [codeError, setCodeError] = useState('');
  const [confirmCode, setConfirmCode] = useState('');
  const { getConfirmCode, addJobInfo } = usePostJob();
  const { userSignup, user } = useUser();

  const handleGoMeeting = () => {
    history.push('/meetings');
  }

  const handlePostAnother = () => {
    history.push('/client/post-job');
  }

  const handleSendVerification = async (e: any) => {
    e.preventDefault();
    if (email === '') {
      setEmailError('Please input email.');
      return;
    }

    if (!isEmail(email)) {
      setEmailError('Please input correct email.');
      return;
    }

    setEmailError('');
    setDisabled(false);
    let result: any = await getConfirmCode(email);
    setConfirmCode(result);
  }

  const handleConfirm = async (e: any) => {
    e.preventDefault();
    if (code === '') {
      setCodeError('Please input confirm code.');
      return;
    }

    if (confirmCode === code) {
      const result = await userSignup({ email: email, user_role: 'client' });
      if (result)
        await addJobInfo();
    }
    else {
      setCodeError('Wrong confirm code.');
      return;
    }
  }

  return (
    <div className="h-full overflow-auto bg-gray-200">
      {user.token !== '' ? (<div className="mx-auto mt-10 p-6 overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800 w-96 text-center">
        <p className="text-black mb-6">Thank you to choose us</p>
        <p className="text-black mb-6">Meeting is scheduled. Your meeting will be started at EST 3PM</p>
        <div>
          <button onClick={handleGoMeeting} className="secondary-btn">
            Go to the Meeting
          </button>
        </div>
        <div className="mt-4">
          <button onClick={handlePostAnother} className="secondary-btn">
            Post Another Project
                    </button>
        </div>
      </div>)
        : (<div className="mx-auto mt-10 p-6 overflow-hidden bg-white rounded-lg shadow-lg dark:bg-gray-800 w-96">
          <div className="flex justify-center mt-2">
            <a className="flex" href="/home">
              <img src="../logo.png" className="h-12 w-12" alt="Logo"></img>
              <div className="logo-title">PLUSPORTFOLIO</div>
            </a>
          </div>
          <p className="text-center text-gray-700 dark:text-white mt-6">Please enter your email to setup the schedule</p>

          <div className="mt-10">
            <input id="emailAddress" autoComplete="off" value={email} onChange={e => setEmail(e.target.value)} className={"block w-full px-4 py-2 text-gray-700 bg-white border rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring " + (emailError ? 'border-red-500' : 'border-grey-300')} type="email"></input>
            {emailError && <p className="text-left text-xs text-red-500 mt-1">{emailError}</p>}
          </div>
          <div className="mt-2">
            <button onClick={handleSendVerification} className="w-full secondary-btn">
              Send Verification Code
                    </button>
          </div>
          <div className="mt-10">
            <input id="loggingPassword" placeholder="12345" value={code} onChange={e => setCode(e.target.value)} className={"disabled:opacity-50 block w-full px-4 py-2 text-gray-700 bg-white border rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring " + (codeError ? 'border-red-500' : 'border-grey-300')} type="text" disabled={disabled}></input>
            {codeError && <p className="text-left text-xs text-red-500 mt-1">{codeError}</p>}
          </div>

          <div className="mt-2">
            <button onClick={(e: any) => handleConfirm(e)} className={"w-full secondary-btn " + (disabled ? "disabled" : "")} disabled={disabled}>
              Confirm Code
                    </button>
          </div>
        </div>)}
    </div>
  );
}

export default SelectMeetingPage;
