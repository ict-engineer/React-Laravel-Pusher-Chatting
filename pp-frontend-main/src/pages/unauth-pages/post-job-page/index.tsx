import { useState } from "react";
import { useHistory } from "react-router-dom";
import { usePostJob } from "../../../store/hooks";
import { makeStyles } from '@material-ui/core/styles';
import TagsInput from "../../../components/Tag-Input"

const useStyles = makeStyles(theme => ({
  postInput: {
    padding: "15px",
    color: "#4a5568",
    borderRadius: "5px",
    border: "1px solid #c4c4c4",
    "&:hover": {
      border: "1px solid #212121"
    },
    "&:focus": {
      outline: 0,
      border: "2px solid #3f51b5"
    }
  },
  error: {
    border: "1px solid red",
  }
}));

const PostJobPage = (props: any) => {
  const history = useHistory();
  const { setJobInfo, getTopFreelancers } = usePostJob();
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');
  const [titleError, setTitleError] = useState('');
  const [selectedTags, setSelectedTags] = useState([]);
  const [tagError, setTagError] = useState("");
  const [descriptionError, setDescriptionError] = useState('');
  const [showModal, setShowModal] = useState(false);
  const classes = useStyles(props);

  const handleSubmit = () => {

    //Validate fields
    if (title === '') {
      setTitleError('Please input job title.');
      return;
    }
    else
      setTitleError('');

    if (description === '') {
      setDescriptionError('Please input job description.');
      return;
    }
    else
      setDescriptionError('');

    if (selectedTags.length === 0) {
      setTagError('Please add input tags.');
      return;
    }
    else
      setTagError("");

    setJobInfo({ job_title: title, job_desc: description, job_tags: selectedTags });
    getTopFreelancers();
    setShowModal(true);
    setTimeout(() => {
      history.push('/client/match-freelancers');
    }, 3000);
  }

  return (
    <div className="h-full overflow-auto">
      <div className="hero-area h-96"></div>
      <div className="m-auto w-full md:max-w-2xl p-8 -mt-72">
        <div className="flex justify-center">
          <a className="flex" href="/home">
            <img src="../logo.png" className="h-12 w-12" alt="Logo"></img>
            <div className="logo-title">PLUSPORTFOLIO</div>
          </a>
        </div>
      </div>
      <div className="m-auto w-full md:max-w-2xl p-8 mb-16 shadow bg-white rounded">
        <div className="text-2xl text-black wow fadeInUp mt-6 mb-2 font-bold" data-wow-delay="1s">Post a job</div>
        <div className="">
          <div className="mt-4">
            <input
              id="jobTitle"
              value={title}
              placeholder="Job Title"
              autoComplete="off"
              onChange={e => setTitle(e.target.value)}
              onKeyDown={e => setTitleError('')}
              className={classes.postInput + " block w-full " + (titleError ? classes.error : '')} type="text">
            </input>
            {titleError && <p className="text-left text-xs text-red-500 mt-1">{titleError}</p>}
          </div>

          <div className="mt-4">
            <textarea
              id="description"
              value={description}
              placeholder="Job Description"
              onChange={e => setDescription(e.target.value)}
              onKeyDown={e => setDescriptionError('')}
              className={classes.postInput + " block w-full h-40 resize-y " + (descriptionError ? classes.error : '')}>
            </textarea>
            {descriptionError && <p className="text-left text-xs text-red-500 mt-1">{descriptionError}</p>}
          </div>

          <div className="mt-4">
            {/* <input id="inputTags" placeholder="Input Tags" className="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring" type="text"></input> */}
            <TagsInput setSelectedTags={setSelectedTags} />
            {tagError && <p className="text-left text-xs text-red-500 mt-1">{tagError}</p>}
          </div>
          <div className="bg-gray-300 mt-4" style={{ height: "1px" }}></div>
          <div className="mt-4 flex justify-center">
            <button onClick={handleSubmit} className="secondary-btn next-btn">
              Next
            </button>
          </div>
        </div>
      </div>
      {showModal ? (
        <>
          <div className="overflow-x-hidden overflow-y-auto fixed inset-0 z-0 outline-none focus:outline-none bg-black opacity-70">
          </div>
          <div className="justify-center items-center flex overflow-x-hidden overflow-y-auto fixed inset-0 z-70 outline-none focus:outline-none">
            <div className="content-center py-16 relative w-96 h-96 z-50 text-indigo-400 rounded text-4xl bg-white">
              <div className="px-16">
                <img className="mx-auto" src="../assets/imgs/timing.png" alt=""></img>
                <p className="text-center text-gray-700 text-base mt-6">Please wait 5 mins while selecting the top 5 developers for your project.</p>
              </div>
            </div>
          </div>
        </>
      ) : null}
    </div >
  );
}

export default PostJobPage;
