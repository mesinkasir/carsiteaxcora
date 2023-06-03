<?php

class pluginTailwindStaticPages extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Static Pages',
			'homeLink'=>true
		);
	}

	// Method called on the settings of the plugin on the admin area
	public function form()
	{
		global $L;

		$html  = '<div class="alert alert-primary" role="alert">';
		$html .= $this->description();
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Label').'</label>';
		$html .= '<input id="jslabel" name="label" type="text" value="'.$this->getValue('label').'">';
		$html .= '<span class="tip">'.$L->get('This title is almost always used in the sidebar of the site').'</span>';
		$html .= '</div>';

		$html .= '<div>';
		$html .= '<label>'.$L->get('Home link').'</label>';
		$html .= '<select name="homeLink">';
		$html .= '<option value="true" '.($this->getValue('homeLink')===true?'selected':'').'>'.$L->get('Enabled').'</option>';
		$html .= '<option value="false" '.($this->getValue('homeLink')===false?'selected':'').'>'.$L->get('Disabled').'</option>';
		$html .= '</select>';
		$html .= '<span class="tip">'.$L->get('show-the-home-link-on-the-sidebar').'</span>';
		$html .= '</div>';

		return $html;
	}

	// Method called on the sidebar of the website
	public function siteSidebar()
	{
		global $L;
		global $url;
		global $site;
		global $pages;

		// HTML for sidebar
		$html  = '<div class="flex flex-col items-start">';

		// Print the label if not empty
		$label = $this->getValue('label');
		if (!empty($label)) {
			$html .= '<dt class="mt-4 font-semibold text-white"><h3 class="plugin-label">'.$label.'</h3></dt>';
		}


			$html .= '<dd class="mt-2 leading-7 text-gray-400">';
		// Show Home page link
		if ($this->getValue('homeLink')) {
			$html .= '<a href="' . $site->url() . '">' . $L->get('Home page') . '</a>';
			$html .= '</dd>';
		}

		// Show static pages
		$staticPages = buildStaticPages();
		foreach ($staticPages as $page) {
			if ($page->isParent()) {
				$html .= '<dd class="mt-2 leading-7 text-gray-400">';
			} else {
				$html .= '<dd class="mt-2 leading-7 text-gray-400">';
			}
			$html .= '<a href="' . $page->permalink() . '">' . $page->title() . '</a>';
			$html .= '</dd>';
		}
			$html .= '</dt>';
 		$html .= '</div>';

		return $html;
	}
}