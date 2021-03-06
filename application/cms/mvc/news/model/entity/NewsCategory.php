<?php
/**
 * Classes e objetos relacionados com a model da galeria de fotos
 * @package	cms.mvc.photo.model
 */

/**
 * Entidade de uma categoria
 * @author mauricio
 *
 */
final class NewsCategory {
	
	/**
	 * 
	 * @var integer
	 */
	private $idCategory;
	
	/**
	 * 
	 * @var string
	 */
	private $categoryName;
	
	/**
	 * Atribui um ID para uma categoria
	 * @param integer $id
	 * @throws UnexpectedValueException Quando o ID informado não é inteiro
	 */
	public function setIdCategory( $id ){
		$this->idCategory = (int) $id;
	}
	
	/**
	 * Atribui um nome de categoria
	 * @param string $name Nome da categoria
	 */
	public function setCategoryName( $name ){
		$this->categoryName = $name;
	}
	
	/**
	 * Retorna o ID da categoria
	 * @return integer
	 */
	public function getIdCategory(){
		return $this->idCategory;
	}
	
	/**
	 * Retorna o nome da categoria
	 * @return string
	 */
	public function getCategoryName(){
		return $this->categoryName;
	}

}
