<?php
	if(!defined("RESTCONFIGURATED")){
		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." 404 Not Found");
		exit;
	}

	$data = [
		'uri' => $GLOBALS['_rest']->getRestUri(),
		'links' => [],
	];

	$reqParams = $GLOBALS['_rest']->bind(['id']);
/*
=================================================
	Call directly to service
=================================================
*/
	if($reqParams['id'] === null){
		if($GLOBALS['_rest']->isMethod(['GET', 'POST'])){
			$options = ($GLOBALS['_rest']->isMethod('GET'))?
				$GLOBALS['_rest']->getQuery() : $GLOBALS['_rest']->getContent();

			$data['self'] = $_service->getAll($options);
			$data['name'] = ['Data01'];
			$data['search'] = 'term';

			$config->addLink($data, 'data01-item', [
				'rel' => 'resource/item', 'alias' => 'view',
			]);
			$config->addLink($data, 'data01-item', [
				'rel' => 'resource/item', 'alias' => 'preview',
			]);
			if($GLOBALS['_grantservice']->authoz('ADMIN')){
				$config->addLink($data, 'data01-new', [
					'rel' => 'resource', 'alias' => 'new',
				]);
			}
			//if(is_array($options['page'])) $data['page'] = $options['page'];
		} else if($GLOBALS['_rest']->isMethod('PUT')){
			$GLOBALS['_grantservice']->authozExcp('ADMIN');
			$data['uri'] = $GLOBALS['_rest']->getServicePath(
				$_service->save(null, $GLOBALS['_rest']->getContent(), $GLOBALS['_grantservice']->getUsername())
			);
			$data['replace'] = $GLOBALS['_rest']->getServicePath($config->prop('new-method'));
			$data['status'] = 'created';
			$data['info'] = $data['uri']." was created";
		} else{
			throw new \sys\HttpMethodNotAllowedException();
		}
/*
=================================================
	Call with new parameter
=================================================
*/
	} else if($reqParams['id'] === $config->prop('new-method')){
		if($GLOBALS['_rest']->isMethod('GET')){
			$data['self'] = $_service->get(null);
			$data['name'] = ['Data01', $config->prop('new-method')];
			if($GLOBALS['_grantservice']->authoz('ADMIN')){
				$config->addNewLinks($data, [
					[
						'href' => $GLOBALS['_rest']->getServicePath(),
						'rel' => 'action',  'alias' => 'save', 'method' => 'put',
						'title' => 'Create', 'icon' => 'content:ic-save',
					],
				]);
			}
		} else{
			throw new \sys\HttpMethodNotAllowedException();
		}
/*
=================================================
	Call with item id
=================================================
*/
	} else{
		if($GLOBALS['_rest']->isMethod('GET')){
			$data['self'] = $_service->get($reqParams['id']);
			$data['name'] = ['Data01', $data['self']['id']];
			if($GLOBALS['_grantservice']->authoz('ADMIN')){
				if($data['self']['_updatable']) $config->addNewLinks($data, [
					[
						'href' => $data['uri'],
						'rel' => 'resource', 'alias' => 'save', 'method' => 'put',
					],
				]);
				if($data['self']['_deletable']) $config->addNewLinks($data, [
					[
						'href' => $data['uri'],
						'rel' => 'resource',  'alias' => 'delete', 'method' => 'delete',
					],
				]);
			}
		} else if($GLOBALS['_rest']->isMethod('PUT')){
			$_service->save($reqParams['id'], $GLOBALS['_rest']->getContent(), $GLOBALS['_grantservice']->getUsername());
			$data['status'] = 'updated';
			$data['info'] = $data['uri']." was updated";
		} else if($GLOBALS['_rest']->isMethod('DELETE')){
			$_service->delete($reqParams['id']);
			$data['status'] = 'deleted';
			$data['info'] = $data['uri']." was deleted";
		} else{
			throw new \sys\HttpMethodNotAllowedException();
		}
	}
?>
