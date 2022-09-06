import { useState } from "react";
import { useTransaction, useUser } from "../../store/hooks";
import Rating from '@material-ui/lab/Rating';

const ReviewModal = (props: any) => {
  const [description, setDescription] = useState('');
  const [rating, setRating] = useState(0);
  const [descriptionError, setDescriptionError] = useState('');
  const { selectedTransactionId, transactions, sendMessageTr } = useTransaction();
  const { user } = useUser();

  const handleSubmit = async () => {
    if (description === '') {
      setDescriptionError('Please input track description a week.');
      return;
    }
    else {
      setDescriptionError('');
    }

    sendMessageTr({
      chat_type: "review",
      review_rating: rating,
      review_feedback: description,
      contract_id: transactions[selectedTransactionId].contract_id,
    });

    props.setShowModal(false);
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
                Review to end the contract
                  </h3>
              <button
                className="p-1 ml-auto bg-transparent border-0 text-black float-right text-3xl leading-none font-semibold outline-none focus:outline-none"
                onClick={() => props.setShowModal(false)}
              >
                <span className="bg-transparent text-black h-6 w-6 text-2xl block outline-none focus:outline-none">
                  ×
                    </span>
              </button>
            </div>


            {/*body*/}
            <div className="relative px-6 py-2 flex-auto w-full">

              <div className="mt-4 w-full">
                <p className="text-green-400">Review</p>
              </div>
              <div className="mt-1 w-full">
                <Rating name="size-large" value={rating} onChange={(e: any) => setRating(e.target.value)} size="large" />
              </div>
              <div className="mt-4 w-full">
                {user.user.user_role === "client" ?
                  <p className="text-xl text-green-400">Please leave a review to the freelancer</p> :
                  <p className="text-xl text-green-400">Please leave a review to the client</p>
                }
              </div>

              <div className="mt-1 w-full">
                <textarea
                  className={"resize-y border w-full px-4 py-2 focus:outline-none rounded-md overflow-hidden h-40 bg-gray-100 " + (descriptionError !== '' ? 'border-red-500' : '')}
                  autoComplete="off"
                  placeholder="Please input brief public review to the client"
                  onChange={e => setDescription(e.target.value)}
                  onKeyDown={e => setDescriptionError('')}
                ></textarea>
                {descriptionError && <p className="text-left text-xs text-red-500 mt-1">{descriptionError}</p>}
              </div>
            </div>
            {/*footer*/}
            <div className="flex items-center justify-end p-6 border-t border-solid border-blueGray-200 rounded-b">
              <button
                className="text-red-500 background-transparent font-bold uppercase px-6 py-2 text-sm outline-none focus:outline-none mr-1 mb-1 ease-linear transition-all duration-150"
                type="button"
                onClick={() => props.setShowModal(false)}
              >
                Close
                  </button>
              <button onClick={handleSubmit} className="ml-3 px-4 py-2 secondary-btn">Submit</button>
            </div>
          </div>
        </div>
      </div>
      <div className="opacity-25 fixed inset-0 z-40 bg-black"></div>
    </>
  );
};

export default ReviewModal;
