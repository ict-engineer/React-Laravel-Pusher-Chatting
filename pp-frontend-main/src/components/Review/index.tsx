import './../../styles/styles.css'
import Avatar from '@material-ui/core/Avatar';
import Rating from '@material-ui/lab/Rating';
import { useUser, useTransaction } from "../../store/hooks";

const Review = (props: any) => {
  const { user } = useUser();
  const { selectedTransactionId, transactions } = useTransaction();

  return (
    <>
      {user.user.user_id !== props.item.user_id ?
        (<div className="flex flex-col items-end relative w-full mb-4" style={{ paddingRight: "45px" }}>
          <Avatar
            style={{ position: "absolute", top: '0', right: '0' }}
            src={process.env.REACT_APP_BASE_URL + user.user.avatar}
          >
            {user.user.first_name === "" ? null : (user.user.first_name.charAt(0).toUpperCase() + user.user.last_name.charAt(0).toUpperCase())}
          </Avatar>
          <div className="w-1/2 border-solid border-gray-300 border-2 px-4 py-2">
            <p className="font-medium text-2xl text-center border-b-2 border-solid border-gray-200 mb-2">Review</p>
            <div className="flex justify-center">
              <Rating name="size-large" value={Number(props.item.review_rating)} size="large" readOnly />
            </div>
            <p className="mb-1 text-gray-400 whitespace-pre-line">{props.item.review_feedback}</p>
          </div>
        </div>) :
        (<div className="flex flex-col items-start relative w-full mb-4" style={{ paddingLeft: "45px" }}>
          <Avatar
            style={{ position: "absolute", top: '0', left: '0' }}
            src={process.env.REACT_APP_BASE_URL + transactions[selectedTransactionId].avatar}
          >
            {transactions[selectedTransactionId].first_name === "" ? null : (transactions[selectedTransactionId].first_name.charAt(0).toUpperCase() + transactions[selectedTransactionId].last_name.charAt(0).toUpperCase())}
          </Avatar>
          <div className="w-1/2 border-solid border-gray-300 border-2 px-4 py-2">
            <p className="font-medium text-2xl text-center border-b-2 border-solid border-gray-200 mb-2">Review</p>
            <div className="flex justify-center">
              <Rating name="size-large" value={Number(props.item.review_rating)} size="large" readOnly />
            </div>
            <p className="mb-1 text-gray-400 whitespace-pre-line">{props.item.review_feedback}</p>
          </div>
        </div>
        )
      }
    </>
  );
};

export default Review;