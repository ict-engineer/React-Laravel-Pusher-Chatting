import './../../styles/styles.css'
import moment from 'moment-timezone';
import Rating from '@material-ui/lab/Rating';

const ReviewCard = (props: any) => {
  return (
    <>
      <div className="border-b-2 border-gray-200 mr-6 pb-3 px-4 mb-5">
        <div className="flex">
          <div className="w-60">
            <p className="text-blue-500">{moment(new Date(props.review.created_at)).format('h:mm A')}</p>
          </div>
          <div className="flex justify-around w-full">
            {/* {props.review.rating.map((rating: any, i: number) => {
              return <div key={i} className="flex items-center">
                {(rating.marks === 1) ? (<p className="text-3xl">&#x1f60D;</p>) :
                  ((rating.marks === 2) ? (<p className="text-3xl">&#x1f604;</p>) :
                    ((rating.marks === 3) ? (<p className="text-3xl">&#x1f917;</p>) :
                      (<p className="text-3xl">&#x1f621;</p>)))}
                <p className="text-xs">{rating.item}</p>
              </div>
            })
            } */}
            <Rating name="size-large" value={Number(props.review.review_rating)} size="large" readOnly />
          </div>
        </div>
        <div>
          <p className="text-sm mt-2 whitespace-pre-line">{props.review.review_feedback}</p>
        </div>
        <div>
          <p className="text-sm mt-2 flex justify-end mr-6">${props.review.rate} * {props.review.track_hours} hrs = ${props.review.rate * props.review.track_hours}</p>
        </div>

      </div>
    </>
  );
};

export default ReviewCard;