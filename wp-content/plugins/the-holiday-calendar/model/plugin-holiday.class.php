<?php
	class thc_plugin_holiday
	{
        public $date;
        public $title;
        public $formattedDate;
        public $url;
        public $teaser;
			
		public function __construct( $date, $title, $formattedDate, $url, $teaser )
		{
			$this->{'date'} = $date;
			$this->title = $title;
			$this->formattedDate = $formattedDate;
			$this->url = $url;
			$this->teaser = $teaser;
		}

		public static function create_from_object( $object )
		{
			return new self( $object->{'Date'}, $object->Title, $object->FormattedDate, $object->Url, $object->Teaser );
		}
    }	
?>