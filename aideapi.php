<?php

class AideException extends Exception {

	protected $data;

	function __construct($data) {
		$this->data = $data;
	}//end constructor

	function toString() {
		return var_export($data);
	}//end function toString

}//end class AideException

class AideAPI {

	protected $appkey;
	protected $host;

	/* Pass in an Application Key to use the API */
	function __construct($appkey) {
		if(!$appkey)
			throw new AideException(array('error' => 'Invalid Appkey'));
		$this->appkey = $appkey;
		$this->host = 'api.aiderss.com';
	}//end constructor

	/* Get the internal AideRSS ID of the feed for the given [url] */
	function feed_id($url) {
		if(!$url || !preg_match('/(^(http|https)?:?\/?\/?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(([0-9]{1,5})?\/.*)?$)/ix',$url))
			throw new AideException(array('error' => 'Feed URL is missing or invalid'));
		$data = json_decode($this->http_fetch('/feed_id?url='.urlencode($url)), true);
		if(!$data['feed_id'])
			throw new AideException($data);
		return $data;
	}//end function feed_id

	/*  Get the [num] most recent [level] posts from [feed_id] */
	function feed($feed_id, $level='all', $num=10) {
		if(is_array($feed_id)) $feed_id = $feed_id['feed_id'];
		if(!$feed_id || (int)$feed_id < 1)
			throw new AideException(array('error' => 'FeedID is missing or invalid'));
		$meta = $level == 'meta' ? '&meta' : '&level='.urlencode($level).'&num='.urlencode($num);
		$data = json_decode($this->http_fetch('/feed?feed_id='.urlencode($feed_id).$meta), true);
		if(!is_array($data))
			throw new AideException($data);
		return $data;
	}//end functin feed

	/* Get the top [num] posts (based on PostRank) for the past [period] for the given [feed_id] */
	function top_posts($feed_id, $period='year', $num=10) {
		if(is_array($feed_id)) $feed_id = $feed_id['feed_id'];
		if(!$feed_id || (int)$feed_id < 1)
			throw new AideException(array('error' => 'FeedID is missing or invalid'));
		$data = json_decode($this->http_fetch('/top_posts?feed_id='.urlencode($feed_id).'&period='.urlencode($period).'&num='.urlencode($num)), true);
		if(!is_array($data))
			throw new AideException($data);
		return $data;
	}//end function top_posts

	/* Get the stats for entries [url] with respect to feeds [feed_id] */
	function entry_stats($url, $feed_id=array()) {
		if(!function_exists('curl_init'))
			throw new AideException(array('error' => 'Entry Stats API requires cURL'));

		if(!is_array($url)) $url = array($url);
		$urls = '';
		foreach($url as $aurl)
			$urls .= '&url[]='.urlencode($aurl);

		if(!is_array($feed_id)) $feed_id = array($feed_id);
		$feed_ids = '';
		foreach($feed_id as $afeed_id)
			$feed_ids .= '&feed_id[]='.urlencode($afeed_id);

		$curl = curl_init('http://'.$this->host.'/entry_stats_r?format=json&appkey='.urlencode($this->appkey));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl, CURL_POST, TRUE);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 'fields[]=postrank&fields[]=postrank_color&fields[]=slash_comments&fields[]=digs&fields[]=delicious&fields[]=google'.$urls.$feed_ids);
		$data = json_decode(curl_exec($curl), true);
		curl_close($curl);

		if(!is_array($data))
			throw new AideException($data);
		return $data;
	}//end function entry_stats

	/* Get the PNG sparkline from [feed_id] for [level] */
	function sparkline($feed_id, $level='all') {
		if(is_array($feed_id)) $feed_id = $feed_id['feed_id'];
		if(!$feed_id || (int)$feed_id < 1)
			throw new AideException(array('error' => 'FeedID is missing or invalid'));
		return $this->http_fetch('/sparkline?feed_id='.urlencode($feed_id).'&level='.urlencode($level), 'png');
	}//end function sparkline

	/* Get the stats for [feed_id] */
	function feed_stats($feed_id) {
		if(is_array($feed_id)) $feed_id = $feed_id['feed_id'];
		if(!$feed_id || (int)$feed_id < 1)
			throw new AideException(array('error' => 'FeedID is missing or invalid'));
		$data = json_decode($this->http_fetch('/feed_stats?feed_id='.urlencode($feed_id)), true);
		if($data['error'])
			throw new AideException($data);
		return $data;
	}//end function feed_stats

	function http_fetch($url,$format='json') {
		$url = 'http://'.$this->host.$url.'&format='.$format.'&appkey='.urlencode($this->appkey);

		if(function_exists('curl_init')) {
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			$rtrn = curl_exec($curl);
			curl_close($curl);
		} else {
			$rtrn = file_get_contents($url);
		}//end if-else curl

		return $rtrn;
	}//end function http_fetch

}//end class AideAPI

?>
