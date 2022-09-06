import { IUser } from './user.models'

export interface IJob {
  job_id: string,
  job_title: string,
  job_desc: string,
  job_tags: string[],
  job_status: string,
  user_id: string,
}
export interface PostJob {
  jobInfo: IJob,
  topFreelancers: IUser[],
  error: string,
  confirmCode: string,
}
