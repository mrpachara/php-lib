<?php
	namespace \rest\service01;

	class Data01 Extends \sys\DataService{
		static TNAME = 'stub.data01';

		function __constructor($pdoconfigurated){
			parent::__constructor($pdoconfigurated);
		}

		protected function prepareEntity(&$data){
			if(empty($data)) return;

			if(!empty($data['data'])){
				$data['data'] = json_decode($data, TRUE);
			}
		}

		protected function get($id, $sqlStatment){
			$data = false;

			if($id === null){
				$data = ['code' => 'Enter code', 'data': '{}'];
			} else{
				$sqlStatement['sqls'][] = "(_table.id = :id)";
				$sqlStatement['params'][':id'] = $id;

				$whereSQL = (!empty($sqlStatement['sqls']))? 'WHERE '.implode(' AND ', $sqlStatement['sqls']) : '';

				$stmt = $this->getPdo()->prepare("
					SELECT
						  *
					FROM {$this::TNAME} AS _table
					{$whereSQL}
					FOR UPDATE OF _table
				");
			}

			return $data;
		}

		protected function getAll($options, $sqlStatement){
			if(!empty($options['term'])){
				$searchTerm = $this->createSearchTerm($options['term']);
				$this->extendWhereSearchTerm($searchTerm, [
					'_table.code',
					'_table.name',
				], $sqlStatement);
				$this->extendWhereSearchSpecial($searchTerm, $sqlStatement);
			}
			if(!empty($options['queries'])) $this->extendWhereQuery($options['queries'], [
				'ids' => '_table.id',
				'codes' => '_table.code',
			], $sqlStatement);
			if(!empty($options['page'])) $this->extendLimit($options['page'], $sqlStatement);

			$whereSQL = (!empty($sqlStatement['sqls']))? 'WHERE '.implode(' AND ', $sqlStatement['sqls']) : '';

			$sqlPattern = "
				SELECT DISTINCT
					  _table.id AS id
					, _table.code AS code
					, _table.name AS name
					, _table.data AS data
				FROM {$this::TNAME} AS _table
				{$whereSQL}
				%s
			";

			if(!empty($options['page']['current'])){
				$stmt = $this->getPdo()->prepare(sprintf('
					SELECT count(_realdata.*) AS numrows FROM (%s) AS _realdata;
				', sprintf($sqlPattern, '')));
				$stmt->execute($sqlStatement['params']);

				$options['page']['total'] = ceil($stmt->fetchColumn() / $options['page']['limit']);
				$stmt->closeCursor();
				if($options['page']['total'] == 0) $options['page']['total'] = 1;
				if($options['page']['current'] >= $options['page']['total']){
					$options['page']['next'] = null;
				}
			}

			$stmt = $this->getPdo()->prepare(sprintf($sqlPattern, "ORDER BY _table.code ASC {$sqlStatement['limit']}"));
			$stmt->execute($sqlStatement['params']);
			$datas = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			$stmt->closeCursor();

			return $datas;
		}
	}
?>
