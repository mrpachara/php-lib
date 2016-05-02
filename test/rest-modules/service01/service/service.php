<?php
	namespace rest\service01;

	class Data01Service Extends \sys\DataService{
		const TABLE = 'stub.data01';

		function __constructor($pdoconfigurated){
			parent::__constructor($pdoconfigurated);
		}

		protected function prepareEntity(&$data){
			if(empty($data)) return;

			if(!empty($data['data'])){
				$data['data'] = json_decode($data['data'], TRUE);
			}
		}

		protected function get($id, $sqlStatment){
			$data = false;

			if($id === null){
				$data = ['code' => 'Enter code', 'data' => '{}'];
			} else{
				$sqlStatement['sqls'][] = "(_table.id = :id)";
				$sqlStatement['params'][':id'] = $id;

				$whereSQL = (!empty($sqlStatement['sqls']))? 'WHERE '.implode(' AND ', $sqlStatement['sqls']) : '';

				$stmt = $this->getPdo()->prepare(sprintf("
					SELECT
						  *
					FROM %s AS _table
					%s
					FOR UPDATE OF _table
				", static::TABLE, $whereSQL));
				$stmt->execute($sqlStatement['params']);
				$data = $stmt->fetch(\PDO::FETCH_ASSOC);
				$stmt->closeCursor();
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

			$sqlPattern = sprintf("
				SELECT DISTINCT
					  _table.id AS id
					, _table.code AS code
					, _table.name AS name
					, _table.data AS data
				FROM %s AS _table
				{$whereSQL}
				%%s
			", static::TABLE);

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

		protected function save($existedData, $data){
			$id = (!empty($existedData['id']))? $existedData['id'] : null;

			if($id === null){
				$stmt = $this->getPdo()->prepare(sprintf("
					INSERT INTO %s (
						  code
						, name
						, data
					) VALUES (
						  :code
						, :name
						, :data
					)
				", static::TABLE));
				$stmt->execute(array_merge(
					  array_intersect_key($data, array_flip(['code', 'name']))
					, ['data' => json_encode($data['data'])]
				));
				$stmt->closeCursor();

				$id = $this->getPdo()->lastInsertId(static::TABLE.'_id_seq');
			} else{
				$stmt = $this->getPdo()->prepare(sprintf("
					UPDATE %s SET
						  code = :code
						, name = :name
						, data = :data
					WHERE id = :id
				", static::TABLE));
				$stmt->execute(array_merge(
					  array_intersect_key($data, array_flip(['code', 'name', 'data']))
					, ['id' => $id, 'data' => json_encode($data['data'])]
				));
				$stmt->closeCursor();
			}

			return $id;
		}

		protected function delete($existedData){
			$id = (!empty($existedData['id']))? $existedData['id'] : null;

			$stmt = $this->getPdo()->prepare(sprintf("
				DELETE FROM %s
				WHERE id = :id
			", static::TABLE));
			$stmt->execute([
				'id' => $id,
			]);
			$stmt->closeCursor();

			return $id;
		}
	}
?>
