import { useState, useRef, useEffect } from "react";
import { useUser } from "../../../store/hooks";
import { format } from "date-fns";
import moment from 'moment-timezone';
import Header from '../../../layouts/header'
import PortfolioCard from './../../../components/Portfolio-Card';
import PortfolioModal from './../../../components/Portfolio-Modal';
import ReviewCard from './../../../components/Review-Card';
import ReactPaginate from 'react-paginate';
import TimezoneSelect from 'react-timezone-select'

function ProfilePage(props: any) {
  const { user, profile, deletePortfolio, updateUserData, setProfileData, getProfileData } = useUser();
  const [isProfileEditable, setIsProfileEditable] = useState(false);
  const [selectedTimezone, setSelectedTimezone] = useState('')
  const [englishLevel, setEnglishLevel] = useState('');
  const [isEditProfile, setIsEditProfile] = useState(false);
  const [isShowPortfolioModal, setIsShowPortfolioModal] = useState(false);
  const [portfolioModalType, setPortfolioModalType] = useState('');
  const [title, setTitle] = useState('');
  const [uploadImage, setUploadImage] = useState('');
  const [portfolioHandleIndex, setPortfolioHandleIndex] = useState(0);
  const [firstName, setFirstName] = useState('');
  const [lastName, setLastName] = useState('');
  const [description, setDescription] = useState('');
  const [avatarImage, setAvatarImage] = useState('');
  const [currentPortfolioPage, setCurrentPortfolioPage] = useState(0);
  const avartarInputRef = useRef<HTMLInputElement>(null);
  const portfolioCountPerPage = 3;

  useEffect(() => {
    if (profile.avatar && profile.avatar !== 'undefined') {
      setAvatarImage(process.env.REACT_APP_BASE_URL + profile.avatar);
      setUploadImage(process.env.REACT_APP_BASE_URL + profile.avatar);
    }
    else
      setAvatarImage('');

    if (profile.timezone)
      setSelectedTimezone(profile.timezone);

    if (profile.description)
      setDescription(profile.description);

    if (profile.english_level)
      setEnglishLevel(profile.english_level);
  }, [profile.avatar, profile.timezone, profile.description, profile.english_level]);

  useEffect(() => {
    if (props.match.params.id === "me") {
      setIsProfileEditable(true);
      setProfileData(user.user);
    }
    else {
      getProfileData(props.match.params.id);
    }
  }, [user.user, props.match.params.id]);// eslint-disable-line react-hooks/exhaustive-deps

  const editProfile = () => {
    setIsEditProfile(true);
    setFirstName(profile.first_name);
    setLastName(profile.last_name);
  }

  const saveUpdates = async () => {
    setIsEditProfile(false);
    if (uploadImage === (process.env.REACT_APP_BASE_URL + profile.avatar)) {
      await updateUserData({
        full_name: firstName + ' ' + lastName,
        first_name: firstName,
        last_name: lastName,
        description: description,
        timezone: selectedTimezone,
        english_level: englishLevel,
        title: title,
      });
    }
    else {
      setAvatarImage(process.env.REACT_APP_BASE_URL + profile.avatar);
      await updateUserData({
        full_name: firstName + ' ' + lastName,
        first_name: firstName,
        last_name: lastName,
        description: description,
        avatar: uploadImage,
        timezone: selectedTimezone,
        english_level: englishLevel,
        title: title,
      });

    }
  }
  const cancelEdit = () => {
    setAvatarImage((process.env.REACT_APP_BASE_URL + profile.avatar) || "");
    setIsEditProfile(false);
  }
  const handlePortfolioPageClick = ({ selected }: any) => {
    setCurrentPortfolioPage(selected);
  }
  const newPortfolio = () => {
    setIsShowPortfolioModal(true);
    setPortfolioModalType('New');
  }
  const editPortfolio = (index: number) => {
    setIsShowPortfolioModal(true);
    setPortfolioModalType('Edit');
    setPortfolioHandleIndex(index);
  }
  const viewPortfolio = (index: number) => {
    setIsShowPortfolioModal(true);
    setPortfolioModalType('View');
    setPortfolioHandleIndex(index);
  }
  const closePortfolioModal = () => {
    setIsShowPortfolioModal(false);
  }
  const handleAvartarChange = (event: any) => {
    let imageURL = URL.createObjectURL(event.target.files[0]);
    setAvatarImage(imageURL);

    let file = event.target.files[0];
    let reader = new FileReader();
    reader.onload = (e: any) => {
      setUploadImage(e.target.result);
    };
    reader.readAsDataURL(file);
  }
  const onClickAvartarChange = () => {
    avartarInputRef.current?.click();
  }

  return (
    <>
      <Header></Header>

      <div className="bg-gray-100 h-full pt-16 overflow-auto pb-4">
        <div className="hero-area h-96"></div>
        <div className="md:flex container -mt-80">
          <div className="md:w-3/12 md:mx-2">
            <div className="bg-white p-3 border-t-4 border-green-400">

              <div className="image overflow-hidden relative">
                {avatarImage === '' || avatarImage === undefined ?
                  <div className="image_box">
                    <img className="w-full mx-auto h-full absolute left-0 top-0"
                      src="/assets/imgs/avatar.png"
                      alt="Avatar"></img>
                  </div> :
                  <div className="image_box">
                    <img className="w-full mx-auto h-full absolute left-0 top-0"
                      src={avatarImage}
                      alt="Avatar"></img>
                  </div>
                }
                <div onClick={onClickAvartarChange} className={`${isEditProfile ? 'visible' : 'invisible'}  edit-button absolute top-1 right-0 z-10 cursor-pointer`}>
                  <img src="/icons/edit-icon.svg" alt=""></img>
                </div>
                <form encType="multipart/form-data">
                  <input type="file" ref={avartarInputRef} className="invisible" onChange={handleAvartarChange} />
                </form>
              </div>
              {profile.user_role === "freelancer" ?
                (
                  isEditProfile ?
                    (<>
                      <div className=" pt-0 flex">
                        < input type="text" placeholder="First Name" value={firstName} onChange={e => setFirstName(e.target.value)} className="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2" />
                        < input type="text" placeholder="Last Name" value={lastName} onChange={e => setLastName(e.target.value)} className="ml-1 block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring my-2" />
                      </div>

                      <select className="w-full mb-2 py-2 text-gray-700 border border-solid border-gray-300 px-3 rounded cursor-pointer focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring" value={englishLevel} onChange={(e: any) => setEnglishLevel(e.target.value)}>
                        <option value="" disabled>Select English Level</option>
                        <option value="Fluent">Fluent</option>
                        <option value="Conversational">Conversational</option>
                      </select>

                      <TimezoneSelect
                        value={selectedTimezone}
                      />
                      <textarea placeholder="Description" value={description} onChange={e => setDescription(e.target.value)} className="block w-full h-60 px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring mt-2" />
                    </>)
                    :
                    (<>
                      <h1 className="text-gray-900 font-bold text-xl leading-8 my-1">{profile.full_name}</h1>
                      <h3 className="text-gray-600 font-lg text-semibold leading-6 my-1">English Level: {profile.english_level}</h3>
                      <p className="text-base mb-3">TimeZone: {moment().tz(profile.timezone).zoneAbbr()}</p>
                      <p className="text-base mb-1">Description</p>
                      <div className="leading-relaxed mb-3 text-gray-400" style={{ wordBreak: "break-word", wordWrap: "break-word", whiteSpace: 'pre-line', minHeight: "15rem" }}>
                        {profile.description}
                      </div>
                    </>)
                )
                : (<>
                  {isEditProfile ?
                    <input placeholder="Title" value={title} onChange={e => setTitle(e.target.value)} className="block w-full px-4 py-2 text-gray-600 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring mt-2" />
                    :
                    <h1 className="text-gray-900 font-bold text-xl leading-8 my-1">Title:   {profile.full_name}</h1>
                  }</>)
              }

              <ul
                className="bg-gray-100 text-gray-600 hover:text-gray-700 hover:shadow py-2 px-3 mt-3 divide-y rounded shadow-sm">
                <li className="flex items-center py-3">
                  <span>Status</span>
                  <span className="ml-auto"><span
                    className="bg-green-500 py-1 px-2 rounded text-white text-sm">Active</span></span>
                </li>
                <li className="flex items-center py-3">
                  <span>Member since</span>
                  <span className="ml-auto">{format(new Date(profile.created_at), "MMMM do, yyyy")}</span>
                </li>
              </ul>

            </div>

          </div>
          <div className="md:w-9/12 mx-2 mb-4">
            <div className="bg-white p-3 shadow-sm rounded-sm mb-4">
              <div className="flex justify-between font-semibold text-gray-900 leading-8 mb-3">
                <span className="text-green-500 tracking-wide">Overall Status</span>

                {isProfileEditable && isEditProfile === false &&
                  <div className="flex justify-end mb-2">
                    <button onClick={editProfile} className="secondary-btn">Edit Profile</button></div>}
                {isEditProfile &&
                  <div className="flex justify-end mb-2">
                    <button onClick={saveUpdates} className="bg-green-500 text-white hover:bg-green-300 py-1 px-4 rounded mr-2">Save</button>
                    <button onClick={cancelEdit} className="bg-white border-2 border-solid border-red-500 hover:bg-red-100 py-1 px-4 rounded">Cancel</button>
                  </div>
                }
              </div>
              <div className="flex justify-between px-8">
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

            {profile.user_role === "freelancer" ?
              (<div className="bg-white p-3 shadow-sm rounded-sm my-4" style={{ minHeight: "15rem" }}>
                <div className="flex items-center space-x-2 font-semibold text-gray-900 leading-8 mb-3">
                  <span className="text-green-500 tracking-wide">Portfolios</span>
                  {isProfileEditable &&
                    <div className="flex justify-center items-center ml-8 cursor-pointer" onClick={newPortfolio}>
                      <img src="/icons/button-plus-icon.svg" alt=""></img>
                    </div>
                  }
                </div>
                {
                  profile.portfolios && profile.portfolios.length !== 0 && <>
                    <div className="grid grid-cols-3">
                      {profile.portfolios.map((portfolio: any, index: number) => {
                        if ((index >= (currentPortfolioPage * portfolioCountPerPage)) && index < ((currentPortfolioPage + 1) * portfolioCountPerPage))
                          return <PortfolioCard
                            editable={isProfileEditable}
                            portfolio={portfolio}
                            index={index}
                            key={index}
                            editPortfolio={editPortfolio}
                            deletePortfolio={deletePortfolio}
                            viewPortfolio={viewPortfolio}
                          />
                        return null;
                      })}

                    </div>
                    <div className="flex justify-end mr-10 mt-3">
                      <ReactPaginate
                        pageCount={(profile.portfolios.length / 3)}
                        pageRangeDisplayed={2}
                        marginPagesDisplayed={3}
                        containerClassName={"pagination"}
                        disabledClassName={"pagination__link--disabled"}
                        activeClassName={"pagination__link--active"}
                        onPageChange={handlePortfolioPageClick}
                        forcePage={currentPortfolioPage}
                      ></ReactPaginate>
                    </div>
                  </>
                }
              </div>) : (null)
            }
            <div className="bg-white p-3 shadow-sm rounded-sm my-4" style={{ minHeight: "15rem" }}>
              <div className="flex items-center space-x-2 font-semibold text-gray-900 leading-8 mb-3">
                <span className="text-green-500 tracking-wide">Reviews({profile.reviews ? profile.reviews.length.toString() : 0})</span>
              </div>
              <div className="pl-2">
                {profile.reviews && profile.reviews.length !== 0 && profile.reviews.map((review: any, index: number) => {
                  return <ReviewCard key={index} review={review} />
                })}
              </div>
            </div>
          </div>
        </div>
      </div >
      {
        isShowPortfolioModal &&
        <PortfolioModal
          portfolioModalType={portfolioModalType}
          closeModal={closePortfolioModal}
          portfolioIndex={portfolioHandleIndex}
        ></PortfolioModal>
      }
    </>
  );
}

export default ProfilePage;
