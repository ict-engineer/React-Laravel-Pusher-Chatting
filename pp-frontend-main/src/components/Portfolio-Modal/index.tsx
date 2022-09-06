import { useState } from "react";
import './../../styles/styles.css'
import TagsInput from "../../components/Tag-Input"
import { useUser } from "../../store/hooks";
import { makeStyles } from '@material-ui/core/styles';

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

const PortfolioModal = (props: any) => {
  const { profile, addPortfolio, editPortfolio } = useUser();
  const [title, setTitle] = useState(props.portfolioModalType !== 'New' ? profile.portfolios[props.portfolioIndex].por_title : '');
  const [description, setDescription] = useState(props.portfolioModalType !== 'New' ? profile.portfolios[props.portfolioIndex].por_desc : '');
  const [titleError, setTitleError] = useState('');
  const [tagError, setTagError] = useState("");
  const [descriptionError, setDescriptionError] = useState('');
  const [selectedTags, setSelectedTags] = useState(props.portfolioModalType !== 'New' ? profile.portfolios[props.portfolioIndex].por_tags : []);
  const classes = useStyles(props);

  const handleSubmit = async () => {
    if (title === '') {
      setTitleError('Please input portfolio title.');
      return;
    }
    else {
      setTitleError('');
    }

    if (description === '') {
      setDescriptionError('Please input portfolio description.');
      return;
    }
    else {
      setDescriptionError('');
    }

    if (selectedTags.length === 0) {
      setTagError('Please add input tags.');
      return;
    }
    else {
      setTagError('');
    }

    if (props.portfolioModalType === 'New') {
      // add portfolio
      let portfolio = {
        por_title: title,
        por_desc: description,
        por_tags: selectedTags,
      }
      await addPortfolio(portfolio);

      props.closeModal()
    }
    else if (props.portfolioModalType === 'Edit') {
      // edit portfolio
      let portfolio = {
        por_id: profile.portfolios[props.portfolioIndex].por_id,
        por_title: title,
        por_desc: description,
        por_tags: selectedTags,
      }
      await editPortfolio(portfolio);
      props.closeModal();
    }
  }

  return (
    <>
      <div
        className="justify-center items-center flex w-full overflow-x-hidden overflow-y-auto fixed inset-0 z-50 outline-none focus:outline-none"
      >
        <div className="relative my-6 mx-auto" style={{ width: "500px" }}>
          {/*content*/}
          <div className="border-0 rounded-lg shadow-lg relative flex flex-col w-full bg-white outline-none focus:outline-none">
            {/*header*/}
            <div className="flex items-start justify-between p-5 border-b border-solid border-blueGray-200 rounded-t">
              <h3 className="text-2xl font-bold">
                {props.portfolioModalType} Portfolio
                  </h3>
              <button
                className="p-1 ml-auto bg-transparent border-0 text-black opacity-5 float-right text-3xl leading-none font-semibold outline-none focus:outline-none"
                onClick={() => props.closeModal()}
              >
                <span className="bg-transparent text-black opacity-5 h-6 w-6 text-2xl block outline-none focus:outline-none">
                  ×
                    </span>
              </button>
            </div>
            {/*body*/}
            {props.portfolioModalType !== 'View' ?
              <div className="relative p-6 flex-auto w-full">
                <div className="mt-4 w-full">
                  <input
                    value={title}
                    placeholder="Portfolio Title"
                    autoComplete="off"
                    onChange={e => setTitle(e.target.value)}
                    onKeyDown={e => setTitleError('')}
                    className={classes.postInput + " block w-full " + (titleError ? classes.error : '')} type="text">
                  </input>
                  {titleError && <p className="text-left text-xs text-red-500 mt-1">{titleError}</p>}
                </div>
                {/* <div className="mt-4 w-full">
                          <p className="block w-full px-4 py-2 text-gray-700 bg-white">{title}</p>
                        </div> */}

                <div className="mt-4 w-full">
                  <textarea
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
                  <TagsInput selectedTags={selectedTags} setSelectedTags={setSelectedTags} />
                  {tagError && <p className="text-left text-xs text-red-500 mt-1">{tagError}</p>}
                </div>
              </div>
              :
              <div className="relative p-6 flex-auto w-full">
                <div className="mt-4 w-full text-center">
                  <p className="title-font text-xl block w-full px-4 py-2 text-teal-600 bg-white">{title}</p>
                </div>

                <div className="mt-4 w-full">
                  {/* <textarea value={description} placeholder="Portfolio Description" onChange={e => setDescription(e.target.value)} className={"block w-full h-40 px-4 py-2 resize-y text-gray-700 bg-white border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-700 focus:border-transparent" + (descriptionError ? 'border-red-500' : 'border-gray-300')}></textarea>
                          {descriptionError && <p className="text-left text-xs text-red-500 mt-1">{descriptionError}</p>} */}
                  <p className="block w-full px-4 py-2 text-gray-700 bg-white" style={{ wordBreak: "break-word", wordWrap: "break-word", whiteSpace: 'pre-line' }}>{description}</p>
                </div>

                <div className="mt-4">
                  {/* <input id="inputTags" placeholder="Input Tags" className="block w-full px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-md dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 focus:border-blue-500 dark:focus:border-blue-500 focus:outline-none focus:ring" type="text"></input> */}
                  <TagsInput selectedTags={selectedTags} setSelectedTags={setSelectedTags} tagsInputType={'view'} />
                  {tagError && <p className="text-left text-xs text-red-500 mt-1">{tagError}</p>}
                </div>
              </div>
            }
            {/*footer*/}
            <div className="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
              <button
                className="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                type="button"
                onClick={() => props.closeModal()}
              >
                Close
                  </button>
              {props.portfolioModalType !== 'View' && <button onClick={handleSubmit} className="ml-3 px-4 py-2 secondary-btn">Save</button>}
            </div>
          </div>
        </div>
      </div>
      <div className="opacity-25 fixed inset-0 z-40 bg-black"></div>
    </>
  );
};

export default PortfolioModal;