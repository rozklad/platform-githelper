<?php namespace Sanatorium\Githelper\Controllers\Frontend;

use Platform\Foundation\Controllers\Controller;

class PackagesController extends Controller {

	/**
	 * Return the main view.
	 *
	 * @return \Illuminate\View\View
	 */
	public function index()
	{
		return view('sanatorium/githelper::index');
	}

}
