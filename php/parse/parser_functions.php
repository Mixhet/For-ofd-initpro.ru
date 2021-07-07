<?php
libxml_use_internal_errors(true);

// Заполнение таблицы данными
function fillingDatabase()
{
	$data_parse = parsing();

	foreach ($data_parse as $key)
	{
		insertProcedures($key['procedure_number'], $key['oos_procedure_number'], $key['link_procedure'], $key['email']);

		$procedure_id = getId($key['procedure_number']);

		foreach ($key['attachment'] as $key_attachment)
		{
			insertAttachment($key_attachment['title'], $key_attachment['link_file'], $procedure_id);
		}
	}
}

// Чтение из таблиц
function readingDatabase()
{
	$data_procedure = selectProcedures();

	$common_data = [];
	foreach ($data_procedure as $key)
	{
		$id_procedure = $key['id'];
		$procedure_number = $key['procedure_number'];
		$oos_procedure_number = $key['oos_procedure_number'];
		$link_procedure = $key['link_procedure'];
		$email = $key['email'];

		$data_attachment = selectAttachment($id_procedure);	

		$attachment_array = [];
		foreach($data_attachment as $key)
		{
			$title = $key['title'];
			$link = $key['link'];

			$attachment_array[] = array(
				'title' => $title,
				'link_to_file' => $link
			);
		}

		$common_data[] = array(
			'id_procedure' => $id_procedure,
			'procedure_number' => $procedure_number,
			'oos_procedure_number' => $oos_procedure_number,
			'link_procedure' => $link_procedure,
			'email' => $email,
			'attachment' => $attachment_array
		);
	}
	// print_r($common_data);
	return $common_data;
	
}

//Парсит сайт https://etp.eltox.ru/
function parsing(){
	// Ссылка с установленным фильтром: Тип процедуры – Запрос цен (котировок)
	$url = 'https://etp.eltox.ru/registry/procedure?type=1';
	$refererUrl = 'https://etp.eltox.ru';

	$data = curlGetContents($url, $refererUrl);

	if ($data['code'] == 200)
	{
		// Карточки
		$cards = getCards($data);

		$page_data = [];

		foreach ($cards as $key => $item) 
		{
			preg_match('/(?<=№\ ).*\d/', $item, $procedure_number);
			preg_match('/(?<=№\ ООС:\ ).*\d/', $item, $oos_procedure_number);
			preg_match('/(?<=href=").*(?=">)/', $item, $link_procedure);

			$page_data[] = array(
				'procedure_number'  => $procedure_number[0],
				'oos_procedure_number' => $oos_procedure_number[0],
				'link_procedure' => 'https://etp.eltox.ru' . $link_procedure[0]
			);
		}
	}

	$common_data = [];
	foreach ($page_data as $key => $item)
	{
		$link_procedure = $item['link_procedure'];

		$data = curlGetContents($link_procedure, $refererUrl);

		$email = getEmail($data);

		preg_match_all('/\{".*?true}/', $data['data'], $data_script);

		$attachment_array = [];
		foreach ($data_script[0] as $key => $json_string)
		{
			$json_array = json_decode($json_string, true);

			$attachment_array[] = array(
				'title' => $json_array['alias'],
				'link_file' => 'https://storage.eltox.ru/'. $json_array['path'] . '/' . $json_array['name']
			);
		}
		// urlencode($json_array['name']

		$common_data[] = array(
			'procedure_number' => $item['procedure_number'],
			'oos_procedure_number' => $item['oos_procedure_number'],
			'link_procedure'=> $link_procedure,
			'email' => $email,
			'attachment' => $attachment_array
		);
	}

	// print_r($common_data);
	return $common_data;

}

//Прочесть содержимое файла в строку при помощи cUrl
function curlGetContents($pageUrl, $baseUrl, $pauseTime = 4, $retry = true) {
	$errors = [];

	$ch = curl_init();

	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_USERAGENT, getRandomUserAgent());

	curl_setopt($ch, CURLOPT_URL, $pageUrl);
	curl_setopt($ch, CURLOPT_REFERER, $baseUrl);

	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

	$response['data'] = curl_exec($ch);

	$ci = curl_getinfo($ch);

	if($ci['http_code'] != 200 && $ci['http_code'] != 404) {
		$errors[] = [1, $pageUrl, $ci['http_code']];

		if($retry) {
			sleep($pauseTime);
			$response['data'] = curl_exec($ch);
			$ci = curl_getinfo($ch);

			if($ci['http_code'] != 200 && $ci['http_code'] != 404){
				$errors[] = [2, $pageUrl, $ci['http_code']];
			}
		}
	}

	$response['code'] = $ci['http_code'];
	$response['errors'] = $errors;

	curl_close($ch);

	return $response;
}

 //Получить случайный заголовок браузера
function getRandomUserAgent()
{
	$userAgents = [
		'Mozilla/5.0 (Windows NT 6.3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36',
		'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.1',
		'Opera/9.80 (X11; Linux i686; Ubuntu/14.10) Presto/2.12.388 Version/12.16',
		'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/7046A194A',
		'Mozilla/5.0 (compatible; YandexBot/3.0; +http://yandex.com/bots)',
		'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
		'Mozilla/5.0 (Windows; U; Win 9x 4.90; SG; rv:1.9.2.4) Gecko/20101104 Netscape/9.1.0285',
		'Lynx/2.8.8dev.3 libwww-FM/2.14 SSL-MM/1.4.1',
		'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; AS; rv:11.0) like Gecko',
	];

	$random = mt_rand(0, count($userAgents)-1);

	return $userAgents[$random];
}

// Получить карточки процедур
function getCards($data)
{
	$doc = new DOMDocument();
	$doc->loadHTML($data['data']);
	$xPath = new DOMXpath($doc);

	$result = [];

	$q = $xPath->query('//td[@class="descriptTenderTd"]/dl/dt');	

	foreach ($q as $key => $item) {
		$result[] = $doc->saveHTML($item);
	}

	return $result;
}

//Получить почту
function getEmail($data)
{
	$doc = new DOMDocument();
	$doc->loadHTML($data['data']);
	$xPath = new DOMXpath($doc);

	$q = $xPath->query('//*[@id="tab-basic"]/table/tr[12]/td');	

	foreach ($q as $key => $item) {
		$email = $item->nodeValue;
	}

	return $email;
}
