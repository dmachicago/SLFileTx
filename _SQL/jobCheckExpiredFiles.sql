
use k3;

//drop event `jobCheckExpiredFiles`;

CREATE EVENT jobCheckExpiredFiles
ON SCHEDULE EVERY 1 day
DO
  delete FROM k3.MemberFiles where ExpireDate < NOW() ;
  
