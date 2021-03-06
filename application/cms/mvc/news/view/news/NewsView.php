<?php
/**
 * Classes e objetos que representam a View da aplicação
 * @package	cms.mvc.view
 */

require_once 'cms/mvc/news/view/AbstractNewsView.php';

/**
 * Implementação da View de galeria de fotos
 * @author	mauricio
 */
class NewsView extends AbstractNewsView {

	/**
	 *
	 * @var string
	 */
	protected $contentSubPage = 'news/default.html';

	/**
	 *
	 * @var New
	 */
	private $_new;

	/**
	 *
	 * @var array
	 */
	private $news;

	/**
	 *
	 * @var array
	 */
	private $categories;

	/**
	 *
	 * @var string
	 */
	private $uploadDirPath;

	/**
	 *
	 * @var string
	 */
	private $uploadDirURL;


	public function __construct(){
		parent::__construct();

		// define a aba que está sendo utilizada pelo usuário
		$this->setTab( 'news' );

		$resourceBundle = Application::getInstance()->getBundle();

		// atribui os diretórios de upload
		$this->uploadDirPath = str_replace( '//', '/', '/'.trim($resourceBundle->getString('NEWS_UPLOAD_DIR') , '/\\'));
		$this->uploadDirURL = trim($resourceBundle->getString('NEWS_UPLOAD_URL'), '/\\');
	}

	/**
	 * @see CrudView::setTemplateForEdit
	 * @throws LogicException Quando não haver nenhum registro para popular o formulário
	 */
	public function setTemplateForEdit(){
		$resourceBundle = Application::getInstance()->getBundle();

		if( !$this->_new ){
			throw new LogicException( 'É necessário informar uma notícia à ser editada.' );
		}

		$this->setContentSubPage( 'news/form.html' );
		$this->setFormTitle( $resourceBundle->getString( 'NEWS_FORM_NEWS_EDIT' ) );

		//Popula os campos do formulário do template
		$this->assign( 'idNews', $this->_new->getIdNews() );
		$this->assign( 'idCategory', $this->_new->getIdCategory() );
		$this->assign( 'newsTitle', $this->_new->getNewsTitle() );
		$this->assign( 'newsResume', $this->_new->getNewsResume() );
		$this->assign( 'newsText', $this->_new->getNewsText() );
		$this->assign( 'newsDate', $this->_new->getNewsDate() );

	}

	/**
	 * Atribui o template para upload múltiplo de imagens e vizualização das que já estão cadastradas.
	 * @throws LogicException Se não for informado um produto
	 */
	public function setTemplateForImages(){

		$resourceBundle = Application::getInstance()->getBundle();

		if( !$this->_new ){
			throw new LogicException( 'Nenhuma notícia foi informada.' );
		}

		$this->setContentSubPage( 'news/uploader.html' );

		// Verifica se este produto possuí alguma imagem
		$dirPath = $this->uploadDirPath .'/'. $this->_new->getIdNews() .'/';
		$dirUrl = $this->uploadDirURL .'/'. $this->_new->getIdNews() .'/';

		$this->assign( 'url', $dirUrl );
		$this->assign( 'idNews', $this->_new->getIdNews() );
		$this->assign( 'newsTitle', $this->_new->getNewsTitle() );

		//Pega todas as imagens da galeria e cria uma lista para mostrar no template
		foreach( glob( $dirPath."thumb/*.{png,jpg,jpeg,gif}", GLOB_BRACE ) as $file ){
			$files[] = basename( $file );
		}

		$this->assign( 'images', $files );

	}

	/**
	 * @see CrudView::setTemplateForCreate
	 * @throws LogicException Quando não haver nenhuma categoria 
	 */
	public function setTemplateForCreate(){

		if( !$this->categories ){
			throw new LogicException( 'Não há nenhuma categoria. Por favor, cadastre-as antes de adicionar uma notícia.' );
		}

		$resourceBundle = Application::getInstance()->getBundle();
		$this->setContentSubPage( 'news/form.html' );
		$this->setFormTitle( $resourceBundle->getString( 'NEWS_FORM_NEWS_CREATE' ) );
	}

	/**
	 * @see CrudView::setTemplateForDefault
	 */
	public function setTemplateForDefault(){
		$resourceBundle = Application::getInstance()->getBundle();

		$this->setContentSubPage( 'news/default.html' );

		//Monta a lista de registros a ser mostrada no template
		$this->assign( 'dataTable', $this->news );

	}

	public function setNew( NewsEntity $new ){
		$this->_new = $new;
	}

	public function setNews( $news ){
		$this->news = $news;
	}
	
	/**
	 * Atribui uma lista de objetos de categorias a view
	 * @param array $categories
	 */
	public function setCategories( $categories ){
		$this->categories = $categories;
		foreach( $this->categories as $category ){
			$options[ $category->getIdCategory() ] = $category->getCategoryName();
		}
		$this->assign( 'categoryOptions', $options );
	}

}
