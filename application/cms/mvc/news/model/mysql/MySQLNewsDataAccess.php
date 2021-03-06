<?php
/**
 * Pacote de classes e objetos das models da notícia de fotos
 * @package cms.mvc.photo.model
 */

require_once 'cms/mvc/news/model/NewsDataAccess.php';
require_once 'cms/mvc/news/model/entity/NewsEntity.php';

/**
 * @author mauricio
 *
 */
class MySQLNewsDataAccess implements NewsDataAccess {	
	
	/**
	 * @param integer $id
	 */
	public function getByID( $id ){
		$pdo = Registry::getInstance()->get( 'pdo' );
		$stm = $pdo->prepare( 'SELECT * FROM `cms_news` AS `u`
								INNER JOIN `cms_news_category` AS `c` ON `c`.`idCategory`=`u`.`idCategory`
								WHERE `u`.`idNews` = :id;' );
		$stm->bindParam( ':id' , $id, PDO::PARAM_INT );
		$stm->setFetchMode( PDO::FETCH_CLASS , 'NewsEntity' );
		$stm->execute();

		$news = $stm->fetch();

		$stm->closeCursor();

		if ( $news instanceof NewsEntity ) {
			return $news;
		} else {
			throw new RuntimeException( 'Nenhuma notícia encontrada com o ID fornecido.' );
		}
	}

	/**
	 * @see NewsEntityDataAccess::getGaleries
	 * @return array
	 */
	public function getAll(){
		$pdo = Registry::getInstance()->get( 'pdo' );

		$stm = $pdo->prepare( 'SELECT * FROM `cms_news` AS `u` INNER JOIN `cms_news_category` AS `c` ON `c`.`idCategory`=`u`.`idCategory` ' );
		$stm->setFetchMode( PDO::FETCH_CLASS , 'NewsEntity' );
		$stm->execute();

		return $stm->fetchAll();
	}

	/**
	 * @see NewsDataAccess::getByCategory
	 * @param integer $idCategory ID da categoria
	 * @return array
	 */
	public function getByCategory( $idCategory ){
		$pdo = Registry::getInstance()->get( 'pdo' );

		$stm = $pdo->prepare( 'SELECT * FROM `cms_news` AS `p` INNER JOIN `cms_news_category` AS `c` ON `c`.`idCategory`=`p`.`idCategory` WHERE `p`.`idCategory` = :id' );
		$stm->bindParam( ':id', $idCategory, PDO::PARAM_INT );
		$stm->setFetchMode( PDO::FETCH_CLASS , 'NewsEntity' );
		$stm->execute();

		return $stm->fetchAll();
	}

	/**
	 * @param NewsEntity $news Dados de uma notícia para ser salva
	 */
	public function save( NewsEntity $news ){
		$pdo = Registry::getInstance()->get( 'pdo' );

		// Verifica se todos os campos estão preenchidos
		if( !$news->getIdCategory() ){
			throw new RuntimeException( 'É necessário informar uma categoria para a notícia.' );
		}

		if( !$news->getNewsTitle() ){
			throw new RuntimeException( 'É necessário informar um título para a notícia.' );
		}

		if( !$news->getNewsText() ){
			throw new RuntimeException( 'É necessário informar o texto da notícia.' );
		}
		
		if( !$news->getNewsDate() ){
			throw new RuntimeException( 'É necessário informar uma data para a notícia.' );
		}

		if( !$news->getIdNews() ){ // adiciona
			$stm = $pdo->prepare( 'INSERT INTO `cms_news`(`idCategory`, `newsTitle`, `newsResume`, `newsDate`, `newsText`) 
			VALUES (:category,:title,:resume,:date,:text)' );
			$stm->bindParam( ':category' , $news->getIdCategory(), PDO::PARAM_INT );
			$stm->bindParam( ':title' , $news->getNewsTitle(), PDO::PARAM_STR );
			$stm->bindParam( ':resume' , $news->getNewsResume(), PDO::PARAM_STR );
			$stm->bindParam( ':date' , $news->getNewsDate(), PDO::PARAM_STR );
			$stm->bindParam( ':text' , $news->getNewsText(), PDO::PARAM_STR );
		}else{
			$stm = $pdo->prepare( 'UPDATE `cms_news`
			SET `idCategory`=:category,`newsTitle`=:title,`newsResume`=:resume,`newsDate`=:date,
			`newsText`=:text WHERE `idNews`=:id ' );
			$stm->bindParam( ':category' , $news->getIdCategory(), PDO::PARAM_INT );
			$stm->bindParam( ':title' , $news->getNewsTitle(), PDO::PARAM_STR );
			$stm->bindParam( ':resume' , $news->getNewsResume(), PDO::PARAM_STR );
			$stm->bindParam( ':date' , $news->getNewsDate(), PDO::PARAM_STR );
			$stm->bindParam( ':text' , $news->getNewsText(), PDO::PARAM_STR );
			$stm->bindParam( ':id' , $news->getIdNews(), PDO::PARAM_INT );
		}
		$stm->execute();

		if( !$news->getIdNews() ){
			$news->setIdNews( (int) $pdo->lastInsertId() );
		}

		return $news->getIdNews();

	}

	/**
	 * @param int $id ID da notícia a ser deletada
	 * @see NewsDataAccess::delete
	 */
	public function delete( $id ){
		$pdo = Registry::getInstance()->get( 'pdo' );
		$stm = $pdo->prepare( 'DELETE FROM `cms_news` WHERE `idNews` = :id' );
		$stm->bindParam( ':id' , $id, PDO::PARAM_INT );
		$stm->execute();
	}

}
