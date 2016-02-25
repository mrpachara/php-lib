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

		public function props($rel, $prop){
			$deps = [];

			foreach($this->links($rel) as $link){
				if(isset($link[$prop])) $deps[] = $link[$prop];
			}

			return $deps;
		}

		public function link($uri){
			if(empty($this->config['links'])) return null;

			foreach($this->config['links'] as $link){
				if((isset($link['href']) && ($link['href'] == $uri)) || (isset($link['alias']) && ($link['alias'] == $uri))) return $link + ['rel' => null, 'href' => null];
			}

			return null;
		}

		public function links($rel){
			$links = [];

			if(empty($this->config['links'])) return $links;

			foreach($this->config['links'] as $link){
				if((isset($link['rel'])) && ($link['rel'] == $rel)) $links[] = $link + ['rel' => null, 'href' => null];
			}

			return $links;
		}

		protected function prepareData(&$data){
			if(!isset($data['links'])) $data['links'] = [];
			$data['links'] = (array)$data['links'];
		}

		public function getLinkModulePath($uri){
			$link = $this->link($uri);

			return static::getModulePath($link['href']);
		}

		protected function extendLink($link, $extend){
			if(is_callable($extend)){
				return $extend($link);
			} else{
				return array_merge($link, (array)$extend);
			}
		}

		public function addLink(&$data, $uri, $extend = null){
			$this->prepareData($data);

			$link = $this->link($uri);
			if($link !== null) $data['links'][] = $this->extendLink($link);

			return ($link !== null);
		}

		public function addLinks(&$data, $rel, $extend = null){
			$this->prepareData($data);

			$links = $this->links($rel);
			foreach($links as $index => $link) $links[$index] = $this->extendLink($link);

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
