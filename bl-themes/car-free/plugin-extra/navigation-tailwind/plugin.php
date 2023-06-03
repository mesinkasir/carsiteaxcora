<?php

class pluginTailwindNavigation extends Plugin {

	public function init()
	{
		// Fields and default values for the database of this plugin
		$this->dbFields = array(
			'label'=>'Navigation',
			'homeLink'=>true,
			'numberOfItems'=>5
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
		$html .= '<span class="tip">'.$L->get('Show the home link on the sidebar').'</span>';
		$html .= '</div>';

		if (ORDER_BY=='date') {
			$html .= '<div>';
			$html .= '<label>'.$L->get('Amount of items').'</label>';
			$html .= '<input id="jsnumberOfItems" name="numberOfItems" type="text" value="'.$this->getValue('numberOfItems').'">';
			$html .= '</div>';
		}

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
			$html .= '<dt class="mt-4 font-semibold text-white"><h2 class="plugin-label">'.$label.'</dt>';
		}


		// Show Home page link
		if ($this->getValue('homeLink')) {
			$html .= '<dd class="mt-2 leading-7 text-gray-400">';
			$html .= '<a href="' . $site->url() . '">' . $L->get('Home page') . '</a>';
			$html .= '</dd>';
		}

		// Pages order by position
		if (ORDER_BY=='position') {
			// Get parents
			$parents = buildParentPages();
			foreach ($parents as $parent) {
				$html .= '<dd class="mt-2 leading-7 text-gray-400">';
				$html .= '<a href="' . $parent->permalink() . '">' . $parent->title() . '</a>';

				if ($parent->hasChildren()) {
					// Get children
					$children = $parent->children();
					$html .= '<dt class="child">';
					foreach ($children as $child) {
						$html .= '<dd class="mt-2 leading-7 text-gray-400">';
						$html .= '<a class="child" href="' . $child->permalink() . '">' . $child->title() . '</a>';
						$html .= '</dd>';
					}
					$html .= '</dt>';
				}
			$html .= '</dd>';
			}
		}
		// Pages order by date
		else {
			// List of published pages
			$onlyPublished = true;
			$pageNumber = 1;
			$numberOfItems = $this->getValue('numberOfItems');
			$publishedPages = $pages->getList($pageNumber, $numberOfItems, $onlyPublished);

			foreach ($publishedPages as $pageKey) {
				try {
					$page = new Page($pageKey);
					$html .= '<dd class="mt-2 leading-7 text-gray-400">';
					$html .= '<a href="' . $page->permalink() . '">' . $page->title() . '</a>';
			$html .= '</dd>';
				} catch (Exception $e) {
					// Continue
				}
			}
		}

			$html .= '</dt>';
 		$html .= '</div>';

		return $html;
	}
}