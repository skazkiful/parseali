<?
Class Parseali{
	public function createcsv($create_data, $file = null, $col_delimiter = ';', $row_delimiter = "\r\n"){
		if( ! is_array($create_data) )
			return false;

		if( $file && ! is_dir( dirname($file) ) )
			return false;

		// строка, которая будет записана в csv файл
		$CSV_str = '';

		// перебираем все данные
		foreach( $create_data as $row ){
			$cols = array();

			foreach( $row as $col_val ){
				// строки должны быть в кавычках ""
				// кавычки " внутри строк нужно предварить такой же кавычкой "
				if( $col_val && preg_match('/[",;\r\n]/', $col_val) ){
					// поправим перенос строки
					if( $row_delimiter === "\r\n" ){
						$col_val = str_replace( "\r\n", '\n', $col_val );
						$col_val = str_replace( "\r", '', $col_val );
					}
					elseif( $row_delimiter === "\n" ){
						$col_val = str_replace( "\n", '\r', $col_val );
						$col_val = str_replace( "\r\r", '\r', $col_val );
					}

					$col_val = str_replace( '"', '""', $col_val ); // предваряем "
					$col_val = '"'. $col_val .'"'; // обрамляем в "
				}

				$cols[] = $col_val; // добавляем колонку в данные
			}

			$CSV_str .= implode( $col_delimiter, $cols ) . $row_delimiter; // добавляем строку в данные
		}

		$CSV_str = rtrim( $CSV_str, $row_delimiter );

		// задаем кодировку windows-1251 для строки
		if( $file ){
			$CSV_str = iconv( "UTF-8", "cp1251",  $CSV_str );

			// создаем csv файл и записываем в него строку
			$done = file_put_contents( $file, $CSV_str );

			return $done ? $CSV_str : false;
		}

		return $CSV_str;
	}
	public function getdata($offset){
		sleep(4);
		$handle=curl_init('https://gpsfront.aliexpress.com/queryGpsProductAjax.do?callback=jQuery18305002855215066833_1552312124554&widget_id=5547572&platform=pc&limit=12&offset='.$offset.'&phase=1&productIds2Top=&postback=&_=1552312124740');
		curl_setopt($handle, CURLOPT_VERBOSE, true);
		curl_setopt($handle, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.121 Safari/537.36');
		curl_setopt ($handle, CURLOPT_REFERER, 'https://flashdeals.aliexpress.com/en.htm');
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt( $handle, CURLOPT_COOKIEJAR, 'cookie.txt' );
		curl_setopt( $handle, CURLOPT_COOKIEFILE, 'cookie.txt' );
		$content = curl_exec($handle);

		return $content;
	}
	public function __construct(){
		$y = 0;
		while($y < 9){
			$query = $this->getdata($y);
			$query = substr($query,41);
			$query = substr($query,0,-1);
			$query = json_decode($query);
			if(empty($zagolovki)){
				foreach($query->gpsProductDetails[0] as $key => $value){
					if($key !== 'trace'){
						$zagolovki[] = $key;
					}
				}
				$create_data = array(
					$zagolovki
				);
			}
			$x = 0;
			while($x < 12){
			foreach($query->gpsProductDetails[$x] as $key => $value){
				if($key !== 'trace'){
					$newdata[] = $value;
				}
			}
			$create_data[] = $newdata;
			unset($newdata);
			$x++;
			}
			$y++;
		}
		$this->createcsv( $create_data, 'csv_file.csv' );
	}
}

$kek = new Parseali();
