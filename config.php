<?php
	namespace sys;

	class Config{
		public static function getModulePath($uri){
			return preg_replace('/\/[^\/]*$/', "", $uri).'/';
		}

		private $config;

		function __construct($file){
			include $file;
			$this->config = $data;
		}

		public function prop($prop){
			return (isset($this->config[$prop]))? $this->config[$prop] : null;
		}

		public function link($uri){
			if(empty($this->config['links'])) return null;

			foreach($this->config['links'] as $link){
				if((isset($link['href']) && ($link['href'] == $uri)) || (isset($link['alias']) && ($link['alias'] == $uri))) return $link + array('rel' => null, 'href' => null);
			}

			return null;
		}

		public function links($rel){
			$links = array();

			if(empty($this->config['links'])) return $links;

			foreach($this->config['links'] as $link){
				if((isset($link['rel'])) && ($link['rel'] == $rel)) $links[] = $link + array('rel' => null, 'href' => null);
			}

			return $links;
		}

		protected function prepareData(&$data){
			if(!isset($data['links'])) $data['links'] = array();
			$data['links'] = (array)$data['links'];
		}

		public function getLinkModulePath($uri){
			$link = $this->link($uri);

			return static::getModulePath($link['href']);
		}

		public function addLink(&$data, $uri, $extend = array()){
			$this->prepareData($data);

			$link = $this->link($uri);
			if($link !== null) $data['links'][] = array_merge($link, $extend);

			return ($link !== null);
		}

		public function addLinks(&$data, $rel, $extend = null){
			$this->prepareData($data);

			$links = array_merge($this->links($rel), (array)$extend);
			$data['links'] = array_merge($data['links'], $links);

			return (count($links) > 0);
		}

		public function addNewLinks(&$data, $links){
			$this->prepareData($data);

			$links = (array)$links;
			$data['links'] = array_merge($data['links'], $links);

			return true;
		}
	}
 ?>
