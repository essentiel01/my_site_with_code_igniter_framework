<?php
/**
 * Controller de la page culture
 */
class Culture extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('posts_model');
		// $this->load->library('pagination');
		// $this->load->helper('url');
	}

	/**
	 * permet d'afficher tous les articles de la page
	 */
	public function index()
	{
		// on stocke dans $session l'url de la page lorsque aucun utilisateur n'est connecté. Cette valeur sera utilisée plus tard pour rediriger l'utilisateur vers cette même page après sa connexion.
		if (!isset($_SESSION['userData'])) {
			$_SESSION['urlRedirect'] = $_SERVER['PATH_INFO'];
		}

		// offset de la close limit
		$start = $this->uri->segment(2,0);

		//param^tres de la requête permettant de retourner le
		$queryParams1 = array(
			//clause inner join
			'join1' => 'categories',
			'on1' => 'categories.categoryId = posts.postCategory',
			'inner1' => 'inner',
			//clause where. le nom cette classe correspond u nom de la catégorie
			'where' => array('categoryName' => get_class()),
		);

		//configuration de la pagination avec bootstrap
		// url de base auquel va être ajouté le numero de la page
		$config['base_url'] =  base_url(strToLower(get_class()) . '/')  ;

		// nombre total de de ligne retourné par la requête
		$config['total_rows'] = $this->posts_model->countPosts('posts', $queryParams1);

		// nombre d'articles par page
		$config['per_page'] = 4;

		// pour signifier que c'est le deuxième segment de l'url qui correspond au numéro dela page
		$config['uri_segment'] = 2;

		$config['full_tag_open'] = '<nav aria-label="..."><ul class="pagination pagination-lg">';
		$config['full_tag_close'] = '</ul></nav>';

		$config['cur_tag_open'] = '<li class="page-item active"><span class ="page-link">';
		$config['cur_tag_close'] = '</span></li>';

		$config['first_link'] = FALSE;
		$config['last_link'] = FALSE;

		$config['num_tag_open'] = '<li class="page-item "><span class ="page-link">';
		$config['num_tag_close'] = '</span></li>';

		$config['prev_tag_open'] = '<li>';
		$config['prev_link'] = 'Précédent';
		$config['prev_tag_close'] = '</li>';

		$config['next_tag_open'] = '<li>';
		$config['next_link'] = 'Suivant';
		$config['next_tag_close'] = '</li>';

		// initialisation de la pagination
		$this->pagination->initialize($config);


		//les variables à transmettre à la vue
		$chronic = $this->posts_model->get_chronic($this->chronicQueryParams(array('categoryName' => get_class() )));
		$posts = $this->posts_model->getPosts($this->postQueryParams($config['per_page'], $start));
		$headerTitle = 'Rubrique ' . get_class();

		$data['culturePage'] = array(
			'headerTitle' => $headerTitle,
			'mainTitle'=> 'Nos dernières revues publiées sur la culture',
			'posts'=>  $posts->result(),
			'chronic'=> $chronic->result(),
			'slides' => $this->posts_model->getCarousel('carousel')->result(),
			'allPostsCount' => $this->postsArchiveCount(),
			'allChronicsCount' => $this->chronicsArchiveCount(),
		);


		// si un utilisateur est connecté on recupère tous les postId de sa liste de favoris
		if (isset($_SESSION['userData'])){
			// on ajoute à $data['culturePage'] les id de toutes les revues ajoutées aux favoris par un utilisateur donné. Ces id sont utilisés dans la page index de chaque catégorie pour identifier les revues favorites de l'utilisateur
			$userId = $_SESSION['userData']->userId;
			$favoritesList = $this->posts_model->getPostIdFromFavorites('posts_favorites', $this->postFavorisQuery($userId));

			$data['culturePage']['favoritesList'] = $favoritesList->result();
		}

		if (isset($_SESSION['userData'])){
			$this->load->view('templates/headerLogged', $data['culturePage']);
		} else {
			$this->load->view('templates/header', $data['culturePage']);
		}
		$this->load->view('templates/index', $data['culturePage']);
		$this->load->view('templates/footer');
	}


	/**
	 * retourne un tableau assoc représentant les paramètres de la requête sql permettant dafficher la liste des revues de presse sur chaque page. Cette function est utilisée comme argument de la function getPosts() exécutée dans la méthode index() de la présente class.
	 * @param  Int    $limit  le nombre de revues à afficher par page paginéé
	 * @param  Int    $offset offset de la clause limit
	 * @return Array         paramètres de la requête sql
	 */
	private function postQueryParams(Int $limit, Int $offset)
	{
		return array(
			'select' => 'postId, postTitle, postSlug, postAudio, countryName, categoryName, postDate, writerFirstName, writerLastName, writerAvatar',

			'from' => 'posts',

			'join1' => 'categories',
			'on1' => 'categories.categoryId = posts.postCategory',
			'inner1' => 'inner',

			'join2' => 'countries',
			'on2' => 'countries.countryId = posts.postCountry',
			'inner2' => 'inner',

			'join3' => 'writers',
			'on3' => 'writers.writerId = posts.postWriter',
			'inner3' => 'inner',

			// le nom de la catégorie correspond au nom de la class
			'where' => array('categoryName' => get_class()),

			'limit' =>  $limit,
			'offset' => $offset,

			'order' => 'postDate DESC'
		);

	}


	/**
	 * retourne un tableau associatif représentant les paramètres de la requête sql permettant de sélectionner toutes les revues de presse de la table posts ou toutes les chroniques de la table chronics
	 * @param  String $type le type de données à selectionner au singulier (soit post ou soit chronic)
	 * @return Array      paramètres de la requête sql
	 */
	private function archiveQueryParams(String $type)
	{
		return array(
			'select' => $type.'Id, ' . $type.'Title, ' . $type.'Slug, countryName, categoryName, ' . $type.'Date,  writerFirstName, writerLastName',

			'join1' => 'categories',
			'on1' => 'categories.categoryId =' . $type.'s.' . $type.'Category',
			'inner1' => 'inner',

			'join2' => 'countries',
			'on2' => 'countries.countryId =' . $type.'s.' . $type.'Country',
			'inner2' => 'inner',

			'join3' => 'writers',
			'on3' => 'writers.writerId =' . $type.'s.'. $type.'Writer',
			'inner3' => 'inner',

			'order' => $type.'Date  DESC'
		);
	}


	/**
	 * retourne un tableau associatif représentant les paramètres de la requête sql permettant de sélectionner la chronique à afficher sur la page index de chaque catégorie et sur la page chronicView. Cette function est utilisée comme argument de la function get_chronic() exécutée dans les méthodes index() et chronicView() de la présente classe
	 * @param  Array  $where tableau assoc représentant la valeur de la clé 'where'
	 * @return Array        paramètres de la requête sql
	 */
	private function chronicQueryParams(Array $where)
	{
		return array(
			'from' => 'chronics',

			'join1' => 'categories',
			'on1' => 'categories.categoryId = chronics.chronicCategory',
			'inner1' => 'inner',

			'join2' => 'countries',
			'on2' => 'countries.countryId = chronics.chronicCountry',
			'inner2' => 'inner',

			'join3' => 'writers',
			'on3' => 'writers.writerId = chronics.chronicWriter',
			'inner3' => 'inner',


			'where' => $where,

			'limit' => 1,

			'order' => 'chronicDate DESC'
		);
	}


	/**
	 * sélectionne toutes les revues de presse de la table posts
	 */
	 public function postsArchive()
	 {
		 $allPosts = $this->posts_model->getAllPosts($this->archiveQueryParams('post'), 'posts');

		 $data['postArchive'] = array(
			 'headerTitle' => 'Archives',
			 'mainTitle'=> 'Toutes nos revues de presse',
			 'allPosts' => $allPosts->result()
		 );

		 if (isset($_SESSION['userData'])){
			 $this->load->view('templates/headerLogged', $data['postArchive']);
		 } else {
			 $this->load->view('templates/header', $data['postArchive']);
		 }
		 $this->load->view('templates/postsArchiveView', $data['postArchive']);
		 $this->load->view('templates/footer');
	 }

	/**
	 * compte et retourne le nombre total de revues de presse dans la table posts. cette function est utilisée dans la méthode index() de la présente class pour renvoyer à la vue le nombre de revues
	 * @return Int nombre de revues de presse
	 */
	 public function postsArchiveCount():Int
	 {
		 return $this->posts_model->getAllPosts($this->archiveQueryParams('post'), 'posts')->num_rows();
	 }

	/**
	 * sélectionne toutes les chroniques de la table chronics
	 */
	public function chronicsArchive()
	{
		$allChronics = $this->posts_model->getAllPosts($this->archiveQueryParams('chronic'), 'chronics');

		$data['chronicArchive'] = array(
			'headerTitle' => 'Archives',
			'mainTitle'=> 'Toutes nos chroniques',
			'allChronics' => $allChronics->result()
		);

		if (isset($_SESSION['userData'])){
			$this->load->view('templates/headerLogged', $data['chronicArchive']);
		} else {
			$this->load->view('templates/header', $data['chronicArchive']);
		}
		$this->load->view('templates/chronicsArchiveView', $data['chronicArchive']);
		$this->load->view('templates/footer');
	}


	/**
	 * compte et retourne le nombre total de chroniques dans la table chronics. cette function est utilisée dans la méthode index() de la présente class pour renvoyer à la vue le nombre de chroniques
	 * @return Int nombre de chroniques
	 */
	public function chronicsArchiveCount():Int
	{
		return $this->posts_model->getAllPosts($this->archiveQueryParams('chronic'), 'chronics')->num_rows();
	}

	// les paramêtres de la requête sql permetant d'afficher un article tout seul
	private function singlePostQuery(int $id)
	{
		return array(
			'select' => 'postId, postTitle, postContent, postSource, countryName, categoryName, postDate, writerFirstName, writerLastName, writerAvatar',

			'from' => 'posts',

			'join1' => 'categories',
			'on1' => 'categories.categoryId = posts.postCategory',
			'inner1' => 'inner',

			'join2' => 'countries',
			'on2' => 'countries.countryId = posts.postCountry',
			'inner2' => 'inner',

			'join3' => 'writers',
			'on3' => 'writers.writerId = posts.postWriter',
			'inner3' => 'inner',

			'where' => array('postId' => $id),
		);
	}

	// les paramêtres de la requête sql permetant de sélectionner le postId dans la table posts_favorites
	private function postFavorisQuery(int $id)
	{
		return array(
			'select' => 'postId',
			'where' => array('userId' => $id)
		);
	}

	// les paramêtres de la requête sql permetant de sélectionner le postId dans la table posts_favorites
	private function chronicFavoriteQuery(int $id)
	{
		return array(
			'select' => 'chronicId',
			'where' => array('userId' => $id)
		);
	}

	/**
	 * affiche un article tout seul
	*/
	public function singleView()
	{
		// url de redirection apres login
		if (!isset($_SESSION['userData'])) {
			$_SESSION['urlRedirect'] = $_SERVER['PATH_INFO'];
		}

		// decoupe l'url en segment sur la base du séparateur '/'
		$uriSegment = explode('/', uri_string());

		// variables à transmettre à la vue
		$post = $this->posts_model->get_single_post($this->singlePostQuery($uriSegment[2]));
		$headerTitle = $uriSegment[3] . ' / Rubrique ' . get_class();

		$data['singleView'] = array(
			'headerTitle' => $headerTitle,
			'post'=> $post->result()
		);

		// si un tutilisateur est connectéon on recupère tous les postId de sa liste de favoris
		if (isset($_SESSION['userData'])){
			// on ajoute à $data['culturePage'] les id de toutes les revues ajoutées aux favoris par un utilisateur donné. Ces id sont utilisés dans la page singleView ajouter un petit coeur aux revues favorites de l'utilisateur
			$userId = $_SESSION['userData']->userId;
			$favoritesList = $this->posts_model->getPostIdFromFavorites('posts_favorites', $this->postFavorisQuery($userId));

			$data['singleView']['favoritesList'] = $favoritesList->result();
		}

		if (isset($_SESSION['userData'])) {
			$this->load->view('templates/headerLogged', $data['singleView']);
		} else {
			$this->load->view('templates/header', $data['singleView']);
		}
		$this->load->view('templates/singleView', $data['singleView']);
		$this->load->view('templates/footer');
	}

	/**
	 * affiche une chronique toute seule
	*/
	public function chronicView()
	{
		// url de redirection apres login
		if (!isset($_SESSION['userData'])) {
			$_SESSION['urlRedirect'] = $_SERVER['PATH_INFO'];
		}

		// decoupe l'url en segment sur la base du séparateur '/'
		$uriSegment = explode('/', uri_string());

		//variables à transmetre à la vue
		$chronic = $this->posts_model->get_chronic($this->chronicQueryParams(array('chronicId' => $uriSegment[2])));
		$headerTitle = $uriSegment[3] . ' / Rubrique ' . get_class();

		$data['chronic'] = array(
			'headerTitle' => $headerTitle,
			'chronic'=> $chronic->result()
		);

		// si un utilisateur est connecté  on recupère tous les chronicId de sa liste de favoris
		if (isset($_SESSION['userData'])){
			
			$userId = $_SESSION['userData']->userId;

			$favoritesList = $this->posts_model->getPostIdFromFavorites('chronics_favorites', 						   	$this->chronicFavoriteQuery($userId) );

			$data['chronic']['favoritesList'] = $favoritesList->result();
		}

		if (isset($_SESSION['userData'])) {
			$this->load->view('templates/headerLogged', $data['chronic']);
		} else {
			$this->load->view('templates/header', $data['chronic']);
		}
		$this->load->view('templates/chronicView', $data['chronic']);
		$this->load->view('templates/footer');
	}


}


 ?>
