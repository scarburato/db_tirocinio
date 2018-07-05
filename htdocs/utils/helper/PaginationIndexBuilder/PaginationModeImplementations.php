<?php
/**
 * Created by PhpStorm.
 * User: dario
 * Date: 17/03/18
 * Time: 18.29
 */

namespace helper;

/**
 * Class IndexHTTP
 * @package helper
 * @see PaginationMode
 */
class IndexHTTP implements PaginationMode
{
	private $opt_param = [];

	private function build_query(int $page): string
	{
		$query = "";
		if(count($this->opt_param) > 0)
			$query = "&" . http_build_query($this->opt_param);

		return "?pagina=" . $page . $query;
	}

	/**
	 * Sono i paramatri GET da salvarsi!
	 * @param array $opt_param
	 */
	public function set_opt_param(array $opt_param)
	{
		unset($opt_param["pagina"]);
		$this->opt_param = $opt_param;
	}

	public function offset_page(Pagination $current, int $offset): Attributes
	{
		return new Attributes(
			[
				"href" => [$this->build_query($current->get_current_page() + $offset)]
			]
		);
	}

	public function last_page(Pagination $current): Attributes
	{
		return new Attributes(
			[
				"href" => [$this->build_query($current->get_max_page())]
			]
		);
	}

	public function first_page(Pagination $current): Attributes
	{
		return new Attributes(
			[
				"href" => [$this->build_query(0)]
			]
		);
	}

	public function next_page(Pagination $current): Attributes
	{
		return $this->offset_page($current, +1);
	}

	public function previus_page(Pagination $current): Attributes
	{
		return $this->offset_page($current, -1);

	}

	public function current_page(Pagination $current): Attributes
	{
		return $this->offset_page($current, 0);
	}
}

/**
 * Class IndexJS
 * @package helper
 * @see PaginationMode
 */
class IndexJS implements PaginationMode
{
	public function offset_page(Pagination $current, int $offset): Attributes
	{
		return new Attributes(
			[
				"class" => ["js-page-nav"],
				"data-page" => [$current->get_current_page() + $offset],
				"tabindex" => ["0"]
			]
		);
	}

	public function last_page(Pagination $current): Attributes
	{
		return new Attributes(
			[
				"class" => ["js-page-nav"],
				"data-page" => [$current->get_max_page()],
				"tabindex" => ["0"]
			]
		);
	}

	public function first_page(Pagination $current): Attributes
	{
		return new Attributes(
			[
				"class" => ["js-page-nav"],
				"data-page" => [0],
				"tabindex" => ["0"]
			]
		);
	}

	public function next_page(Pagination $current): Attributes
	{
		return $this->offset_page($current, +1);
	}

	public function current_page(Pagination $current): Attributes
	{
		return $this->offset_page($current, 0);
	}

	public function previus_page(Pagination $current): Attributes
	{
		return $this->offset_page($current, -1);
	}
}