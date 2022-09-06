import { useState, useEffect } from "react";
import { useUser } from "../../../store/hooks";
import Header from '../../../layouts/header'
import isEmail from '../../../utils/validation/isEmail';

function SettingPage(props: any) {
  const { user, updateUserData } = useUser();
  const [selected, setSelected] = useState(1);
  const [payment, setPayment] = useState('');
  const [newPassword, setNewPassword] = useState('');
  const [oldPassword, setOldPassword] = useState('');
  const [confirmPassword, setConfirmPassword] = useState('');
  const [paymentError, setPaymentError] = useState('');
  const [newPasswordError, setNewPasswordError] = useState('');
  const [oldPasswordError, setOldPasswordError] = useState('');
  const [confirmPasswordError, setConfirmPasswordError] = useState('');

  useEffect(() => {
    setPayment(user.user.payment_email);
  }, [user.user.payment_email]);

  const onSavepaymentInfo = async (e: any) => {
    e.preventDefault();
    if (payment === '') {
      setPaymentError('Please input payment info.');
      return;
    }

    if (!isEmail(payment)) {
      setPaymentError('Please input valid payment info.');
      return;
    }
    await updateUserData({ payment_email: payment });
  }

  const onSaveSecurityInfo = async (e: any) => {
    e.preventDefault();
    if (oldPassword === '') {
      setOldPasswordError('Please input old password.');
      return;
    }

    if (newPassword === '') {
      setNewPasswordError('Please input new password.');
      return;
    }

    if (newPassword.length < 6) {
      setNewPasswordError('New password must be at least 6 letters.');
      return;
    }

    if (confirmPassword === '') {
      setConfirmPasswordError('Please input confirm password.');
      return;
    }

    if (newPassword !== confirmPassword) {
      setConfirmPasswordError('Confirm Password must be same as new password.');
      return;
    }

    await updateUserData({ oldPassword: oldPassword, password: newPassword });
  }
  return (
    <div className="h-full bg-gray-100">
      <Header></Header>
      <div className="hero-area h-80">
      </div>
      <div className="md:flex -mt-48 w-full mx-auto max-w-screen-lg">
        <div className="md:w-3/12 md:mx-1">
          <div className="shadow-lg bg-white rounded-lg w-full">
            <div className="bg-white p-3 border-t-4 border-green-400">
              <div className="image overflow-hidden relative max-w-xs m-auto">
                {user.user.avatar === '' || user.user.avatar === undefined ?
                  <img className="w-full mx-auto"
                    src="/assets/imgs/avatar.png"
                    alt=""></img> :
                  <img className="h-auto w-full mx-auto"
                    src={user.user.avatar}
                    alt=""></img>
                }
              </div>
            </div>
            <div className="flex flex-col items-center px-2">
              <div
                onClick={(e: any) => setSelected(1)}
                className={"w-full text-center rounded-full cursor-pointer font-medium text-lg hover:outline-none mb-2 py-1 px-4 " + ((selected === 1) ? 'bg-blue-300 text-black' : 'text-gray-600')}>payment</div>
              <div
                onClick={(e: any) => setSelected(2)}
                className={"w-full text-center rounded-full cursor-pointer font-medium text-lg hover:outline-none mb-2 py-1 px-4 " + ((selected === 2) ? 'bg-blue-300 text-black' : 'text-gray-600')}>Security</div>
            </div>
          </div>
        </div>
        <div className="md:w-9/12 mx-1 mb-4">
          <div className="shadow-lg bg-white rounded-lg w-full p-8">
            {
              selected === 1 ? (
                <>
                  <p className="text-3xl text-black font-bold">payment</p>
                  <div className="flex justify-center">
                    <div className="w-80">
                      <input
                        type="text"
                        placeholder="solution-paypal.gmail.com"
                        value={payment}
                        onChange={e => { setPaymentError(''); setPayment(e.target.value); }}
                        className={"mt-8 block w-80 px-4 py-2 text-gray-700 bg-white border rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2 " + (paymentError ? 'border-red-600' : 'border-gray-300')}
                      />
                      {paymentError && <p className="text-left text-xs text-red-500 mt-1">{paymentError}</p>}
                      <div className="flex justify-end mt-4">
                        <button onClick={(e: any) => onSavepaymentInfo(e)} className="secondary-btn">Save Changes</button>
                      </div>
                    </div>
                  </div>
                </>
              ) :
                (
                  <>
                    <p className="text-3xl text-black font-bold">Change Password</p>
                    <div className="flex justify-center">
                      <div className="w-80 mt-4">
                        <input
                          type="password"
                          placeholder="Old Password"
                          value={oldPassword}
                          onChange={e => { setOldPasswordError(''); setOldPassword(e.target.value) }}
                          className={"mt-4 block w-80 px-4 py-2 text-gray-700 bg-white border rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2 " + (oldPasswordError ? 'border-red-600' : 'border-gray-300')}
                        />
                        {oldPasswordError && <p className="text-left text-xs text-red-500 mt-1">{oldPasswordError}</p>}
                        <input
                          type="password"
                          placeholder="New Password"
                          value={newPassword}
                          onChange={e => { setNewPasswordError(''); setNewPassword(e.target.value) }}
                          className={"mt-4 block w-80 px-4 py-2 text-gray-700 bg-white border rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2 " + (newPasswordError ? 'border-red-600' : 'border-gray-300')}
                        />
                        {newPasswordError && <p className="text-left text-xs text-red-500 mt-1">{newPasswordError}</p>}
                        <input
                          type="password"
                          placeholder="Confirm Password"
                          value={confirmPassword}
                          onChange={e => { setConfirmPasswordError(''); setConfirmPassword(e.target.value) }}
                          className={"mt-4 block w-80 px-4 py-2 text-gray-700 bg-white border rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2 " + (confirmPasswordError ? 'border-red-600' : 'border-gray-300')}
                        />
                        {confirmPasswordError && <p className="text-left text-xs text-red-500 mt-1">{confirmPasswordError}</p>}
                        <div className="flex justify-end mt-4">
                          <button onClick={(e: any) => onSaveSecurityInfo(e)} className="secondary-btn">Save Changes</button>
                        </div>
                      </div>
                    </div>
                  </>
                )
            }
          </div>
        </div>

      </div>
    </div >
  );
}

export default SettingPage;
