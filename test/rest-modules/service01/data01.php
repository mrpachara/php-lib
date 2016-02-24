<?php
	if(!defined("RESTCONFIGURATED")){
		header(((isset($_SERVER['SERVER_PROTOCOL']))? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0')." 404 Not Found");
		exit;
	}

	$data = [
		'uri' => $GLOBALS['_rest']->getRestUri(),
	];

	if($GLOBALS['_rest']->getMethod() == 'GET'){
		if($GLOBALS['_rest']->getArgument(0) === null){
			$options = [
				'term' => (!empty($_GET['term']))? $_GET['term'] : null,
				'page' => (!empty($_GET['page']))? $_GET['page'] : null,
				'queries' => (!empty($_GET['queries']))? json_decode($_GET['queries'], true) : null,
			];
			$data['items'] = $_service->getAll($options);
			$config->addNewLinks($data, [
				[
					'href' => $GLOBALS['_rest']->getServicePath('{{item.id}}'),
					'rel' => 'action', 'alias' => 'view',
					'link' => "recruitItem({id: item.id})",
					'title' => 'View', 'icon' => 'action:ic-pageview',
				],
			]);
			if($GLOBALS['_grantservice']->authoz('EDUSERVICES_MANAGER')){
				$config->addNewLinks($data, [
					[
						'href' => $GLOBALS['_rest']->getServicePath($_service::NEWPARAM),
						'rel' => 'global-action', 'alias' => 'new',
						'link' => "recruitItem({id: '".$_service::NEWPARAM."'})",
						'title' => 'New', 'icon' => 'content:ic-add'
					],
				]);
			}
			if(is_array($options['page'])) $data['page'] = $options['page'];
		} else{
			if($GLOBALS['_rest']->getArgument(0) != $_service::NEWPARAM){
				$data['item'] = $_service->get($GLOBALS['_rest']->getArgument(0));
				if($GLOBALS['_grantservice']->authoz('EDUSERVICES_MANAGER')){
					if($data['item']['_updatable']) $config->addNewLinks($data, [
						[
							'href' => $GLOBALS['_rest']->getServicePath($GLOBALS['_rest']->getArgument(0)),
							'rel' => 'action', 'alias' => 'save', 'method' => 'put',
							'title' => 'Edit', 'icon' => 'editor:ic-mode-edit',
						],
					]);
					if($data['item']['_deletable']) $config->addNewLinks($data, [
						[
							'href' => $GLOBALS['_rest']->getServicePath($GLOBALS['_rest']->getArgument(0)),
							'rel' => 'action',  'alias' => 'delete', 'method' => 'delete',
							'title' => 'Delete', 'icon' => 'action:ic-delete', 'class' => 'warn',
						],
					]);
				}
			} else{
				$data['item'] = $_service->get(null);
				if($GLOBALS['_grantservice']->authoz('EDUSERVICES_MANAGER')){
					$config->addNewLinks($data, [
						[
							'href' => $GLOBALS['_rest']->getServicePath(),
							'rel' => 'action',  'alias' => 'save', 'method' => 'put',
							'title' => 'Create', 'icon' => 'content:ic-save',
						],
					]);
				}
			}

			$config->addNewLinks($data, [
				[
					'rel' => 'domain', 'href' => $config->getLinkModulePath('applicationtemplate').'applicationtemplate-domain', 'alias' => 'applicationtemplate-domain',
					'from' => 'applicationtemplate', 'for' => 'applicationtemplate',
				],
				[
					'rel' => 'domain', 'href' => $config->getLinkModulePath('recruitpayment').'recruitpayment-domain', 'alias' => 'recruitpayment-domain',
					'from' => 'recruitpayment', 'for' => 'recruitpayment',
				],
				[
					'rel' => 'domain', 'href' => $config->getLinkModulePath('recruitexamcard').'recruitexamcard-domain', 'alias' => 'recruitexamcard-domain',
					'from' => 'recruitexamcard', 'for' => 'recruitexamcard',
				],
				[
					'rel' => 'domain', 'href' => $config->getLinkModulePath('recruitapplicationverify').'recruitapplicationverify-domain', 'alias' => 'recruitapplicationverify-domain',
					'from' => 'recruitapplicationverify', 'for' => 'recruitapplicationverify',
				],
				[
					'rel' => 'domain', 'href' => $config->getLinkModulePath('curriculum').'curriculum-domain', 'alias' => 'curriculum-domain',
					'from' => 'curriculum', 'for' => 'recruitapplications.curriculum',
				],
				[
					'rel' => 'domain', 'href' => $config->getLinkModulePath('applicationquestionaire').'applicationquestionaire-domain', 'alias' => 'applicationquestionaire-domain',
					'from' => 'applicationquestionaire', 'for' => 'recruitapplications.recruitapplicationquestionaires.applicationquestionaire',
				],
			]);
		}
	} else{
		$GLOBALS['_grantservice']->authozExcp('EDUSERVICES_MANAGER');
		if($GLOBALS['_rest']->getMethod() == 'DELETE'){
			$data['id'] = $_service->delete($GLOBALS['_rest']->getArgument(0));
			$data['status'] = 'deleted';
			$data['info'] = $GLOBALS['_rest']->getServicePath($data['id'])." was deleted";
		} else{
			$item = $GLOBALS['_rest']->getContent();
			if($GLOBALS['_rest']->getArgument(0) === null){
				$data['id'] = $_service->save(null, $item, $GLOBALS['_grantservice']->getUsername());
				$data['status'] = 'created';
				$data['info'] = $GLOBALS['_rest']->getServicePath($data['id'])." was created";
			} else{
				$data['id'] = $_service->save($GLOBALS['_rest']->getArgument(0), $item, $GLOBALS['_grantservice']->getUsername());
				$data['status'] = 'updated';
				$data['info'] = $GLOBALS['_rest']->getServicePath($data['id'])." was updated";
			}
		}
	}
?>
