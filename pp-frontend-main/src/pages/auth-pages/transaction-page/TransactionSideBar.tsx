import { useState } from 'react';
import { useTransaction } from "../../../store/hooks";
import TransactionListItem from './TransactionListItem';

const TransactionSidebar = (props: any) => {
  const { transactions } = useTransaction();
  const [searchText, setSearchText] = useState('');

  return (
    <div className="flex flex-col flex-auto h-full">
      <div className="w-full">
        <div className="px-4">
          <div className="relative my-2 flex">
            <span className="absolute inset-y-0 pl-4 flex items-center">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" className="h-4 w-4 text-gray-400"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input
              value={searchText}
              onChange={(e: any) => setSearchText(e.target.value)}
              type="text"
              placeholder="Search"
              className="py-3 pl-10 rounded-full text-xs w-full placeholder-gray-400 outline-none focus:outline-none"></input>
          </div>
          {transactions.map((contact: any, i: any) => (
            contact.full_name.toLowerCase().includes(searchText.toLowerCase()) ?
              <TransactionListItem
                key={i}
                contact={contact}
                index={i}
              /> : null
          ))}
        </div>
      </div>
    </div>
  );
}

export default TransactionSidebar;
