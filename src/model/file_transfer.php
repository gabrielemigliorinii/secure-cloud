<?php

    require_once __DIR__ . '/model.php';
    require_once __DIR__ . '/../../utils/mydatetime.php';

    class FileTransferModel extends Model
    {
        private $transfer_date;
        private string $transfer_type;
        private string $id_file;

        public const TTYPE_UPLOAD = "upload";
        public const TTYPE_DOWNLOAD = "download";

        public function __construct($transfer_date = null, string $transfer_type = null, string $id_file = null) 
        {
            if ($transfer_date === null)
                $this->setTransferDateNow();
            else
                $this->setTransferDate($transfer_date);

            $this->setTransferType($transfer_type ? $transfer_type : parent::DEFAULT_STR);
            $this->setFileID($id_file ? $id_file : parent::DEFAULT_STR);
        }

        public function setTransferDate($transfer_date) : void
        {
            $this->transfer_date = $transfer_date;
        }

        public function setTransferDateNow()
        {
            $transfer_date = MyDatetime::now();
            $this->setTransferDate($transfer_date);
        }

        public function getTransferDate()
        {
            return $this->transfer_date;
        }

        public function setTransferType(string $transfer_type) : void
        {
            $transfer_type = strtolower($transfer_type);
            $this->transfer_type = in_array($transfer_type, [self::TTYPE_UPLOAD, self::TTYPE_DOWNLOAD]) ? $transfer_type : self::DEFAULT_STR;
        }

        public function getTransferType() : string
        {
            return $this->transfer_type;
        }

        public function setFileID(string $id_file) : void
        {
            $this->id_file = $id_file;
        }

        public function getFileID() : string
        {
            return $this->id_file;
        }

        public function toAssocArray($transfer_date = false, $transfer_type = false, $id_file = false)
        {
            $params = array();

            if ($transfer_date)
                $params['transfer_date'] =  $this->getTransferDate();

            if ($transfer_type)
                $params['transfer_type'] =  $this->getTransferType();

            if ($id_file)
                $params['id_file'] =  $this->getFileID();

            return $params;
        }

        public function ins()
        {
            $qry = "INSERT INTO `file_transfers` (`transfer_date`, `transfer_type`, `id_file`) VALUES (:transfer_date, :transfer_type, :id_file)";
        
            MyPDO::connect($_ENV['EDIT_USERNAME'], $_ENV['EDIT_PASSWORD'], $_ENV['DB_HOST'], $_ENV['DB_NAME']);

            return MyPDO::qryExec
            (
                $qry, 
                $this->toAssocArray(transfer_date:true, transfer_type:true, id_file:true)
            );
        }
    }

?>