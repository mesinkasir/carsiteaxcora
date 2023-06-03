<?php

class pluginTailwindTags extends Plugin {

	public function init()
	{
		$this->dbFields = array(
			'label'=>'Tags'
		);
	}

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

		return $html;
	}

	public function siteSidebar()
	{
		global $L;
		global $tags;
		global $url;

		$filter = $url->filters('tag');

		$html  = '<div class="flex-col items-start">';
		$html .= '<h3 class="mt-4 font-semibold text-white">'.$this->getValue('label').'</h3>';

		// By default the database of tags are alphanumeric sorted
		foreach( $tags->db as $key=>$fields ) {
			$html .= '<span class="mt-2 text-gray-400">';
			$html .= '<a href="'.DOMAIN_TAGS.$key.'">#';
			$html .= $fields['name'];
			$html .= '</a> ';
			$html .= '</span>&nbsp;';
		}

 		$html .= '</div>';

		return $html;
	}
}