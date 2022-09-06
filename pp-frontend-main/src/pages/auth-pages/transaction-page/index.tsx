import { useEffect } from 'react';
import Transaction from './transaction';
import TransactionsSidebar from './TransactionSideBar';
import Header from '../../../layouts/header'
import { useUser, useTransaction } from "../../../store/hooks";

const TransactionPage = (props: any) => {
  const { getTransactions } = useTransaction();
  const { setUnreadTransaction } = useUser();

  useEffect(() => {
    getTransactions();
    setUnreadTransaction(0);
  }, []);// eslint-disable-line react-hooks/exhaustive-deps

  return (
    <>
      <Header></Header>
      <div className="bg-gray-100 h-screen pt-20 pb-4 px-4">
        <div className="flex flex-wrap content-evenly bg-white shadow h-full">
          <div className='w-full h-full flex flex-col'>
            <div className="flex h-full">
              <div className="hidden sm:block w-full max-w-xs" style={{ backgroundColor: "rgb(237,242,247)" }}>
                <TransactionsSidebar />
              </div>

              <main className='z-10 flex flex-col w-full h-full'>
                <div className="relative h-full">
                  <Transaction className="flex flex-1 z-10 h-full" />
                </div>
                {/* </>
                )} */}
              </main>
            </div>

          </div>
        </div>
      </div>
    </>
  );
}

export default TransactionPage;