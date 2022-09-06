import './../../styles/styles.css'

const PortfolioCard = (props: any) => {
  const editPortfolio = () => {
    props.editPortfolio(props.index);
  }
  const deletePortfolio = () => {
    props.deletePortfolio(props.portfolio.por_id);
  }
  const viewPortfolio = (e: any) => {
    e.preventDefault();
    props.viewPortfolio(props.index);
  }
  return (
    <>
      <div className="p-2">
        <div className={`group h-full border-2 border-gray-200 border-opacity-60 rounded-lg overflow-hidden ${props.editable ? 'hover:bg-black hover:opacity-80' : ''} cursor-pointer relative`}>
          {props.editable && (<><div onClick={editPortfolio} className='group-hover:opacity-100 opacity-0 edit-button absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 -ml-4'>
            <img src="/icons/edit-icon.svg" alt="edit"></img>
          </div>

            <div onClick={deletePortfolio} className='group-hover:opacity-100 opacity-0 remove-button absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 ml-4'>
              <img src="/icons/delete-icon.svg" alt="delete"></img>
            </div></>)}
          <div className="p-6 w-full h-full">
            {/* <img className="w-full h-full" src={props.portfolio.por_image} alt="" onClick={viewPortfolio}></img> */}
            {/* <h2 className="tracking-widest text-xs title-font font-medium text-gray-400 mb-1">CATEGORY</h2> */}
            <h1 className="title-font portfolio-card-content font-medium text-teal-600 mb-3">{props.portfolio.por_title}</h1>
            <p className="leading-relaxed mb-3 portfolio-card-content">{props.portfolio.por_desc}</p>
            <div className="flex items-center flex-wrap justify-end">
              <a onClick={viewPortfolio} className="text-indigo-500 inline-flex items-center md:mb-2 lg:mb-0 hover:underline" href="void(0)">View Details
                                <svg className="w-4 h-4 ml-2" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round">
                  <path d="M5 12h14"></path>
                  <path d="M12 5l7 7-7 7"></path>
                </svg>
              </a>
              {/* <span className="text-gray-400 mr-3 inline-flex items-center lg:ml-auto md:ml-0 ml-auto leading-none text-sm pr-3 py-1 border-r-2 border-gray-200">
                            <svg className="w-4 h-4 mr-1" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round" viewBox="0 0 24 24">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                            <circle cx="12" cy="12" r="3"></circle>
                            </svg>1.2K
                        </span>
                        <span className="text-gray-400 inline-flex items-center leading-none text-sm">
                            <svg className="w-4 h-4 mr-1" stroke="currentColor" strokeWidth="2" fill="none" strokeLinecap="round" strokeLinejoin="round" viewBox="0 0 24 24">
                            <path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"></path>
                            </svg>6
                        </span> */}
            </div>
          </div>
        </div>
      </div>
    </>
  );
};

export default PortfolioCard;