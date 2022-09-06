const StatusIcon = (props: any) => {
  switch (props.status) {
    case 'online':
      return <div className="block rounded-full w-4 h-4 bg-green-500 border-solid border-2 border-blue-200"></div>;
    case 'away':
      return <div className="block rounded-full w-4 h-4 bg-yellow-400 border-solid border-2 border-gray-200 "></div>;
    case 'do-not-disturb':
      return <div className="block rounded-full w-4 h-4 bg-red-500 border-solid border-2 border-gray-200  "></div>;
    case 'offline':
      return <div className="block rounded-full w-4 h-4 bg-gray-200 border-solid border-2 border-gray-300"></div>;
    default:
      return null;
  }
}

export default StatusIcon;
